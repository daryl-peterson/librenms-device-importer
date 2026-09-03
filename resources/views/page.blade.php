<div style="margin-top:-12px; padding-bottom: 1em;">
    @includeIf('device-importer::layouts.flash-messages')
</div>
<div class="container-fluid">
    <div class="col-sm-12 col-lg-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                @includeIf('device-importer::partials.menu')

            </div>
            <div class="panel-body">
                @includeIf('device-importer::partials.author')

            </div>
        </div>
    </div>
</div>


@push('scripts')
    <script>
        $(document).ready(function() {
            console.log("LibreNMS custom script running!");
            $(".alert").delay(5000).fadeOut(500, function() {
                $(this).alert('close');
            });
        });
    </script>
@endpush
