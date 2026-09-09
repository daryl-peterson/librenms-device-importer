@isset($info['dbStatus']['error'])
    <div class="panel panel-danger">
        <div class="panel-heading" style="padding: 2em">
            <h3 class="panel-title"><strong>Error : </strong>{{ $info['dbStatus']['error'] }}</h3>
        </div>
    </div>
@endisset
