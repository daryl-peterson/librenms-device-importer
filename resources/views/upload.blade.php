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
                        style="margin-bottom: 28px;" id="device-importer-upload-form">
                        @csrf
                        <input type="hidden" name="action" value="upload">



                        <input class="form-control" type="file" name="csv" accept=".csv"
                            style="min-width: 150px; margin-bottom: 10px;">
                        <button type="submit" class="btn btn-primary pull-right">Upload CSV</button>



                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
