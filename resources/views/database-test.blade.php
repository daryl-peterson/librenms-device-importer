@extends('layouts.librenmsv1')

@section('content')
    <div class="container-fluid">
        <div class="col-sm-12 col-lg-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    @include("$plugin::partials.breadcrumb")
                </div>
                <div class="panel-body">
                    <h2 class="h4">Database Test</h2>
                    <p>Check the connection and migrations table using the active Device Importer database configuration. Edit the <code>PLUGIN_DB_*</code> values in the LibreNMS <code>.env</code> to change this connection.</p>

                    <dl class="dl-horizontal">
                        <dt>Host</dt>
                        <dd>{{ $database['host'] }}</dd>
                        <dt>Port</dt>
                        <dd>{{ $database['port'] }}</dd>
                        <dt>Database</dt>
                        <dd>{{ $database['database'] }}</dd>
                        <dt>Username</dt>
                        <dd>{{ $database['username'] }}</dd>
                    </dl>

                    @if ($result !== null)
                        <div class="alert alert-{{ $result[0] }}" role="alert">{{ $result[1] }}</div>
                    @endif

                    <form method="POST" action="{{ route("$plugin.database-test") }}">
                        @csrf
                        <button type="submit" class="btn btn-primary">Test Database</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
