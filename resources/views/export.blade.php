@extends('layouts.librenmsv1')

@section('content')
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

                    <form method="post" action="{{ url('plugin/device-importer/action') }}" enctype="multipart/form-data"
                        style="margin-bottom: 28px;" id="device-importer-export-form">
                        @csrf
                        <input type="hidden" name="action" value="export">

                        <button type="submit" class="btn btn-primary pull-right">Export</button>

                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
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
