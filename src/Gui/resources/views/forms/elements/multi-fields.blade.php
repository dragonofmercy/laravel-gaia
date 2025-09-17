<div {{ $attr }}>
    <div class="mf-content">
        <table>
            <thead>
            @foreach($columns as $name => $label)
                <th {{ $columnsAttributes->get($name) }}>{{ $label }}</th>
            @endforeach
            </thead>
            <tbody>
            @foreach($rows as $index => $row)
                <tr>
                @foreach($row as $columnName => $field)
                    <td {{ $columnsAttributes->get($columnName) }}>
                    @if($columnName !== 'gui-control')
                        {{ $field }}
                        @if($errors->get($index)->get($columnName))
                        <div class="invalid-feedback">{{ $errors->get($index)->get($columnName) }}</div>
                        @endif
                    @else
                        <x-gui::button class="btn-link btn-square" data-trigger="remove"><x-gui::tabler-icon name="trash" /></x-gui::button>
                        @if($sortable)
                        <x-gui::button class="btn-link btn-square" data-trigger="move"><x-gui::tabler-icon name="grip-horizontal" /></x-gui::button>
                        @endif
                    @endif
                    </td>
                @endforeach
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    @isset($invalidFeedback)
        <div class="invalid-feedback">{{ $invalidFeedback }}</div>
    @endif
    <x-gui::button data-trigger="add"><x-gui::tabler-icon name="copy-plus" />{{ $stringAddLine }}</x-gui::button>
</div>
<x-gui::javascript-ready>
    $('#{{ $attr->get('id') }}').GuiControlMultiFields({!! $componentConfig !!})
</x-gui::javascript-ready>