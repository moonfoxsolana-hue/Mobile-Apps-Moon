<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\NgepetMatchController;


class NgepetController extends Controller
{
    public function index(NgepetMatchController $apiController)
    {
        // Panggil fungsi API langsung (tanpa HTTP Request)
        $response = $apiController->listOpenMatches();

        // Konversi ke array
        $data = $response->getData(true);

        // Ambil matches dari JSON
        $matches = $data['matches'] ?? [];

        return view('games.ngepet_online.index', compact('matches'));
    }
    public function match(NgepetMatchController $apiController, $id)
    {
        // Panggil fungsi API langsung (tanpa HTTP Request)
        $response = $apiController->show($id);

        // Konversi ke array
        $data = $response->getData(true);

        // Ambil matches dari JSON
        $match = $data['match'] ?? [];

        return view('games.ngepet_online.match', compact('match'));
    }
}
