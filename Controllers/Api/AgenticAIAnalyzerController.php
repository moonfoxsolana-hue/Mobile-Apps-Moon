<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AiAnalyzeDocumentService;
use App\Services\GeminiTTSServiceforGeneral;
use App\Models\AiAnalyzeDocument;
use Dom\Document;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image as ImageManager;


class AgenticAIAnalyzerController extends Controller
{
    protected AiAnalyzeDocumentService $aiAnalyzeDocumentService;

    public function __construct(AiAnalyzeDocumentService $aiAnalyzeDocumentService)
    {
        $this->aiAnalyzeDocumentService = $aiAnalyzeDocumentService;
    }

    public function analyzeDocument(Request $request)
    {
        set_time_limit(600);
        try {
            Log::info('Starting document analysis request');
            Log::info('Request Data: ', $request->all());
            $request->validate([
                'file' => 'required|file|mimes:pdf,doc,docx,txt,jpg,jpeg,png',
            ]);
            Log::info('Received file for analysis: ' . $request->file('file')->getClientOriginalName());
            try {
                // Store file to storage
                $file = $request->file('file');
                $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs('ai-documents', $fileName, 'public');
                Log::info('File stored at: ' . $filePath);
                // Save to database
                $document = AiAnalyzeDocument::create([
                    'file_path' => $filePath,
                    'file_name' => $file->getClientOriginalName(),
                    'status' => 'processing',
                ]);
                Log::info('Document record created with ID: ' . $document->id);
                // Send to AI service
                $aiResponse = $this->aiAnalyzeDocumentService->analyze(
                    Storage::path('public/' . $filePath)
                );
                // Update document with AI response
                $document->update([
                    'title' => $aiResponse['judul'] ?? null,
                    'content' => $aiResponse['isi'] ?? null,
                    'analyze_ai' => $aiResponse['analisa'] ?? null,
                    'status' => 'completed',
                ]);
                Log::info('Document updated with AI analysis for ID: ' . $document->id);
                $ai_audio_service = new GeminiTTSServiceforGeneral();
                $content = $document->analyze_ai;
                $contentForAudio = mb_substr(strip_tags($content), 0, 1000);
                $flattenedContent = preg_replace('/\s+/', ' ', $contentForAudio);

                $audioUrl = $ai_audio_service->generate($flattenedContent, $document->id);
                $document->update(['audio_path' => $audioUrl]);

                //Log::info('Generated audio for analysis with URL: ' . $audioUrl);
                return response()->json([
                    'status' => 'success',
                    'message' => 'File analyzed successfully',
                    'data' => [
                        'document_id' => $document->id,
                        'title' => $document->title,
                        'content' => $document->content,
                        'analysis' => $document->analyze_ai,
                        'audio_path' => $document->audio_path,
                    ],
                ]);
            } catch (\Exception $e) {
                Log::error('AI Analysis Error: ' . $e->getMessage());

                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to analyze file',
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('Validation Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid request',
            ], 400);
        }
    }
}
