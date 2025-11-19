@props(['headers' => [], 'sortableColumns' => [], 'rowsView' => null, 'collection' => collect()])
<div class="overflow-x-auto" x-data="tableSort()">
    <table class="min-w-full divide-y divide-gray-200 text-left">
        <thead class="bg-gray-50 text-[11px] uppercase tracking-wide text-gray-500">
            <tr>
                @foreach($headers as $index => $header)
                    @php
                        $column = $sortableColumns[$index] ?? null;
                    @endphp
                    <th scope="col" class="px-4 py-2 font-semibold whitespace-nowrap">
                        @if($column)
                            <button @click="sort('{{ $column }}')" class="flex items-center gap-1 hover:text-gray-700 transition-colors">
                                <span>{{ $header }}</span>
                                <span class="text-gray-400">
                                    <i :class="{
                                        'fas fa-sort-up': sortColumn === '{{ $column }}' && sortDirection === 'asc',
                                        'fas fa-sort-down': sortColumn === '{{ $column }}' && sortDirection === 'desc',
                                        'fas fa-sort': sortColumn !== '{{ $column }}'
                                    }" class="text-xs"></i>
                                </span>
                            </button>
                        @else
                            <div class="flex items-center gap-1">
                                <span>{{ $header }}</span>
                            </div>
                        @endif
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-100">
            <template x-if="loading">
                <tr>
                    <td :colspan="{{ count($headers) }}" class="px-4 py-8 text-center text-sm text-gray-500">
                        <i class="fas fa-spinner fa-spin mr-2"></i> Memuat data...
                    </td>
                </tr>
            </template>
            <template x-if="!loading">
                @if($rowsView)
                    @include($rowsView, ['collection' => $collection])
                @else
                    {{ $slot }}
                @endif
            </template>
        </tbody>
    </table>
</div>

<script>
function tableSort() {
    return {
        sortColumn: '{{ request("sort") }}',
        sortDirection: '{{ request("dir", "asc") }}',
        loading: false,
        
        sort(column) {
            if (this.sortColumn === column) {
                this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
            } else {
                this.sortColumn = column;
                this.sortDirection = 'asc';
            }
            
            this.loading = true;
            
            const url = new URL(window.location.href);
            url.searchParams.set('sort', this.sortColumn);
            url.searchParams.set('dir', this.sortDirection);
            
            window.location.href = url.toString();
        }
    }
}
</script>
