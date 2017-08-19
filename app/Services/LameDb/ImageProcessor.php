<?php

namespace App\Services\LameDb;

use File;
use Storage;

class ImageProcessor
{
    private function changeExtension($fileNames, $toExtension, $fromExtension = '.png')
    {
        return collect($fileNames)->map(function($file) use ($fromExtension, $toExtension) {
            return str_replace($fromExtension, $toExtension, $file);
        })->toArray() ;
    }

    private function cleanFolder($directory)
    {
        if (file_exists($directory)) {
            File::cleanDirectory($directory);
        } else {
            File::makeDirectory($directory);
        }

        return $directory;
    }

    public function convertImages()
    {
        $this->convertAllImages(
            ($basePath = '/Users/antoniocarlos/code/notos/picons') . '/5-selected',
            $this->cleanFolder($basePath.'/6-jpeg'),
            $this->cleanFolder($basePath.'/7-enigma2'),
            $this->cleanFolder($basePath.'/8-shield')
        );
    }

    /**
     * @param $selectedPath
     * @param $enigma2Path
     * @param $shieldPath
     * @internal param $path
     */
    protected function convertAllImages($selectedPath, $jpegPath, $enigma2Path, $shieldPath)
    {
        $lameDb = LameDb::factoryFromFile(database_path('lamedb'));

        $found = 0;

        $notFound = 0;

        collect($lameDb->getServices())->each(function ($service) use ($selectedPath, $jpegPath, $enigma2Path, $shieldPath, &$found, &$notFound) {
            if ($service->name) {
                $this->copyFiles($service, $selectedPath, $jpegPath, $enigma2Path, $shieldPath, $found, $notFound);
            }
        });

        echo "<br>";

        echo "Found: {$found}<br>";
        echo "Not Found: {$notFound}<br>";
    }

    private function copyAll($sourceFilenames, $destinationFilenames, $sourcePath, $destinationPath)
    {
        $copied = false;

        foreach ($sourceFilenames as $source) {
            foreach ($destinationFilenames as $destination) {
                if (file_exists($source = $sourcePath.'/'.$source)) {
                    copy($source, "{$destinationPath}/{$destination}");

                    $copied = true;
                }
            }
        }

        return $copied;
    }

    /**
     * @param $service
     * @param $selectedPath
     * @param $enigma2Path
     * @param $shieldPath
     * @param $found
     * @param $notFound
     * @return array
     */
    function copyFiles($service, $selectedPath, $jpegPath, $enigma2Path, $shieldPath, &$found, &$notFound)
    {
        $jpegFiles = $this->changeExtension($service->filenames, '.jpeg');

        $copied = false;

        $copied = $this->copyAll($service->filenames, $service->enigma2Filenames, $selectedPath, $enigma2Path) || $copied;

        $copied = $this->copyAll($service->filenames, $service->shieldFilenames, $selectedPath, $shieldPath)  || $copied;

        $copied = $this->copyAll($service->filenames, $jpegFiles, $selectedPath, $jpegPath) || $copied;

        if ($copied) {
            $found++;
        } else {
            foreach ($service->filenames as $file) {
                echo "Not found: {$service->name} - {$file}<br>";
            }

            echo "<br>";

            $notFound++;
        }

        return [$found, $notFound];
    }
}
