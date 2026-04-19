@if ($paginator->hasPages())
    @php
        $currentPage = $paginator->currentPage();
        $lastPage = $paginator->lastPage();

        // Keep number buttons compact: max 5 visible pages.
        $startPage = max(1, min($currentPage - 2, $lastPage - 4));
        $endPage = min($lastPage, $startPage + 4);
    @endphp

    <ul class="pagination">
        <li class="page-item {{ $paginator->onFirstPage() ? 'disabled' : '' }}">
            @if ($paginator->onFirstPage())
                <span class="page-link" aria-hidden="true">&lt;&lt;</span>
            @else
                <a class="page-link" href="{{ $paginator->url(1) }}" rel="first" aria-label="First">&lt;&lt;</a>
            @endif
        </li>

        <li class="page-item {{ $paginator->onFirstPage() ? 'disabled' : '' }}">
            @if ($paginator->onFirstPage())
                <span class="page-link" aria-hidden="true">&lt;</span>
            @else
                <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev"
                    aria-label="Previous">&lt;</a>
            @endif
        </li>

        @for ($page = $startPage; $page <= $endPage; $page++)
            <li class="page-item {{ $page === $currentPage ? 'active' : '' }}"
                aria-current="{{ $page === $currentPage ? 'page' : 'false' }}">
                @if ($page === $currentPage)
                    <span class="page-link">{{ $page }}</span>
                @else
                    <a class="page-link" href="{{ $paginator->url($page) }}">{{ $page }}</a>
                @endif
            </li>
        @endfor

        <li class="page-item {{ $paginator->hasMorePages() ? '' : 'disabled' }}">
            @if ($paginator->hasMorePages())
                <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Next">&gt;</a>
            @else
                <span class="page-link" aria-hidden="true">&gt;</span>
            @endif
        </li>

        <li class="page-item {{ $paginator->hasMorePages() ? '' : 'disabled' }}">
            @if ($paginator->hasMorePages())
                <a class="page-link" href="{{ $paginator->url($lastPage) }}" rel="last"
                    aria-label="Last">&gt;&gt;</a>
            @else
                <span class="page-link" aria-hidden="true">&gt;&gt;</span>
            @endif
        </li>
    </ul>
@endif
