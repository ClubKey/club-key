<?php

declare(strict_types=1);

namespace App\Support\DataTable;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

final class DataTable
{
    /** @var array<int, Closure|string> */
    private array $searchCallbacks = [];

    /** @var array<string, Closure> */
    private array $filterCallbacks = [];

    /** @var array<string, array{callback: Closure|string, default_direction: 'asc'|'desc'}> */
    private array $sortDefinitions = [];

    private ?string $defaultSortKey = null;

    /** @var 'asc'|'desc' */
    private string $defaultSortDirection = 'asc';

    private ?string $appliedSortKey = null;

    /** @var 'asc'|'desc' */
    private string $appliedSortDirection = 'asc';

    private string $appliedSearch = '';

    /** @var array<string, mixed> */
    private array $appliedFilters = [];

    private function __construct(private Builder $builder)
    {
    }

    public static function for(Builder $builder): self
    {
        return new self($builder);
    }

    /**
     * @param array<int, Closure|string> $columns
     */
    public function searchable(array $columns): self
    {
        $this->searchCallbacks = $columns;

        return $this;
    }

    public function filter(string $key, Closure $callback): self
    {
        $this->filterCallbacks[$key] = $callback;

        return $this;
    }

    /**
     * @param Closure|string $callback
     * @param 'asc'|'desc' $defaultDirection
     */
    public function sortable(string $key, Closure|string $callback, string $defaultDirection = 'asc'): self
    {
        $direction = strtolower($defaultDirection) === 'desc' ? 'desc' : 'asc';

        $this->sortDefinitions[$key] = [
            'callback' => $callback,
            'default_direction' => $direction,
        ];

        return $this;
    }

    /**
     * @param 'asc'|'desc' $direction
     */
    public function defaultSort(string $key, string $direction = 'asc'): self
    {
        $this->defaultSortKey = $key;
        $this->defaultSortDirection = strtolower($direction) === 'desc' ? 'desc' : 'asc';

        return $this;
    }

    public function apply(Request $request): self
    {
        $this->applySearch($request);
        $this->applyFilters($request);
        $this->applySort($request);

        return $this;
    }

    public function builder(): Builder
    {
        return $this->builder;
    }

    /**
     * @return array{search: string, sort: array{key: ?string, direction: 'asc'|'desc'}, filters: array<string, mixed>}
     */
    public function state(): array
    {
        return [
            'search' => $this->appliedSearch,
            'sort' => [
                'key' => $this->appliedSortKey,
                'direction' => $this->appliedSortDirection,
            ],
            'filters' => $this->appliedFilters,
        ];
    }

    /**
     * @return array<string, array{callback: Closure|string, default_direction: 'asc'|'desc'}>
     */
    public function sorts(): array
    {
        return $this->sortDefinitions;
    }

    /**
     * @return array<string, Closure>
     */
    public function filters(): array
    {
        return $this->filterCallbacks;
    }

    private function applySearch(Request $request): void
    {
        $search = (string) $request->query('search', '');
        $this->appliedSearch = $search;

        if ($search === '' || $this->searchCallbacks === []) {
            return;
        }

        $this->builder->where(function (Builder $query) use ($search): void {
            foreach ($this->searchCallbacks as $callback) {
                if ($callback instanceof Closure) {
                    $callback($query, $search);

                    continue;
                }

                $query->orWhere($callback, 'like', '%' . $search . '%');
            }
        });
    }

    private function applyFilters(Request $request): void
    {
        $filters = $request->query('filters', []);

        if (! is_array($filters)) {
            return;
        }

        foreach ($filters as $key => $value) {
            if (! array_key_exists($key, $this->filterCallbacks)) {
                continue;
            }

            if ($this->isEmptyFilterValue($value)) {
                continue;
            }

            $this->filterCallbacks[$key]($this->builder, $value);

            $this->appliedFilters[$key] = $this->normalizeFilterValue($value);
        }
    }

    private function applySort(Request $request): void
    {
        $sortKey = $request->query('sort');
        $sortKey = is_string($sortKey) ? $sortKey : null;

        if ($sortKey === null || ! array_key_exists($sortKey, $this->sortDefinitions)) {
            $sortKey = $this->defaultSortKey ?? array_key_first($this->sortDefinitions);
        }

        if ($sortKey === null) {
            $this->appliedSortKey = null;

            return;
        }

        $requestedDirection = $request->query('direction');
        $direction = $this->normalizeDirection($requestedDirection);

        if ($direction === null) {
            $direction = $this->sortDefinitions[$sortKey]['default_direction']
                ?? $this->defaultSortDirection;
        }

        $this->appliedSortKey = $sortKey;
        $this->appliedSortDirection = $direction;

        $callback = $this->sortDefinitions[$sortKey]['callback'];

        if ($callback instanceof Closure) {
            $callback($this->builder, $direction);

            return;
        }

        $this->builder->orderBy($callback, $direction);
    }

    private function normalizeDirection(mixed $direction): ?string
    {
        if (! is_string($direction)) {
            return null;
        }

        $direction = strtolower($direction);

        if (! in_array($direction, ['asc', 'desc'], true)) {
            return null;
        }

        return $direction;
    }

    private function isEmptyFilterValue(mixed $value): bool
    {
        if (is_array($value)) {
            $filtered = array_filter($value, static fn ($item) => $item !== null && $item !== '');

            return $filtered === [];
        }

        return $value === null || $value === '';
    }

    private function normalizeFilterValue(mixed $value): mixed
    {
        if (is_array($value)) {
            return array_values(Arr::where($value, static fn ($item) => $item !== null && $item !== ''));
        }

        return $value;
    }
}
