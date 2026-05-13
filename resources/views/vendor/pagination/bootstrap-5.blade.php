@if ($paginator->hasPages())
<nav class="mt-3" aria-label="Sayfalama">
    <ul class="pagination pagination-sm justify-content-center gap-1 mb-0">

        {{-- Önceki --}}
        @if ($paginator->onFirstPage())
            <li class="page-item disabled">
                <span class="page-link border-0 bg-light text-muted" style="border-radius:8px;">
                    <i class="bi bi-chevron-left"></i>
                </span>
            </li>
        @else
            <li class="page-item">
                <a class="page-link border-0 bg-light text-dark" style="border-radius:8px;"
                   href="{{ $paginator->previousPageUrl() }}">
                    <i class="bi bi-chevron-left"></i>
                </a>
            </li>
        @endif

        {{-- Sayfa numaraları --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <li class="page-item disabled">
                    <span class="page-link border-0 bg-transparent">{{ $element }}</span>
                </li>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <li class="page-item active">
                            <span class="page-link border-0 fw-semibold" style="border-radius:8px;background:linear-gradient(135deg,#6c63ff,#5a52d5);">
                                {{ $page }}
                            </span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link border-0 bg-light text-dark" style="border-radius:8px;"
                               href="{{ $url }}">{{ $page }}</a>
                        </li>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Sonraki --}}
        @if ($paginator->hasMorePages())
            <li class="page-item">
                <a class="page-link border-0 bg-light text-dark" style="border-radius:8px;"
                   href="{{ $paginator->nextPageUrl() }}">
                    <i class="bi bi-chevron-right"></i>
                </a>
            </li>
        @else
            <li class="page-item disabled">
                <span class="page-link border-0 bg-light text-muted" style="border-radius:8px;">
                    <i class="bi bi-chevron-right"></i>
                </span>
            </li>
        @endif

    </ul>
    <p class="text-center text-muted small mt-2 mb-0">
        {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} / {{ $paginator->total() }} post
    </p>
</nav>
@endif
