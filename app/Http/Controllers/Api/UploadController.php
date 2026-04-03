<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UploadStoreRequest;
use App\Http\Resources\UploadResource;
use App\Models\Upload;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UploadController extends Controller
{
    public function store(UploadStoreRequest $request): JsonResponse
    {
        $file = $request->file('file');
        $path = $file->store('uploads', 'public');

        $upload = Upload::create([
            'token' => $this->generateUniqueToken(),
            'user_id' => $request->user()->id,
            'original_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
        ]);

        return (new UploadResource($upload))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Request $request, string $token): UploadResource
    {
        $upload = Upload::query()
            ->where('token', $token)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        return new UploadResource($upload);
    }

    private function generateUniqueToken(): string
    {
        do {
            $token = Str::lower(Str::random(20));
        } while (Upload::query()->where('token', $token)->exists());

        return $token;
    }
}
