<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ImageProxyController extends Controller
{
    public function __invoke(Request $request, string $encodedPath): StreamedResponse
    {
        $path = base64_decode(strtr($encodedPath, '-_', '+/'));

        if (! $path || ! Storage::disk('s3-private')->exists($path)) {
            abort(404);
        }

        $stream = Storage::disk('s3-private')->readStream($path);

        if ($stream === false) {
            abort(404);
        }

        $mimeType = Storage::disk('s3-private')->mimeType($path) ?? 'application/octet-stream';

        return response()->stream(function () use ($stream) {
            fpassthru($stream);
            if (is_resource($stream)) {
                fclose($stream);
            }
        }, 200, [
            'Content-Type' => $mimeType,
            'Cache-Control' => 'private, max-age=300',
        ]);
    }
}
