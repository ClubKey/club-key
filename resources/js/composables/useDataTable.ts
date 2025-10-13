import { router } from '@inertiajs/vue3';
import { watchDebounced } from '@vueuse/core';
import { computed, reactive, ref, watch, type Ref } from 'vue';

import type {
    DataTableFilterConfig,
    DataTableMeta,
    DataTableSortOption,
} from '@/types';

type FilterValue = string[] | string | null;

interface UseDataTableOptions {
    routeName: string;
    meta: Ref<DataTableMeta>;
    query?: Record<string, unknown>;
}

function sanitizeArrayValue(value: unknown): string[] {
    if (! Array.isArray(value)) {
        return [];
    }

    return value
        .map((item) => (item === null || item === undefined ? null : String(item)))
        .filter((item): item is string => item !== null && item !== '');
}

export function useDataTable(options: UseDataTableOptions) {
    const search = ref<string>('');
    const sortKey = ref<string | null>(null);
    const sortDirection = ref<'asc' | 'desc'>('asc');
    const filters = reactive<Record<string, FilterValue>>({});

    const filtersConfig = computed(() => options.meta.value.filters);
    const sorts = computed(() => options.meta.value.sorts);

    function syncFilters(): void {
        const nextState: Record<string, FilterValue> = {};
        const stateFilters = options.meta.value.state.filters ?? {};

        Object.entries(stateFilters).forEach(([key, value]) => {
            if (Array.isArray(value)) {
                nextState[key] = sanitizeArrayValue(value);

                return;
            }

            if (value === null || value === undefined) {
                nextState[key] = null;

                return;
            }

            nextState[key] = String(value);
        });

        Object.entries(filtersConfig.value).forEach(([key, config]) => {
            if (nextState[key] !== undefined) {
                return;
            }

            nextState[key] = config.type === 'multi' ? [] : null;
        });

        Object.keys(filters).forEach((key) => {
            if (! Object.prototype.hasOwnProperty.call(nextState, key)) {
                delete filters[key];
            }
        });

        Object.assign(filters, nextState);
    }

    watch(
        options.meta,
        (meta) => {
            search.value = typeof meta.state.search === 'string' ? meta.state.search : '';
            sortKey.value = meta.state.sort.key ?? null;
            sortDirection.value = meta.state.sort.direction ?? 'asc';
            syncFilters();
        },
        { immediate: true }
    );

    const activeFilterCount = computed(() =>
        Object.values(filters).reduce((count, value) => {
            if (Array.isArray(value)) {
                return count + (value.length > 0 ? 1 : 0);
            }

            return count + (value !== null && value !== '' ? 1 : 0);
        }, 0)
    );

    function buildFiltersPayload(): Record<string, unknown> {
        const payload: Record<string, unknown> = {};

        Object.entries(filters).forEach(([key, value]) => {
            if (Array.isArray(value)) {
                if (value.length > 0) {
                    payload[key] = value;
                }

                return;
            }

            if (value !== null && value !== '') {
                payload[key] = value;
            }
        });

        return payload;
    }

    function refresh(extra: Record<string, unknown> = {}): void {
        const filtersPayload = buildFiltersPayload();

        const data: Record<string, unknown> = {
            ...(options.query ?? {}),
            search: search.value !== '' ? search.value : undefined,
            sort: sortKey.value ?? undefined,
            direction: sortKey.value ? sortDirection.value : undefined,
            ...extra,
        };

        if (Object.keys(filtersPayload).length > 0) {
            data.filters = filtersPayload;
        }

        router.get(route(options.routeName), data, {
            preserveScroll: true,
            preserveState: true,
            replace: true,
        });
    }

    watchDebounced(
        search,
        () => {
            refresh();
        },
        { debounce: 300 }
    );

    function setSearch(value: string): void {
        search.value = value;
    }

    function setSort(option: DataTableSortOption): void {
        if (sortKey.value === option.value && sortDirection.value === option.direction) {
            return;
        }

        sortKey.value = option.value;
        sortDirection.value = option.direction;
        refresh();
    }

    function toggleFilter(key: string, value: string): void {
        const config: DataTableFilterConfig | undefined = filtersConfig[key];

        if (! config) {
            return;
        }

        if (config.type === 'multi') {
            const current = Array.isArray(filters[key]) ? [...(filters[key] as string[])] : [];
            const index = current.indexOf(value);

            if (index >= 0) {
                current.splice(index, 1);
            } else {
                current.push(value);
            }

            filters[key] = current;
        } else {
            filters[key] = filters[key] === value ? null : value;
        }

        refresh();
    }

    function clearFilters(): void {
        Object.entries(filtersConfig.value).forEach(([key, config]) => {
            filters[key] = config.type === 'multi' ? [] : null;
        });

        refresh();
    }

    function isFilterActive(key: string, value: string): boolean {
        const current = filters[key];

        if (Array.isArray(current)) {
            return current.includes(value);
        }

        return current === value;
    }

    return {
        search,
        sortKey,
        sortDirection,
        filters,
        sorts,
        filtersConfig,
        activeFilterCount,
        setSearch,
        setSort,
        toggleFilter,
        clearFilters,
        refresh,
        isFilterActive,
    };
}
