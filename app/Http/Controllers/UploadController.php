<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Aws\S3\S3Client;

class UploadController extends Controller
{
    public function videoPutUrl(Request $request)
    {
        $request->validate([
            'fileName' => 'required|string',
            'contentType' => 'required|string',
            'folder' => 'sometimes|string',
        ]);

        $folder = $request->input('folder', 'videos');
        $fileName = trim($request->input('fileName'));
        $contentType = trim($request->input('contentType'));

        $key = rtrim($folder, '/') . '/' . now()->format('Y/m/d') . '/' . Str::uuid() . '-' . $fileName;

        $client = new S3Client([
            'version' => 'latest',
            'region' => env('DO_SPACES_REGION'),
            'endpoint' => env('DO_SPACES_ENDPOINT'),
            'credentials' => [
                'key' => env('DO_SPACES_KEY'),
                'secret' => env('DO_SPACES_SECRET'),
            ],
        ]);

        $cmd = $client->getCommand('PutObject', [
            'Bucket' => env('DO_SPACES_BUCKET'),
            'Key' => $key,
            'ContentType' => $contentType,
            'ACL' => 'public-read',
        ]);

        $presigned = $client->createPresignedRequest($cmd, '+15 minutes');
        $url = (string) $presigned->getUri();

        return response()->json([
            'key' => $key,
            'url' => $url,
        ]);
    }
}