<?php

namespace App\Http\Controllers;

use App\Http\Requests\M3u as M3uRequest;
use App\Services\LameDb\M3u as M3uService;

class M3u extends Controller
{
    /**
     * @param $method
     * @param M3uRequest $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    protected function convert($method, M3uRequest $request)
    {
        $m3u = new M3uService;

        $this->storeRequest('lamedb2m3u', $request);

        return response()->download($m3u->{$method}($request), 'channels.m3u');
    }

    public function lamedb2m38(M3uRequest $request)
    {
        return $this->convert('lamedb2M3u', $request);
    }

    public function lamedb2csv(M3uRequest $request)
    {
        return $this->convert('lamedb2csv', $request);
    }
}
