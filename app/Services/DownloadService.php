<?php

namespace App\Services;

use App\Models\Download;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class DownloadService
{
  /**
   * Get filtered and paginated downloads for public view.
   */
  public function getPublicDownloads(Request $request): LengthAwarePaginator
  {
    $query = Download::query()
      ->public()
      ->with('media');

    $this->applyFilters($query, $request);
    $this->applySorting($query, $request);

    return $query->paginate($request->get('per_page', 12));
  }

  /**
   * Get filtered and paginated downloads for admin dashboard.
   */
  public function getAdminDownloads(Request $request): LengthAwarePaginator
  {
    $query = Download::query()->with('media');

    $this->applyFilters($query, $request);
    $this->applySorting($query, $request);

    return $query->paginate($request->get('per_page', 15));
  }

  /**
   * Get a single download with relationships.
   */
  public function getDownload(Download $download, array $with = []): Download
  {
    if (!empty($with)) {
      $download->load($with);
    }

    return $download;
  }

  /**
   * Create a new download.
   */
  public function createDownload(array $data): Download
  {
    // Generate UUID if not provided
    if (empty($data['uuid'])) {
      $data['uuid'] = (string)Str::orderedUuid();
    }

    // Set default sort order
    if (!isset($data['sort_order'])) {
      $data['sort_order'] = Download::max('sort_order') + 1;
    }

    return Download::create($data);
  }

  /**
   * Update an existing download.
   */
  public function updateDownload(Download $download, array $data): Download
  {
    $download->update($data);
    return $download->fresh();
  }

  /**
   * Delete a download.
   */
  public function deleteDownload(Download $download): bool
  {
    // Clear all media before deleting
    $download->clearMediaCollections();
    return $download->delete();
  }

  /**
   * Duplicate a download.
   */
  public function duplicateDownload(Download $originalDownload): Download
  {
    DB::beginTransaction();

    try {
      // Create new download with duplicated data
      $duplicatedData = $originalDownload->toArray();

      // Remove unique fields and modify title
      unset($duplicatedData['id'], $duplicatedData['uuid'], $duplicatedData['created_at'], $duplicatedData['updated_at']);
      $duplicatedData['title'] = $duplicatedData['title'] . ' (Copy)';
      $duplicatedData['uuid'] = (string)Str::orderedUuid();
      $duplicatedData['sort_order'] = Download::max('sort_order') + 1;

      $newDownload = Download::create($duplicatedData);

      // Duplicate media files
      $posterMedia = $originalDownload->getFirstMedia('poster');
      if ($posterMedia) {
        $newDownload->addMediaFromUrl($posterMedia->getUrl())
          ->usingName($posterMedia->name)
          ->usingFileName($posterMedia->file_name)
          ->toMediaCollection('poster');
      }

      $fileMedia = $originalDownload->getFirstMedia('file');
      if ($fileMedia) {
        $newDownload->addMediaFromUrl($fileMedia->getUrl())
          ->usingName($fileMedia->name)
          ->usingFileName($fileMedia->file_name)
          ->toMediaCollection('file');
      }

      DB::commit();

      Log::info('Download duplicated successfully', [
        'original_id' => $originalDownload->id,
        'new_id' => $newDownload->id
      ]);

      return $newDownload;

    } catch (\Exception $e) {
      DB::rollBack();
      Log::error('Failed to duplicate download', [
        'original_id' => $originalDownload->id,
        'error' => $e->getMessage()
      ]);
      throw $e;
    }
  }

  /**
   * Handle file uploads for a download.
   */
  public function handleFileUploads(
    Download      $download,
    ?UploadedFile $posterFile = null,
    ?UploadedFile $downloadFile = null
  ): void
  {
    DB::transaction(function () use ($download, $posterFile, $downloadFile) {
      // Handle poster upload
      if ($posterFile) {
        $this->uploadPoster($download, $posterFile);
      }

      // Handle download file upload
      if ($downloadFile) {
        $this->uploadDownloadFile($download, $downloadFile);
      }
    });
  }

  /**
   * Upload poster image for a download.
   */
  public function uploadPoster(Download $download, UploadedFile $file): void
  {
    try {
      // Clear existing poster
      $download->clearMediaCollection('poster');

      // Add new poster
      $media = $download->addMediaFromRequest('poster_image')
        ->usingFileName($this->generateUniqueFileName($file))
        ->usingName($file->getClientOriginalName())
        ->toMediaCollection('poster');

      Log::info('Poster uploaded successfully', [
        'download_id' => $download->id,
        'media_id' => $media->id,
        'file_name' => $file->getClientOriginalName()
      ]);
    } catch (\Exception $e) {
      Log::error('Error uploading poster', [
        'download_id' => $download->id,
        'file_name' => $file->getClientOriginalName(),
        'error' => $e->getMessage()
      ]);
      throw $e;
    }
  }

  /**
   * Upload download file.
   */
  public function uploadDownloadFile(Download $download, UploadedFile $file): void
  {
    try {
      // Clear existing file
      $download->clearMediaCollection('file');

      // Add new file
      $media = $download->addMediaFromRequest('download_file')
        ->usingFileName($this->generateUniqueFileName($file))
        ->usingName($file->getClientOriginalName())
        ->toMediaCollection('file');

      // Update download metadata
      $download->update([
        'file_type' => $file->getClientOriginalExtension(),
        'file_size' => $media->size,
      ]);

      Log::info('Download file uploaded successfully', [
        'download_id' => $download->id,
        'media_id' => $media->id,
        'file_name' => $file->getClientOriginalName(),
        'file_size' =>  $media->size,
      ]);
    } catch (\Exception $e) {
      Log::error('Error uploading download file', [
        'download_id' => $download->id,
        'file_name' => $file->getClientOriginalName(),
        'error' => $e->getMessage()
      ]);
      throw $e;
    }
  }

  /**
   * Process download request and increment counter.
   */
  public function processDownload(Download $download): string
  {
    $media = $download->getFirstMedia('file');

    if (!$media) {
      throw new \Exception('Download file not found');
    }

    // Increment download count
    $download->incrementDownloadCount();

    Log::info('Download processed', [
      'download_id' => $download->id,
      'download_count' => $download->download_count,
      'file_name' => $media->file_name
    ]);

    return $media->getUrl();
  }

  /**
   * Get download statistics.
   */
  public function getDownloadStats(): array
  {
    return Download::getStats();
  }

  /**
   * Get distinct categories.
   */
  public function getCategories(bool $publicOnly = false)
  {
    $query = Download::distinct()->whereNotNull('category');

    if ($publicOnly) {
      $query->where('is_public', true);
    }

    return $query->pluck('category')->sort()->values();
  }

  /**
   * Get distinct file types.
   */
  public function getFileTypes(bool $publicOnly = false)
  {
    $query = Download::distinct()->whereNotNull('file_type');

    if ($publicOnly) {
      $query->where('is_public', true);
    }

    return $query->pluck('file_type')->sort()->values();
  }

  /**
   * Get distinct brands.
   */
  public function getBrands(bool $publicOnly = false)
  {
    $query = Download::distinct()->whereNotNull('brand');

    if ($publicOnly) {
      $query->where('is_public', true);
    }

    return $query->pluck('brand')->sort()->values();
  }

  /**
   * Get featured downloads.
   */
  public function getFeaturedDownloads(int $limit = 6): Collection
  {
    return Download::featured()
      ->public()
      ->with('media')
      ->ordered()
      ->limit($limit)
      ->get();
  }

  /**
   * Get popular downloads.
   */
  public function getPopularDownloads(int $limit = 6): Collection
  {
    return Download::public()
      ->with('media')
      ->popular()
      ->limit($limit)
      ->get();
  }

  /**
   * Get recent downloads.
   */
  public function getRecentDownloads(int $limit = 6): Collection
  {
    return Download::public()
      ->with('media')
      ->recent()
      ->limit($limit)
      ->get();
  }

  /**
   * Search downloads.
   */
  public function searchDownloads(string $query, bool $publicOnly = true): Collection
  {
    $downloadQuery = Download::search($query)->with('media');

    if ($publicOnly) {
      $downloadQuery->public();
    }

    return $downloadQuery->get();
  }

  /**
   * Reorder downloads.
   */
  public function reorderDownloads(array $downloads): bool
  {
    DB::beginTransaction();

    try {
      foreach ($downloads as $downloadData) {
        Download::where('id', $downloadData['id'])
          ->update(['sort_order' => $downloadData['sort_order']]);
      }

      DB::commit();

      Log::info('Downloads reordered successfully', [
        'count' => count($downloads)
      ]);

      return true;
    } catch (\Exception $e) {
      DB::rollBack();
      Log::error('Failed to reorder downloads', [
        'error' => $e->getMessage()
      ]);
      throw $e;
    }
  }

  /**
   * Bulk update downloads.
   */
  public function bulkUpdateDownloads(array $downloadIds, array $data): int
  {
    DB::beginTransaction();

    try {
      $count = Download::whereIn('id', $downloadIds)->update($data);

      DB::commit();

      Log::info('Bulk update completed', [
        'affected_count' => $count,
        'data' => $data
      ]);

      return $count;

    } catch (\Exception $e) {
      DB::rollBack();
      Log::error('Bulk update failed', [
        'ids' => $downloadIds,
        'data' => $data,
        'error' => $e->getMessage()
      ]);
      throw $e;
    }
  }

  /**
   * Bulk delete downloads.
   */
  public function bulkDeleteDownloads(array $downloadIds): int
  {
    DB::beginTransaction();

    try {
      $downloads = Download::whereIn('id', $downloadIds)->get();

      foreach ($downloads as $download) {
        $download->clearMediaCollections();
      }

      $count = Download::whereIn('id', $downloadIds)->delete();

      DB::commit();

      Log::info('Bulk delete completed', [
        'deleted_count' => $count
      ]);

      return $count;

    } catch (\Exception $e) {
      DB::rollBack();
      Log::error('Bulk delete failed', [
        'ids' => $downloadIds,
        'error' => $e->getMessage()
      ]);
      throw $e;
    }
  }

  /**
   * Export downloads data.
   */
  public function exportDownloads(string $format, array $filters = []): string
  {
    $query = Download::query()->with('media');

    // Apply filters if provided
    if (!empty($filters)) {
      $request = new Request($filters);
      $this->applyFilters($query, $request);
    }

    $downloads = $query->get();

    $data = $downloads->map(function ($download) {
      return [
        'ID' => $download->id,
        'UUID' => $download->uuid,
        'Title' => $download->title,
        'Description' => $download->description,
        'Brand' => $download->brand,
        'Category' => $download->category,
        'File Type' => $download->file_type,
        'File Size' => $download->formatted_file_size,
        'Download Count' => $download->download_count,
        'Is Featured' => $download->is_featured ? 'Yes' : 'No',
        'Is Public' => $download->is_public ? 'Yes' : 'No',
        'Sort Order' => $download->sort_order,
        'Created At' => $download->created_at->format('Y-m-d H:i:s'),
        'Updated At' => $download->updated_at->format('Y-m-d H:i:s'),
      ];
    })->toArray();

    $filename = 'downloads_export_' . now()->format('Y-m-d_H-i-s');
    $filePath = storage_path('app/temp/' . $filename);

    // Ensure temp directory exists
    if (!file_exists(dirname($filePath))) {
      mkdir(dirname($filePath), 0755, true);
    }

    switch ($format) {
      case 'csv':
        return $this->exportToCsv($data, $filePath . '.csv');
      case 'xlsx':
        return $this->exportToXlsx($data, $filePath . '.xlsx');
      case 'json':
        return $this->exportToJson($data, $filePath . '.json');
      default:
        throw new \Exception('Unsupported export format');
    }
  }

  /**
   * Export data to CSV format.
   */
  private function exportToCsv(array $data, string $filePath): string
  {
    $csv = Writer::createFromPath($filePath, 'w+');

    if (!empty($data)) {
      $csv->insertOne(array_keys($data[0])); // Headers
      $csv->insertAll($data);
    }

    return $filePath;
  }

  /**
   * Export data to XLSX format.
   */
  private function exportToXlsx(array $data, string $filePath): string
  {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    if (!empty($data)) {
      // Add headers
      $headers = array_keys($data[0]);
      $sheet->fromArray($headers, null, 'A1');

      // Add data
      $sheet->fromArray($data, null, 'A2');

      // Auto-size columns
      foreach (range('A', $sheet->getHighestColumn()) as $column) {
        $sheet->getColumnDimension($column)->setAutoSize(true);
      }
    }

    $writer = new Xlsx($spreadsheet);
    $writer->save($filePath);

    return $filePath;
  }

  /**
   * Export data to JSON format.
   */
  private function exportToJson(array $data, string $filePath): string
  {
    file_put_contents($filePath, json_encode($data, JSON_PRETTY_PRINT));
    return $filePath;
  }

  /**
   * Generate a unique filename to prevent conflicts.
   */
  private function generateUniqueFileName(UploadedFile $file): string
  {
    $extension = $file->getClientOriginalExtension();
    $name = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
    $timestamp = now()->format('Y-m-d_H-i-s');
    $random = Str::random(6);

    return Str::slug($name) . "_{$timestamp}_{$random}.{$extension}";
  }

  /**
   * Apply filters to the query.
   */
  private function applyFilters($query, Request $request): void
  {
    if ($request->filled('search')) {
      $query->search($request->search);
    }

    if ($request->filled('category')) {
      $query->byCategory($request->category);
    }

    if ($request->filled('file_type')) {
      $query->byFileType($request->file_type);
    }

    if ($request->filled('brand')) {
      $query->where('brand', $request->brand);
    }

    if ($request->filled('featured')) {
      $query->featured();
    }

    if ($request->filled('public')) {
      $query->public();
    }
  }

  /**
   * Apply sorting to the query.
   */
  private function applySorting($query, Request $request): void
  {
    $sortField = $request->get('sort_by', 'sort_order');
    $sortDirection = $request->get('sort_direction', 'asc');

    $allowedSortFields = [
      'title', 'brand', 'category', 'file_type', 'file_size',
      'download_count', 'sort_order', 'created_at', 'updated_at'
    ];

    if (in_array($sortField, $allowedSortFields)) {
      if ($sortField === 'sort_order') {
        $query->ordered();
      } else {
        $query->orderBy($sortField, $sortDirection);
      }
    } else {
      $query->ordered();
    }
  }
}
