<?php

namespace App\Services;

class JsonService
{
    private function getPath($file)
    {
        return storage_path("data/{$file}.json");
    }

    public function read($file)
    {
        $path = $this->getPath($file);

        if (!file_exists($path)) {
            return [];
        }

        return json_decode(file_get_contents($path), true) ?? [];
    }

    public function write($file, $data)
    {
        $path = $this->getPath($file);

        file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT));
    }
}