@extends('layouts.master')

@section('contents')
    <div class="row">
        <div class="col-4">
            <div class="card" style="width: 20rem;">
                <div class="card-header">
                    LameDB to .m3u (all channels)
                </div>
                <div class="card-body">
                    <form action="/m3u/lamedb2m38" method="POST" enctype="multipart/form-data">
                        {{ csrf_field() }}

                        <div class="form-group">
                            <label for="exampleInputEmail1">Host</label>
                            <input class="form-control" name="m3u_host" placeholder="Host url" value="{{ $m3u_host }}">
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
