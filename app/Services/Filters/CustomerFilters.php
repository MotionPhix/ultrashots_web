<?php

namespace App\Services\Filters;

use Illuminate\Http\Request;

class CustomerFilters
{
    public function __construct(
        public readonly ?string $search = null,
        public readonly ?string $status = null,
        public readonly ?string $companyName = null,
        public readonly string $sortBy = 'created_at',
        public readonly string $sortDirection = 'desc',
        public readonly int $perPage = 15,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return self::fromArray($request->all());
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            search: self::nullableString($data['search'] ?? null),
            status: self::nullableString($data['status'] ?? null),
            companyName: self::nullableString($data['company_name'] ?? null),
            sortBy: self::nullableString($data['sort_by'] ?? null) ?? 'created_at',
            sortDirection: strtolower(self::nullableString($data['sort_direction'] ?? null) ?? 'desc') === 'asc' ? 'asc' : 'desc',
            perPage: max(1, (int) ($data['per_page'] ?? 15)),
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
