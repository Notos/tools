<?php

namespace App\Http\Controllers;

use App\Http\Requests\M3u as M3uRequest;
use App\Services\LameDb\M3u as M3uService;

class M3u extends Controller
{
    public function lamedb2m38(M3uRequest $request, M3uService $m3u)
    {
        $this->storeRequest('m3u', $request);

        return response()->download($m3u->lamedb2M3u($request), 'channels.m3u');
    }
}
