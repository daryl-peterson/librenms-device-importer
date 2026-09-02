@extends('layouts.librenmsv1')

@section('content')
<div class="panel panel-default" style="margin: 2em; padding: 2em;">
    <h2>Device Importer upload</h2>


    <form method="post" action="{{ url('plugin/device-importer/action') }}" enctype="multipart/form-data" style="margin-bottom: 28px;" id="device-photo-upload-form">
        @csrf
        <input type="hidden" name="action" value="upload">


            <div class="form-group">
                <input class="form-control col-md-6" type="file" name="csv" accept=".csv" style="max-width: 150px; margin-bottom: 10px;">
                <button type="submit" class="btn btn-primary">Upload CSV</button>
            </div>

    </form>

</div>
@endsection
