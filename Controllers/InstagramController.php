<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InstagramController extends Controller
{
    public function handle(Request $request)
    {
        // --- 1. PROSES VERIFIKASI TOKEN (Permintaan GET) ---
        
        if ($request->isMethod('get')) {
            
            $mode = $request->query('hub_mode');
            $token = $request->query('hub_verify_token');
            $challenge = $request->query('hub_challenge');
            
            // Cek apakah semua parameter yang dibutuhkan ada
            if ($mode && $token) {
                
                // Cek apakah mode adalah 'subscribe' dan token cocok
                if ($mode === 'subscribe' && $token === env('INSTAGRAM_VERIFY_TOKEN')) {
                    
                    // Verifikasi berhasil! Kembalikan string 'hub_challenge'
                    return response($challenge, 200)
                        ->header('Content-Type', 'text/plain');
                        
                } else {
                    // Token tidak cocok atau mode salah
                    return response('Forbidden: Verify token mismatch or incorrect mode.', 403);
                }
            }
            
            // Jika request GET tanpa parameter hub, ini bukan request verifikasi Meta
            return response('Invalid Meta verification request.', 400);
        }
        
        // --- 2. PROSES PENERIMAAN DATA (Permintaan POST) ---
        
        if ($request->isMethod('post')) {
            // Setelah verifikasi, semua data event akan masuk ke sini via POST
            
            $data = $request->all();
            // Log data untuk debugging
            file_put_contents(storage_path('app/instagram-token.json'), json_encode($data));
            
            return "Login Berhasil! Token disimpan.";
            
            // Lakukan sesuatu dengan data (misalnya, kirim ke Job atau Event)
            
            // Penting: Meta mengharapkan respons HTTP 200 OK dalam waktu 20 detik.
            return response('EVENT_RECEIVED', 200);
        }

        return response('Method Not Allowed', 405);
    }
}