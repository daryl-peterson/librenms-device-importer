
<div style="margin-top:-12px; padding-bottom: 1em;">
    @includeIf("$plugin::layouts.flash-messages")
</div>
<div class="container-fluid">
    <div class="col-sm-12 col-lg-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                @includeIf("$plugin::partials.breadcrumb")

            </div>
            <div class="panel-body">
                @includeIf("$plugin::partials.author")

                @includeIf("$plugin::partials.dbstatus")

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
