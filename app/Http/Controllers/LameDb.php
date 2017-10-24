<?php

namespace App\Http\Controllers;

use App\Http\Requests\M3u as M3uRequest;
use App\Services\LameDb\M3u as M3uService;

class LameDb extends Controller
{
    /**
     * @param M3uRequest $request
     * @param $exporter
     * @param $extension
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    protected function convert(M3uRequest $request, $exporter, $extension)
    {
        $m3u = new M3uService;

        $this->storeRequest('lamedb', $request);

        return response()->download($m3u->{$exporter}($request), "channels.{$extension}");
    }

    public function export(M3uRequest $request)
    {
        return $this->convert(
            $request,
            'lamedb2'.studly_case($request->get('exporter')),
            $request->get('exporter')
        );
    }
}
