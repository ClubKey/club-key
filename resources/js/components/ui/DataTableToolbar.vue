<script lang="ts" setup>
import { useVModel } from '@vueuse/core';
import { ArrowUpDown, Filter, Search } from 'lucide-vue-next';
import { computed } from 'vue';

import Button from '@/components/ui/button/Button.vue';
import Input from '@/components/ui/input/Input.vue';
import {
    DropdownMenu,
    DropdownMenuCheckboxItem,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import type { DataTableFilterConfig, DataTableSortOption } from '@/types';

type FilterState = Record<string, string[] | string | null>;

const props = withDefaults(
    defineProps<{
        search: string;
        sorts: DataTableSortOption[];
        filters: Record<string, DataTableFilterConfig>;
        filtersState: FilterState;
        activeFilterCount: number;
        searchPlaceholder?: string;
        sortKey: string | null;
    }>(),
    {
        searchPlaceholder: 'Search',
    }
);

const emits = defineEmits<{
    (e: 'update:search', value: string): void;
    (e: 'sort-change', value: DataTableSortOption): void;
    (e: 'filter-toggle', payload: { key: string; value: string }): void;
    (e: 'filter-clear'): void;
}>();

const searchModel = useVModel(props, 'search', emits);

const currentSort = computed(() =>
    props.sorts.find((option) => option.value === props.sortKey) ?? props.sorts[0] ?? null
);

const hasFilters = computed(() => Object.keys(props.filters).length > 0);

function isFilterActive(key: string, value: string): boolean {
    const state = props.filtersState[key];

    if (Array.isArray(state)) {
        return state.includes(value);
    }

    return state === value;
}

function handleSortChange(value: string): void {
    const option = props.sorts.find((item) => item.value === value);

    if (! option) {
        return;
    }

    emits('sort-change', option);
}

function handleFilterToggle(key: string, value: string): void {
    emits('filter-toggle', { key, value });
}

function clearFilters(): void {
    emits('filter-clear');
}
</script>

<template>
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="w-full sm:max-w-sm">
            <div class="relative">
                <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-muted-foreground">
                    <Search class="h-4 w-4" aria-hidden="true" />
                </span>
                <Input
                    v-model="searchModel"
                    :placeholder="searchPlaceholder"
                    class="pl-10"
                    type="search"
                />
            </div>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <DropdownMenu v-if="props.sorts.length">
                <DropdownMenuTrigger as-child>
                    <Button variant="outline" size="sm" class="flex items-center gap-2">
                        <ArrowUpDown class="h-4 w-4" aria-hidden="true" />
                        <span>{{ currentSort?.label ?? 'Sort' }}</span>
                    </Button>
                </DropdownMenuTrigger>
                <DropdownMenuContent align="end" class="w-56">
                    <DropdownMenuLabel>Sort by</DropdownMenuLabel>
                    <DropdownMenuSeparator />
                    <DropdownMenuItem
                        v-for="option in props.sorts"
                        :key="option.value"
                        :data-state="currentSort?.value === option.value ? 'checked' : undefined"
                        @select.stop.prevent="handleSortChange(option.value)"
                    >
                        {{ option.label }}
                    </DropdownMenuItem>
                </DropdownMenuContent>
            </DropdownMenu>

            <DropdownMenu v-if="hasFilters">
                <DropdownMenuTrigger as-child>
                    <Button variant="outline" size="sm" class="flex items-center gap-2">
                        <Filter class="h-4 w-4" aria-hidden="true" />
                        <span>Filters</span>
                        <span
                            v-if="activeFilterCount > 0"
                            class="inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-primary px-1 text-xs font-semibold text-primary-foreground"
                        >
                            {{ activeFilterCount }}
                        </span>
                    </Button>
                </DropdownMenuTrigger>
                <DropdownMenuContent align="end" class="w-72">
                    <div class="flex items-center justify-between px-2 py-1.5">
                        <DropdownMenuLabel class="p-0">Filters</DropdownMenuLabel>
                        <Button
                            variant="ghost"
                            size="sm"
                            class="h-7 px-2 text-xs"
                            :disabled="activeFilterCount === 0"
                            @click.stop.prevent="clearFilters"
                        >
                            Reset
                        </Button>
                    </div>
                    <DropdownMenuSeparator />
                    <div class="max-h-72 overflow-auto p-2">
                        <div v-for="(config, key) in props.filters" :key="key" class="mb-4 last:mb-0">
                            <p class="text-xs font-semibold uppercase text-muted-foreground">{{ config.label }}</p>
                            <div class="mt-2 space-y-1">
                                <DropdownMenuCheckboxItem
                                    v-for="option in config.options"
                                    :key="option.value"
                                    :checked="isFilterActive(key, option.value)"
                                    @update:checked="() => handleFilterToggle(key, option.value)"
                                >
                                    {{ option.label }}
                                </DropdownMenuCheckboxItem>
                            </div>
                        </div>
                    </div>
                </DropdownMenuContent>
            </DropdownMenu>
        </div>
    </div>
</template>
