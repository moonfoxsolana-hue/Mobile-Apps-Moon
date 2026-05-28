<?php

namespace App\Http\Controllers;


use App\Services\TiktokService;
use Illuminate\Http\Request;


class TiktokController extends Controller
{
    protected $srv;
    public function __construct(TiktokService $srv)
    {
        $this->srv = $srv;
    }


    public function login()
    {
        return redirect($this->srv->buildAuthUrl());
    }


    public function callback(Request $request)
    {
        $code = $request->get('code');
        if (!$code) return response('Missing code', 400);


        try {
            $this->srv->exchangeCodeForToken($code);
            return response('Token tersimpan. Sukses!', 200);
        } catch (\Exception $e) {
            return response('Error: ' . $e->getMessage(), 500);
        }
    }
}
