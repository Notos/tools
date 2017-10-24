<?php

namespace App\Services\LameDb;

use Illuminate\Support\Facades\Storage;

class M3u
{
    protected $disk = 'm3u';

    private function generateCsvFile($request)
    {
        $host = $request->get('m3u_host');

        $file = ['Class,Name,URL,Image'];

        collect($this->lameDbFactory($request)->getServices())->filter(function ($service) {
            return trim($service->name) != '';
        })->map(function($service) {
            $service->channel = hexdec($service->lsid);

            $service->title = "{$service->channel} - {$service->name}";

            return $service;
        })->sortBy(function ($service) {
            return $service->channel;
        })->each(function ($service) use ($request, &$file, $host) {
            $file[] = "IPTV,{$service->title},{$host}/{$service->channelId}:,";
        });

        return implode("\n", $file);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return string
     */
    public function lamedb2M3u($request)
    {
        Storage::disk($this->disk)->put($file = 'channels.m3u', $this->generateM3uFile($request));

        return storage_path("{$this->disk}/{$file}");
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return string
     */
    public function lamedb2csv($request)
    {
        Storage::disk($this->disk)->put($file = 'channels.m3u', $this->generateCsvFile($request));

        return storage_path("{$this->disk}/{$file}");
    }

    private function generateM3uFile($request)
    {
        $host = $request->get('m3u_host');

        $file = ['#EXTM3U'];

        collect($this->lameDbFactory($request)->getServices())->filter(function ($service) {
            return trim($service->name) != '';
        })->map(function($service) {
            $service->channel = hexdec($service->lsid);

            $service->title = "{$service->channel} - {$service->name}";

            return $service;
        })->sortBy(function ($service) {
            return $service->channel;
        })->each(function ($service) use ($request, &$file, $host) {
            $file[] = '';

            $file[] = "#EXTINF:-1,{$service->title}";

            $file[] = "#EXTVLCOPT:program={$service->channel}";

            $file[] = '#EXTVLCOPT--http-reconnect=true';

            $file[] = "#EXTVLCOPT:meta-title={$service->title}";

            $file[] = "{$host}/{$service->channelId}:";
        });

        return implode("\n", $file);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return LameDb
     */
    protected function lameDbFactory($request)
    {
        $file = $this->storeLameDbFile($request);

        return LameDb::factoryFromFile(storage_path("m3u/db/{$file}"));
    }

    private function makeFilename($service)
    {
        $name = trim(
            $this->stripAccents(
                strtolower(
                    str_replace([' ', '(', ')'], ['-', '', ''], $service->name)
                )
            )
        );

        return "{$name}.m3u";
    }

    /**
     * @param $request
     * @return string
     */
    protected function storeLameDbFile($request)
    {
        $request->lamedb->storeAs('db', $file = 'lamedb.txt', $this->disk);

        return $file;
    }

    public function stripAccents($stripAccents){
        return strtr(utf8_decode($stripAccents), utf8_decode('àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ'), 'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY');
    }
}
