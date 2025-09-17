<script>
    @if(\Illuminate\Support\Facades\Request::isXmlHttpRequest())
    {!! $slot !!}
    @else
    window.addEventListener("DOMContentLoaded",function(){
        <?php echo $slot ?>
    })
    @endif
</script>