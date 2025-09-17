@if ($paginator->hasPages())
    <ul class="pagination mb-0">
        <li class="page-item">
            <a @class(['page-link', 'disabled' => $paginator->onFirstPage()]) data-gui-url="{{ $paginator->previousPageUrl() }}">
                <x-gui::tabler-icon name="chevron-left" />
            </a>
        </li>
        @foreach($elements as $element)
            @if(is_array($element))
                @foreach($element as $page => $url)
                    <li class="page-item">
                        <a @class(['page-link', 'active' => $page == $paginator->currentPage()]) data-gui-url="{{ $url }}">{{ $page }}</a>
                    </li>
                @endforeach
            @else
                <li class="page-item disabled">
                    <div class="page-text">
                        <x-gui::tabler-icon name="antenna-bars-1" />
                    </div>
                </li>
            @endif
        @endforeach
        <li class="page-item">
            <a @class(['page-link', 'disabled' => $paginator->onLastPage()]) data-gui-url="{{ $paginator->nextPageUrl() }}">
                <x-gui::tabler-icon name="chevron-right" />
            </a>
        </li>
    </ul>
@endif