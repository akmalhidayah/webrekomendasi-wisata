@props(['paginator'])

@php($total = $paginator->total())

<div class="admin-pagination mt-4" data-testid="admin-pagination">
    <p class="admin-pagination-summary mb-2 text-muted">
        @if ($total === 0)
            Tidak ada data
        @else
            Menampilkan {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} dari {{ $total }} data
        @endif
    </p>
    @if ($paginator->hasPages())
        <div class="admin-pagination-links">
            {{ $paginator->onEachSide(1)->withQueryString()->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
