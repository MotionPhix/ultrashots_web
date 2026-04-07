<?php

namespace App\Services\Filters;

use Illuminate\Http\Request;

class ProjectFilters
{
    public function __construct(
        public readonly ?string $search = null,
        public readonly ?string $status = null,
        public readonly ?string $productionType = null,
        public readonly ?string $category = null,
        public readonly ?string $year = null,
        public readonly ?string $priority = null,
        public readonly ?string $customerId = null,
        public readonly bool $featured = false,
        public readonly bool $public = false,
        public readonly ?string $startDate = null,
        public readonly ?string $endDate = null,
        public readonly string $sort = 'newest',
        public readonly string $sortBy = 'created_at',
        public readonly string $sortDirection = 'desc',
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
            status: self::nullableString($data['status'] ?? null),
            productionType: self::nullableString($data['production_type'] ?? null),
            category: self::nullableString($data['category'] ?? null),
            year: self::nullableString($data['year'] ?? null),
            priority: self::nullableString($data['priority'] ?? null),
            customerId: self::nullableString($data['customer_id'] ?? null),
            featured: filter_var($data['featured'] ?? false, FILTER_VALIDATE_BOOLEAN),
            public: filter_var($data['public'] ?? false, FILTER_VALIDATE_BOOLEAN),
            startDate: self::nullableString($data['start_date'] ?? null),
            endDate: self::nullableString($data['end_date'] ?? null),
            sort: self::nullableString($data['sort'] ?? null) ?? 'newest',
            sortBy: self::nullableString($data['sort_by'] ?? null) ?? 'created_at',
            sortDirection: strtolower(self::nullableString($data['sort_direction'] ?? null) ?? 'desc') === 'asc' ? 'asc' : 'desc',
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
