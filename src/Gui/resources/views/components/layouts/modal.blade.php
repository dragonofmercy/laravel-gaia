@props(['id' => 'gui-modal', 'animated' => true, 'modalSize' => null])
<div @class(['modal', 'fade' => $animated]) id="{{ $id }}">
    <div @class(['modal-dialog', $modalSize])>
        <div class="modal-content">{{ $slot }}</div>
    </div>
</div>