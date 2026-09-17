@extends('layouts.librenmsv1')


@section('content')
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

                    <form method="POST" action="{{ url("plugin/$plugin/action") }}" enctype="multipart/form-data">
                        @csrf


                        <div class="form-group">
                            <label for="database">Database</label>
                            <input type="text" class="form-control" id="database" name="database"
                                value="{{ $info['settings']['database'] ?? 'librenms_plugin_db' }}" placeholder="database_name">
                        </div>

                        <div class="form-group">
                            <label for="host">Database Host</label>
                            <input type="text" class="form-control" id="host" name="host"
                                value="{{ $info['settings']['host'] ?? '127.0.0.1' }}" placeholder="database_host">
                        </div>

                        <div class="form-group">
                            <label for="port">Database Port</label>
                            <input type="text" class="form-control" id="port" name="port"
                                value="{{ $info['settings']['port'] ?? '3306' }}" placeholder="database_port">
                        </div>

                        <div class="form-group">
                            <label for="username">Database Username</label>
                            <input type="text" class="form-control" id="username" name="username"
                                value="{{ $info['settings']['username'] ?? '' }}" placeholder="database_username">
                        </div>
                        <div class="form-group">
                            <label for="password">Database Password</label>
                            <input type="password" class="form-control" id="password" name="password"
                                value="{{ $info['settings']['password'] ?? '' }}" placeholder="database_password">
                        </div>


                        <button type="submit" class="btn btn-primary pull-right" name="action" value="save"
                            style="margin-left: 10px;">Save Settings</button>

                        <button type="submit" class="btn btn-primary pull-right" name="action" value="test">Test Settings</button>

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
