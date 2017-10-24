<?php

namespace App\Http\Controllers;

use App\Services\Constants;
use Session;

class Tools extends Controller
{
    public function index()
    {
        $data = [
            'lamedb_host' => Session::get('req-lamedb.lamedb_host'),

            'lamedb_username' => Session::get('req-lamedb.lamedb_username'),

            'lamedb_password' => Session::get('req-lamedb.lamedb_password'),

            'lamedb_group' => Session::get('req-lamedb.lamedb_password', 'IPTV'),

            'picons' => Constants::PICONS,

            'exporters' => Constants::EXPORTERS,
        ];

        return view('m3u.index', $data);
    }
}
