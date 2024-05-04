<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Requests\ApiPostRequest;
use Illuminate\Support\Facades\Storage;

class ApiController extends Controller
{
    // Set the base directory where files will be saved
    protected $baseDir = '/home/dade/rivet/workspace/laravel';  // Update as necessary

    // API URL from which to fetch the data
    protected $apiUrl = 'http://127.0.0.1:3000/generate';

    public function fetchAndStoreData(ApiPostRequest $request)
    {
        $postData = $request->validated();

        try {
            $response = Http::timeout(120)->post($this->apiUrl, $postData);
            $files = $response->json()['value'];

            foreach ($files as $file) {
                $this->saveToFile($file['path'], $file['content']);
            }

            return response()->json(['message' => 'Files saved successfully!']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error fetching data: ' . $e->getMessage()], 500);
        }
    }

    /*protected function saveToFile($filePath, $content)
    {
        $fullPath = $this->baseDir . '/' . $filePath;
        Storage::makeDirectory(dirname($fullPath));
        Storage::put($fullPath, $content);
    }*/

    protected function saveToFile($filePath, $content)
    {

        $fullPath = $this->baseDir . '/' . $filePath;

        if (!file_exists(dirname($fullPath))) {
            mkdir(dirname($fullPath), 0777, true);
        }

        file_put_contents($fullPath, $content);
    }
}
