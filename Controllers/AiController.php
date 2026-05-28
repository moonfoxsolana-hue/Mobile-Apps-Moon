<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use OpenAI;

class AiController extends Controller
{
    public function index()
    {
        return view('ai'); // Render view awal
    }

    public function process(Request $request)
    {
        $page = $request->input('page'); // login, pdp, cart, checkout
        $userMessage = $request->input('user_message');

        // Definisikan prompt berdasarkan halaman
        $prompts = [
            'login' => [
                'system' => "You are a concise customer service AI for e-commerce login page. Only handle login failures: explain common errors (e.g., wrong password, account not found), guide to reset password or register new account. Keep responses short, empathetic, and direct user to retry or proceed to shopping after fix. Do not discuss other topics.",
                'example_user' => "Login saya gagal, kata sandi salah."
            ],
            'pdp' => [
                'system' => "You are an efficient product explainer AI on e-commerce PDP. Provide brief, accurate explanations of product features, specs, reviews, or comparisons. Always suggest adding to cart or checking similar items to speed up shopping. Avoid off-topic responses; focus on guiding to cart.",
                'example_user' => "Jelaskan fitur dari smartphone ini."
            ],
            'cart' => [
                'system' => "You are a smart cart optimizer AI for e-commerce. Suggest the best discounts (e.g., apply codes, bundles), and recommend fastest or cheapest shipping based on user preference. Responses must be concise; calculate totals if possible, then urge to proceed to checkout. Only handle cart-related queries.",
                'example_user' => "Ada diskon apa di cart saya? Saya mau pengiriman tercepat."
            ],
            'checkout' => [
                'system' => "You are a quick checkout assistant AI for e-commerce. Guide through payment methods, address confirmation, and order summary. Handle issues like payment failures briefly. Keep it short and positive; end with order confirmation and next steps (e.g., tracking). No other topics allowed.",
                'example_user' => "Bagaimana cara checkout dengan kartu kredit?"
            ]
        ];

    //     if (!array_key_exists($page, $prompts)) {
    //         return back()->with('error', 'Halaman tidak valid.');
    //     }

    //     // Gunakan user message custom jika ada, fallback ke example
    //     $userContent = $userMessage ?: $prompts[$page]['example_user'];

    //     // Siapkan messages untuk OpenAI
    //     $messages = [
    //         ['role' => 'system', 'content' => $prompts[$page]['system']],
    //         ['role' => 'user', 'content' => $userContent]
    //     ];

    //     // Call OpenAI API
    //     $client = OpenAI::client(env('OPENAI_API_KEY')); // Simpan key di .env: OPENAI_API_KEY=your_key_here

    //     $response = $client->chat()->create([
    //         'model' => 'gpt-4o-mini', // Ganti dengan model yang Anda inginkan, e.g., gpt-4o
    //         'messages' => $messages,
    //     ]);

    //     $aiResponse = $response['choices'][0]['message']['content'];

    //     return view('dummy-ai', compact('aiResponse', 'page', 'userContent'));
     }
}