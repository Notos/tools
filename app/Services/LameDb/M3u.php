<?php

namespace App\Services\LameDb;

use App\Services\Constants;
use Illuminate\Support\Facades\Storage;

class M3u
{
    protected $disk = 'm3u';

    public function getCsvGenerator()
    {
        return function ($service, &$file) {
            $file[] = "{$service->group},{$service->title},{$service->url},{$service->logo}";
        };
    }

    public function getM3uGenerator()
    {
        return function ($service, &$file) {
            $file[] = '';

            $file[] = "#EXTINF:-1 tvg-id=\"{$service->channel}\" tvg-name=\"{$service->title}\" tvg-logo=\"{$service->logo}\" group-title=\"{$service->group}\",{$service->title}";

            $file[] = "#EXTVLCOPT:program={$service->channel}";

            $file[] = '#EXTVLCOPT--http-reconnect=true';

            $file[] = "#EXTVLCOPT:meta-title={$service->title}";

            $file[] = "{$service->url}";
        };
    }

    private function exportToCsv($request)
    {
        return $this->export($request, ['Class,Name,URL,Image'], $this->getCsvGenerator());
    }

    public function export($request, $file, $exporter)
    {
        $host = $this->makeUrl(
            $request->get('lamedb_host'),
            $request->get('lamedb_username'),
            $request->get('lamedb_password')
        );

        $picons = Constants::PICONS[$request->get('picons')];

        $group = $request->get('lamedb_group');

        collect($this->lameDbFactory($request)->getServices())->filter(function ($service) {
            return trim($service->name) != '';
        })->map(function($service) use ($picons, $host, $group) {
            $service->channel = hexdec($service->lsid);

            $service->title = "{$service->channel} - {$service->name}";

            $service->logo = "{$picons['base_url']}/".reset($service->filenames);

            $service->url = "{$host}/{$service->channelId}:";

            $service->group = $group;

            return $service;
        })->sortBy(function ($service) {
            return $service->channel;
        })->each(function($service) use ($exporter, &$file) {
            $exporter($service, $file);
        });

        return implode("\n", $file);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return string
     */
    public function lamedb2M3u($request)
    {
        Storage::disk($this->disk)->put($file = 'channels.m3u', $this->exportToM3u($request));

        return storage_path("{$this->disk}/{$file}");
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return string
     */
    public function lamedb2csv($request)
    {
        Storage::disk($this->disk)->put($file = 'channels.m3u', $this->exportToCsv($request));

        return storage_path("{$this->disk}/{$file}");
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return string
     */
    private function exportToM3u($request)
    {
        return $this->export($request, ['#EXTM3U'], $this->getM3uGenerator());
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return LameDb
     */
    protected function lameDbFactory($request)
    {
        $file = $this->storeLameDb($request);

        return LameDb::factoryFromFile(storage_path("m3u/db/{$file}"));
    }

    /**
     * @param $request
     * @return string
     */
    protected function storeLameDb($request)
    {
        $request->lamedb_file->storeAs('db', $file = 'lamedb.txt', $this->disk);

        return $file;
    }

    public function stripAccents($stripAccents){
        return strtr(utf8_decode($stripAccents), utf8_decode('àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ'), 'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY');
    }

    public function makeUrl($url, $username, $password)
    {
        if (empty($username) || empty($password)) {
            return $url;
        }

        $url = parse_url($url);

        $port = isset($url['port']) && !empty($url['port'])
            ? ":{$url['port']}"
            : '';

        return "{$url['scheme']}://{$username}:{$password}@{$url['host']}{$port}";
    }
}
