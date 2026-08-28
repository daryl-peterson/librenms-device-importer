<div class="panel panel-default" style="margin: 2em"></div>


        <div class="row">
            <div class="col-md-8 col-md-offset-2">



                <!-- Main Form Card -->
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title"><i class="fa fa-cog"></i> File Uploader Settings</h3>
                    </div>
                    <div class="panel-body">

                        <form method="POST" enctype="multipart/form-data">


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
