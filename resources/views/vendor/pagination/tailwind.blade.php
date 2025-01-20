@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex justify-between">
        @if ($paginator->onFirstPage())
            <span class="disabled">«</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="text-blue-500">«</a>
        @endif

        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="disabled">{{ $element }}</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="font-bold">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="text-blue-500">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="text-blue-500">»</a>
        @else
            <span class="disabled">»</span>
        @endif
    </nav>
@endif
