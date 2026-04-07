<?php

namespace App\Services\Filters;

use Illuminate\Http\Request;

class DownloadFilters
{
    public function __construct(
        public readonly ?string $search = null,
        public readonly ?string $category = null,
        public readonly ?string $fileType = null,
        public readonly ?string $brand = null,
        public readonly bool $featured = false,
        public readonly bool $public = false,
        public readonly string $sortBy = 'sort_order',
        public readonly string $sortDirection = 'asc',
        public readonly int $perPage = 15,
    ) {}

    public static function fromRequest(Request $request, int $defaultPerPage = 15): self
    {
        return self::fromArray($request->all(), $defaultPerPage);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data, int $defaultPerPage = 15): self
    {
        return new self(
            search: self::nullableString($data['search'] ?? null),
            category: self::nullableString($data['category'] ?? null),
            fileType: self::nullableString($data['file_type'] ?? null),
            brand: self::nullableString($data['brand'] ?? null),
            featured: filter_var($data['featured'] ?? false, FILTER_VALIDATE_BOOLEAN),
            public: filter_var($data['public'] ?? false, FILTER_VALIDATE_BOOLEAN),
            sortBy: self::nullableString($data['sort_by'] ?? null) ?? 'sort_order',
            sortDirection: strtolower(self::nullableString($data['sort_direction'] ?? null) ?? 'asc') === 'desc' ? 'desc' : 'asc',
            perPage: max(1, (int) ($data['per_page'] ?? $defaultPerPage)),
        );
    }

    private static function nullableString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
