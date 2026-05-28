<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AIPresentation;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Services\ImageGeneratorService;
use Illuminate\Support\Str;
use App\Models\AiGeneratedImage;
use PhpOffice\PhpPresentation\PhpPresentation;
use PhpOffice\PhpPresentation\IOFactory;
use PhpOffice\PhpPresentation\Style\Color;
use PhpOffice\PhpPresentation\Slide\Background\Color as BgColor;
use PhpOffice\PhpPresentation\Slide\Background\Image as BgImage;
use PhpOffice\PhpPresentation\Style\Bullet;
use PhpOffice\PhpPresentation\Style\Alignment as PhpAlignment;
use PhpOffice\PhpPresentation\Style\Border;


class PresentationController extends Controller
{
    // 1. Generate Outline dari AI
    public function generateOutline(Request $request)
    {
        $request->validate([
            'prompt' => 'required|string',
        ]);

        // Kirim ke ChatGPT menggunakan API OpenAI
        $client = new \GuzzleHttp\Client();
        $response = $client->post('https://api.groq.com/openai/v1/chat/completions', [
            'headers' => [
                'Authorization' => 'Bearer ' . env('AI_API_KEY'),
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'model' => 'openai/gpt-oss-120b',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $this->systemPrompt()
                    ],
                    [
                        'role' => 'user',
                        'content' => $request->prompt
                    ]
                ]
            ]
        ]);

        $response = json_decode($response->getBody()->getContents(), true);

        $json = json_decode($response['choices'][0]['message']['content'], true);
        if (!$json || !isset($json['title']) || !isset($json['slides'])) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid outline format from AI.'
            ], 500);
        }
        $bgimagePath = null;
        // Generate background image menggunakan AI Image Generator
        $promptvisual = "Create a visually appealing presentation cover image that represents the following title: " . $json['title'];
        $imageai = new ImageGeneratorService();
        $generatedUUID = Str::uuid();
        $imagePath = $imageai->generateAndSave(
            $promptvisual,
            '1:1',
            'digital-art',
            ['vibrant colors', 'high detail', 'attractive composition'],
            $generatedUUID->toString()
        );
        $mediaOption = $imagePath ? $imagePath : null;

        if ($mediaOption) {
            $path = $imagePath;
            $image = AiGeneratedImage::create([
                'user_id' => null,
                'task_id' => $generatedUUID,
                'prompt' => $promptvisual,
                'image_url' => $path,
                'status' => 'COMPLETED',
                'token_used' => '0',
            ]);
            $bgimagePath = $path;
        }

        $presentation = AIPresentation::create([
            'title' => $json['title'],
            'theme' => $json['theme'] ?? 'mystic-nusa-dark',
            'prompt' => $request->prompt,
            'outline_json' => $json,
            'bgimage_path' => $bgimagePath,
        ]);

        return response()->json([
            'success' => true,
            'presentation' => $presentation
        ]);
    }

    // 2. Generate File PPT
    public function generatePpt($id)
    {
        $pres = AIPresentation::findOrFail($id);
        $data = $pres->outline_json;

        // === BUILD PPT ===
        $pptPath = $this->buildPpt($data, $id, $pres->bgimage_path);

        $pres->update([
            'ppt_path' => $pptPath
        ]);

        return response()->json([
            'success' => true,
            'download_url' => asset($pptPath)
        ]);
    }

    private function systemPrompt()
    {
        return <<<EOT
You are an expert presentation architect.
Convert user input into a structured PPT outline.

OUTPUT ONLY VALID JSON:

{
  "title": "",
  "theme": "mystic-nusa-dark",
  "slides": [
    { "title": "", "subtitle": "", "points": ["",""] }
  ]
}

Rules:
- Min 5 slides.
- First slide = opening title.
- Last slide = closing.
- No markdown, no explanation, JSON only.
EOT;
    }


    // ==========================================
    //    PPT BUILDER USING PHPPRESENTATION
    // ==========================================

    private function buildPpt($json, $id, $bgimagePath = null)
    {
        $folder = 'document';
        Log::info("Building PPT for presentation ID {$id}");
        $filename = "presentation_{$id}.pptx";
        Log::info("PPT will be saved to {$folder}\\{$filename}");
        Log::info("Storage path: " . public_path("{$folder}\\{$filename}"));
        $path = public_path("{$folder}\\{$filename}");

        // === Folder check ===
        if (!file_exists(public_path($folder))) {
            mkdir(public_path($folder), 0777, true);
        }

        // === Init PHPPresentation ===
        $objPHPPresentation = new PhpPresentation();

        $objPHPPresentation->getDocumentProperties()
            ->setCreator("Mystic Nusa Agent")
            ->setTitle($json['title']);

        // Remove default slide
        $objPHPPresentation->removeSlideByIndex(0);

        foreach ($json['slides'] as $slideData) {
            $slide = $objPHPPresentation->createSlide();

            // === BACKGROUND IMAGE SUPPORT ===
            $slide->setBackground(
                $this->themeBackground($json['theme'], $bgimagePath)
            );

            // === OVERLAY SHAPE FOR SEMI-TRANSPARENT BACKGROUND ===
            $bgBox = $slide->createRichTextShape()
                ->setWidth(960)
                ->setHeight(720)
                ->setOffsetX(0)
                ->setOffsetY(0);
            $bgBox->getFill()
                ->setFillType(\PhpOffice\PhpPresentation\Style\Fill::FILL_SOLID)
                ->setStartColor(new Color('4D000000')); // semi-transparent black
            $bgBox->getBorder()->setLineStyle(\PhpOffice\PhpPresentation\Style\Border::LINE_NONE);

            // === ONE SHAPE FOR ALL TEXT (AUTO WRAP, CENTERED) ===
            $textShape = $slide->createRichTextShape()
                ->setWidth(900)
                ->setHeight(500)
                ->setOffsetX(40)
                ->setOffsetY(80);

            // === TITLE ===
            $pTitle = $textShape->createParagraph();
            $pTitle->getAlignment()
                ->setHorizontal(PhpAlignment::HORIZONTAL_CENTER);
            $runTitle = $pTitle->createTextRun($slideData['title']);
            $runTitle->getFont()->setSize(48)->setBold(true)->setColor(new Color('FFE0D4FF'));

            // Space after title
            $textShape->createBreak();
            $textShape->createBreak();

            // === BULLET LIST ===
            foreach ($slideData['points'] as $point) {
                $p = $textShape->createParagraph();
                $p->getBulletStyle()->setBulletType(\PhpOffice\PhpPresentation\Style\Bullet::TYPE_BULLET);
                $p->getAlignment()->setHorizontal(\PhpOffice\PhpPresentation\Style\Alignment::HORIZONTAL_CENTER);

                $run = $p->createTextRun($point);
                $run->getFont()->setSize(26)->setColor(new Color('FFFFFFFF'));
            }
        }

        // === SAVE FILE ===
        $writer = IOFactory::createWriter($objPHPPresentation, 'PowerPoint2007');
        $writer->save($path);

        return "{$folder}/{$filename}";
    }


    private function themeBackground($theme, $imagePath = null)
    {
        if ($imagePath && file_exists($imagePath)) {
            // Background pake gambar
            $background = new BgImage();
            $background->setPath($imagePath);
            return $background;
        }

        // Default: warna (fallback)
        $background = new BgColor();
        $background->setColor(new Color('FF0B071A'));
        return $background;
    }
}
