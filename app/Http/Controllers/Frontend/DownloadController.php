<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\UploadDownloadFixRequest;
use App\Models\Download;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class DownloadController extends Controller
{
    public function index(Request $request): Response
    {
        $downloads = Download::query()
            ->public()
            ->with('media')
            ->when($request->filled('search'), fn ($query) => $query->search((string) $request->string('search')))
            ->when($request->filled('category'), fn ($query) => $query->byCategory((string) $request->string('category')))
            ->when($request->filled('file_type'), fn ($query) => $query->byFileType((string) $request->string('file_type')))
            ->ordered()
            ->get()
            ->map(fn (Download $download) => [
                'id' => $download->id,
                'uuid' => $download->uuid,
                'title' => $download->title,
                'brand' => $download->brand,
                'description' => $download->description,
                'category' => $download->category,
                'file_type' => $download->file_type,
                'download_count' => $download->download_count,
                'formatted_file_size' => $download->formatted_file_size,
                'poster_url' => $download->poster_url,
                'is_featured' => $download->is_featured,
                'has_file' => $download->has_file,
                'created_at' => $download->created_at?->format('M Y'),
            ])
            ->values();

        return Inertia::render('frontend/downloads/Index', [
            'downloads' => $downloads,
            'filters' => [
                'search' => $request->string('search')->toString(),
                'category' => $request->string('category')->toString(),
                'file_type' => $request->string('file_type')->toString(),
            ],
            'categories' => Download::query()
                ->public()
                ->whereNotNull('category')
                ->distinct()
                ->orderBy('category')
                ->pluck('category')
                ->values(),
            'fileTypes' => Download::query()
                ->public()
                ->whereNotNull('file_type')
                ->distinct()
                ->orderBy('file_type')
                ->pluck('file_type')
                ->values(),
            'stats' => [
                'count' => $downloads->count(),
                'featured' => $downloads->where('is_featured', true)->count(),
                'download_events' => (int) Download::public()->sum('download_count'),
            ],
        ]);
    }

    public function fixRequest(): RedirectResponse
    {
        return redirect()->to(route('public.downloads.index').'#fix-request');
    }

    public function uploadFile(UploadDownloadFixRequest $request): RedirectResponse
    {
        $file = $request->file('file');

        $path = $file->storeAs('fix-requests', $file->hashName(), 'local');

        Storage::disk('local')->put(
            Str::replaceLast('.'.$file->getClientOriginalExtension(), '.json', $path),
            json_encode([
                'name' => $request->string('name')->toString(),
                'email' => Str::lower($request->string('email')->toString()),
                'notes' => $request->string('notes')->toString(),
                'original_name' => $file->getClientOriginalName(),
                'stored_path' => $path,
                'submitted_at' => now()->toIso8601String(),
            ], JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR)
        );

        return redirect()->to(route('public.downloads.index').'#fix-request')
            ->with('success', 'Your file has been received. We will review the requested fixes and follow up by email.');
    }

    public function downloadFile(Download $download)
    {
        abort_unless($download->is_public, 404);

        $media = $download->getFirstMedia('file');

        abort_if($media === null, 404);

        $download->incrementDownloadCount();

        $baseName = Str::of(pathinfo($media->file_name, PATHINFO_FILENAME))
            ->ascii()
            ->replace(' ', '-')
            ->trim('-')
            ->value();

        if ($baseName === '') {
            $baseName = 'download-'.$download->uuid;
        }

        $extension = pathinfo($media->file_name, PATHINFO_EXTENSION);
        $downloadName = $extension !== '' ? $baseName.'.'.$extension : $baseName;

        return response()->download(
            $media->getPath(),
            $downloadName,
            ['Content-Type' => $media->mime_type ?? 'application/octet-stream']
        );
    }
}
