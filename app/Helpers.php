<?php

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\HttpException;

if (!function_exists('assetToPath')) {
    function assetToPath(string $link, string $form)
    {
        $path = strstr($link, $form);
        return $path;
    }
}

if (!function_exists('blobConvert')) {
    function blobConvert($file)
    {
        $data = explode(';base64,', $file);
        if (!is_array($data) || !isset($data[1])) {
            return false;
        };
        $data = base64_decode($data[1]);
        if (!$data) {
            return false;
        }
        return $data;
    }
}

if (!function_exists('chunkUpload')) {
    function chunkUpload($directory, $data, $last = false, $file_name = false, $cancel = false)
    {
        $files = collect( Storage::disk('temp')->files($directory));
        $response = (object) [ 'status' => false, 'message' => ''];
        if ($cancel === true) {
            Storage::disk('temp')->deleteDirectory($directory);
            $response->status = false;
            $response->message = 'File upload has been canceled';
            return $response;
        }
        if ($files->count() > 1) {
            $size = 0;
            foreach ($files as $key => $filepath) {
                $file_size = (int)File::size(storage_path('app/temp/'.$filepath));
                $size += $file_size;
            }
            if ($size > 1024 * 1024 * 500) {
                Storage::disk('temp')->deleteDirectory($directory); 
                return abort(401,'your provided file is too large');
            }
        }

        $temp_name = 'temp' . (string)($files->count() + 1) . '.tmp';
        if ($last && $file_name) {
            if (!Storage::disk('temp')->exists($directory)) {
                Storage::put($file_name, $data);
                $response->status = true;
                $response->message = 'file upload complete';
            };
            foreach ($files as $key => $file_path) {
                $content = Storage::disk('temp')->get($file_path);
                
                Storage::append($file_name, $content, null);
            }
            Storage::append($file_name, $data, null);
            Storage::disk('temp')->deleteDirectory($directory); 
            $response->status = true;
            $response->message = 'file upload complete';
        } else {
            if (!Storage::disk('temp')->exists($directory)) {
                Storage::disk('temp')->makeDirectory($directory);
            }

            Storage::put('/temp//' . $directory . '/' . $temp_name, $data);
            $response->status = true;
            $response->message = 'chunk upload complete';
        }
        return $response;
    }
}
