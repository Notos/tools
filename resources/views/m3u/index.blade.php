@extends('layouts.master')

@section('contents')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    LameDB Exporter (m3u & csv)
                </div>
                <div class="card-body">
                    <form action="/lamedb/export" method="POST" enctype="multipart/form-data">
                        {{ csrf_field() }}

                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="exporter">Export to</label>
                                    <select class="form-control" id="exporter" name="exporter">
                                        @foreach ($exporters as $exporter)
                                            <option value="{{ $exporter['id'] }}">{{ $exporter['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="picons">Picons</label>
                                    <select class="form-control" id="picons" name="picons">
                                        @foreach ($picons as $picon)
                                            <option value="{{ $picon['id'] }}">{{ $picon['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="lamedb_group">Group</label>
                                    <input class="form-control" name="lamedb_group" placeholder="ex: http://192.168.1.10:8001" value="{{ $lamedb_group ?: old('lamedb_group') }}">
                                </div>

                                <div class="form-group">
                                    <label for="lamedb_file">Select your <strong>lamedb</strong> file</label>
                                    <input type="file" class="form-control-file" id="lamedb_file" name="lamedb_file">
                                </div>
                            </div>

                            <div class="col-6">
                                <div class="form-group">
                                    <label for="lamedb_host">Service url & port</label>
                                    <input class="form-control" name="lamedb_host" placeholder="ex: http://192.168.1.10:8001" value="{{ $lamedb_host ?: old('lamedb_host') }}">
                                </div>

                                <div class="form-group">
                                    <label for="username">Username</label>
                                    <input class="form-control" name="lamedb_username" placeholder="service username (optional)" value="{{ $lamedb_username ?: old('lamedb_username') }}">
                                </div>

                                <div class="form-group">
                                    <label for="password">Password</label>
                                    <input class="form-control" name="lamedb_password" placeholder="service password (optional)" value="{{ $lamedb_password ?: old('lamedb_password') }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 pull-right">
                                <p class="text-right">
                                    <button type="submit" class="btn btn-primary">Generate</button>
                                </p>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection





