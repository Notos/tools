@extends('layouts.master')

@section('contents')
    <div class="row">
        <div class="col-4">
            <div class="card" style="width: 20rem;">
                <div class="card-header">
                    LameDB to .m3u
                </div>
                <div class="card-body">
                    <form action="/m3u/lamedb2m38" method="POST" enctype="multipart/form-data">
                        {{ csrf_field() }}

                        <div class="form-group">
                            <label for="exampleInputEmail1">Server url & port</label>
                            <input class="form-control" name="m3u_host" placeholder="ex: http://192.168.1.10:8001" value="{{ $m3u_host }}">
                        </div>

                        <div class="form-group">
                            <label for="lamedb">Select your <strong>lamedb</strong> file</label>
                            <input type="file" class="form-control-file" id="lamedb" name="lamedb">
                        </div>

                        <button type="submit" class="btn btn-primary">Generate</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-4">
            <div class="card" style="width: 20rem;">
                <div class="card-header">
                    LameDB to .csv
                </div>
                <div class="card-body">
                    <form action="/m3u/lamedb2csv" method="POST" enctype="multipart/form-data">
                        {{ csrf_field() }}

                        <div class="form-group">
                            <label for="exampleInputEmail1">Server url & port</label>
                            <input class="form-control" name="m3u_host" placeholder="ex: http://192.168.1.10:8001" value="{{ $m3u_host }}">
                        </div>

                        <div class="form-group">
                            <label for="lamedb">Select your <strong>lamedb</strong> file</label>
                            <input type="file" class="form-control-file" id="lamedb" name="lamedb">
                        </div>

                        <button type="submit" class="btn btn-primary">Generate</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
