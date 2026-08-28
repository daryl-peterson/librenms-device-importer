@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">

            <!-- Alert Notifications -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="box" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any() || session('error'))
                <div class="alert alert-danger">
                    {{ session('error') ?? 'Please correct the errors below.' }}
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Main Form Card -->
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-cog"></i> File Uploader Settings</h3>
                </div>
                <div class="panel-body">

                    <form action="{{ route('plugin.fileuploader.settings') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="form-group">
                            <label for="plugin_config_file">Upload Configuration File</label>
                            <input type="file" id="plugin_config_file" name="plugin_config_file" class="form-control-file">
                            <p class="help-block">Accepted formats: .json, .txt, .csv, .yaml (Max size: 2MB).</p>
                        </div>

                        <hr>

                        <div class="form-group clearfix">
                            <button type="submit" class="btn btn-primary pull-right">
                                <i class="fa fa-upload"></i> Upload & Save
                            </button>
                        </div>
                    </form>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection
