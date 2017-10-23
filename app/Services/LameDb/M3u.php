<?php

namespace App\Services\LameDb;

use Illuminate\Support\Facades\Storage;

class M3u
{
    /**
     *
     */
    public function createAll()
    {
        $lameDb = LameDb::factoryFromFile(database_path('lamedb'));

        collect($lameDb->getServices())->each(function ($service, $key) {
            if (trim($service->name) != '') {
                Storage::disk('m3u')->put($this->makeFilename($service), $this->generateFile($service, $key));
            }
        });
    }

    private function generateFile($service, $key)
    {
        $contents = file_get_contents(__DIR__.'/m3u.stub');

        $search = [
            '{channel-name}',
            '{channel-number}',
            '{host}',
            '{channel-id}',
        ];

        $replace = [
            $service->name,
            hexdec($service->lsid),
            'http://172.17.0.114:8001',
            $service->channelId,
        ];

        return str_replace($search, $replace, $contents);
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

    public function stripAccents($stripAccents){
        return strtr(utf8_decode($stripAccents), utf8_decode('àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ'), 'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY');
    }
}
