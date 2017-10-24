<?php

namespace App\Http\Controllers;

use App\Services\Constants;
use Session;

class Tools extends Controller
{
    public function index()
    {
        $data = [
            'm3u_host' => Session::get('req-m3u.m3u_host'),

            'picons' => Constants::PICONS,
        ];

        return view('m3u.index', $data);
    }
}
