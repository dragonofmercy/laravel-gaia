<style>
    {{ $decorator->buildStyles() }}
</style>

<x-gui::flash :toast="true" name="datatable_{{ $decorator->getDatatableUid() }}" class="m-3" />

@if($decorator->hasSearchFilters())
<div @class(['collapse', 'show' => $decorator->shouldDisplaySearch()]) id="datagrid_search_{{ $decorator->getDatatableUid() }}">
    <div class="datatable-search" data-search-url="{{ $decorator->buildUrl(1) }}">
        <div class="search-container">
            <div class="search-groups">
            @foreach($decorator->getSearchFilters() as $filter)
                <form-group>
                    <div class="{{ $filter->get('class') }}">
                        @if($filter->get('label') !== '')
                            <div class="control-label"><label>{{ $filter->get('label') }}</label></div>
                        @endif
                        <div class="control-field">{{ $filter->get('element') }}</div>
                    </div>
                </form-group>
            @endforeach
            </div>
            <div class="search-buttons">
                <x-gui::button data-trigger="search" data-loading-text="{{ trans('gui::messages.generic.loading') }}"><x-gui::tabler-icon name="search" />@lang('gui::messages.datatable.search')</x-gui::button>
                <x-gui::button data-trigger="clear" data-loading-text="{{ trans('gui::messages.generic.loading') }}"><x-gui::tabler-icon name="eraser" />@lang('gui::messages.datatable.clear')</x-gui::button>
                @if(!$decorator->isSearchAlwaysVisible())
                <x-gui::button data-trigger="close" class="ms-auto" data-bs-toggle="collapse" data-bs-target="datagrid_search_{{ $decorator->getDatatableUid() }}">
                    <x-gui::tabler-icon name="x" />@lang('gui::messages.datatable.close')
                </x-gui::button>
                @endif
            </div>
        </div>
    </div>
</div>
@endif

<table class="table card-table" data-gui-behavior="datatable">
    <thead>
        <tr>
        @foreach($decorator->getColumns() as $name => $label)
            <th {{ $decorator->getColumnAttributes($name, $label, false) }}>
                {{ $decorator->getHeaderCell($name, $label) }}
            </th>
        @endforeach
        </tr>
    </thead>
    <tbody>
    @if(!$decorator->hasRows())
        <tr>
            <td colspan="{{ count($decorator->getColumns()) }}" class="empty-cell">
                <div class="empty">
                @if($decorator->hasSearchValues())
                    <div class="empty-icon"><x-gui::tabler-icon name="filter-exclamation" /></div>
                    <div class="empty-text">@lang('gui::messages.datatable.empty_search')</div>
                @else
                    <div class="empty-icon"><x-gui::tabler-icon name="playlist-x" /></div>
                    <div class="empty-text">@lang('gui::messages.datatable.empty')</div>
                @endif
                </div>
            </td>
        </tr>
    @else
    @foreach($decorator->getRows() as $row)
        <tr>
        @foreach($decorator->getColumns() as $name => $label)
            <td {{ $decorator->getColumnAttributes($name, $label) }}>{{ $decorator->getCell($row, $name) }}</td>
        @endforeach
        </tr>
    @endforeach
    @endif
    </tbody>
</table>

<div class="card-body">
    <div class="d-flex flex-column gap-3 flex-lg-row justify-content-between align-items-center">
        <div>@lang('gui::messages.datatable.records', ['first' => $decorator->getPagination()->firstItem() ?? 0, 'last' => $decorator->getPagination()->lastItem() ?? 0, 'total' => $decorator->getPagination()->total() ?? 0])</div>
        @if($decorator->getPagination()->hasPages())
            {{ $decorator->getPagination()->onEachSide(2)->links('gui::datatables.default-pagination') }}
        @endif
    </div>
</div>