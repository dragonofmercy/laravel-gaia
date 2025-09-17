<div class="alert alert-danger">
@foreach($decorator->getGlobalErrors() as $name => $error)
    <div>{{ $error }}</div>
@endforeach
</div>