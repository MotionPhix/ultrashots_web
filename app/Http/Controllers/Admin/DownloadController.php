<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\BulkUpdateDownloadsRequest;
use App\Http\Requests\ReorderDownloadsRequest;
use App\Http\Requests\UpsertDownloadRequest;
use App\Models\Download;
use App\Services\DownloadService;
use App\Services\Filters\DownloadFilters;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DownloadController extends Controller
{
    public function __construct(
        private DownloadService $downloadService,
    ) {}

    public function index(Request $request): Response
    {
        $downloads = $this->downloadService->getAdminDownloads(DownloadFilters::fromRequest($request))
            ->through(fn (Download $download): array => $this->downloadPayload($download));

        return Inertia::render('admin/downloads/Index', [
            'downloads' => $downloads,
            'filters' => $request->only(['search', 'category', 'file_type', 'brand', 'sort_by', 'sort_direction']),
            'stats' => $this->downloadService->getDownloadStats(),
            'categories' => $this->downloadService->getCategories()->values(),
            'file_types' => $this->downloadService->getFileTypes()->values(),
            'brands' => $this->downloadService->getBrands()->values(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/downloads/Form', [
            'download' => null,
        ]);
    }

    public function store(UpsertDownloadRequest $request): RedirectResponse
    {
        $download = $this->downloadService->createDownload($request->safe()->except(['poster_image', 'download_file']));

        $this->downloadService->handleFileUploads(
            $download,
            $request->file('poster_image'),
            $request->file('download_file'),
        );

        return redirect()
            ->route('admin.downloads.show', $download)
            ->with('success', 'Download created successfully.');
    }

    public function show(Download $download): Response
    {
        return Inertia::render('admin/downloads/Show', [
            'download' => $this->downloadPayload($this->downloadService->getDownload($download, ['media']), includeMedia: true),
        ]);
    }

    public function edit(Download $download): Response
    {
        return Inertia::render('admin/downloads/Form', [
            'download' => $this->downloadPayload($this->downloadService->getDownload($download, ['media']), includeMedia: true),
        ]);
    }

    public function update(UpsertDownloadRequest $request, Download $download): RedirectResponse
    {
        $this->downloadService->updateDownload($download, $request->safe()->except(['poster_image', 'download_file']));

        $this->downloadService->handleFileUploads(
            $download,
            $request->file('poster_image'),
            $request->file('download_file'),
        );

        return redirect()
            ->route('admin.downloads.show', $download)
            ->with('success', 'Download updated successfully.');
    }

    public function destroy(Download $download): RedirectResponse
    {
        $this->downloadService->deleteDownload($download);

        return redirect()
            ->route('admin.downloads.index')
            ->with('success', 'Download deleted successfully.');
    }

    public function bulkUpdate(BulkUpdateDownloadsRequest $request): JsonResponse
    {
        $count = $this->downloadService->bulkUpdateDownloads(
            $request->validated('download_ids'),
            collect($request->validated('changes'))
                ->reject(fn ($value) => $value === '')
                ->all(),
        );

        return response()->json([
            'success' => true,
            'updated' => $count,
        ]);
    }

    public function reorder(ReorderDownloadsRequest $request): JsonResponse
    {
        $this->downloadService->reorderDownloads($request->validated('downloads'));

        return response()->json([
            'success' => true,
        ]);
    }

    public function stats(): JsonResponse
    {
        return response()->json($this->downloadService->getDownloadStats());
    }

    public function export(): StreamedResponse
    {
        $fileName = 'downloads-export-'.now()->format('Ymd-His').'.csv';
        $downloads = Download::query()->ordered()->get();

        return response()->streamDownload(function () use ($downloads): void {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, ['UUID', 'Title', 'Brand', 'Category', 'File Type', 'Download Count', 'Public', 'Featured', 'Published At']);

            foreach ($downloads as $download) {
                fputcsv($handle, [
                    $download->uuid,
                    $download->title,
                    $download->brand,
                    $download->category,
                    $download->file_type,
                    $download->download_count,
                    $download->is_public ? 'yes' : 'no',
                    $download->is_featured ? 'yes' : 'no',
                    $download->published_at?->toDateTimeString(),
                ]);
            }

            fclose($handle);
        }, $fileName, [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function duplicate(Download $download): RedirectResponse
    {
        $duplicate = $this->downloadService->duplicateDownload($download);

        return redirect()
            ->route('admin.downloads.edit', $duplicate)
            ->with('success', 'Download duplicated successfully.');
    }

    public function download(Download $download): BinaryFileResponse|RedirectResponse
    {
        $media = $download->getFirstMedia('file');

        if ($media === null) {
            return redirect()
                ->route('admin.downloads.show', $download)
                ->with('error', 'No file is attached to this download.');
        }

        return response()->download($media->getPath(), $media->file_name);
    }

    private function downloadPayload(Download $download, bool $includeMedia = false): array
    {
        $fileMedia = $download->getFirstMedia('file');
        $posterMedia = $download->getFirstMedia('poster');

        $payload = [
            'id' => $download->id,
            'uuid' => $download->uuid,
            'title' => $download->title,
            'brand' => $download->brand,
            'description' => $download->description,
            'category' => $download->category,
            'file_type' => $download->file_type,
            'file_size' => $download->file_size,
            'formatted_file_size' => $download->formatted_file_size,
            'download_count' => $download->download_count,
            'is_featured' => $download->is_featured,
            'is_public' => $download->is_public,
            'sort_order' => $download->sort_order,
            'published_at' => $download->published_at?->format('Y-m-d\\TH:i'),
            'poster_url' => $download->poster_url,
            'has_file' => $download->has_file,
            'created_at' => $download->created_at?->toDateTimeString(),
            'updated_at' => $download->updated_at?->toDateTimeString(),
        ];

        if ($includeMedia) {
            $payload['file_name'] = $fileMedia?->file_name;
            $payload['poster_name'] = $posterMedia?->file_name;
        }

        return $payload;
    }
}
