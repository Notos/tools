<?php

namespace App\Http\Controllers;

use Session;

class Tools extends Controller
{
    public function index()
    {
        $data = [
            'm3u_host' => Session::get('req-m3u.m3u_host')
        ];

        return view('m3u.index', $data);
    }
}
