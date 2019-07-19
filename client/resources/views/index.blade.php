@extends('layout.layout')

@section('header')
    <link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <link rel="stylesheet" type="text/css" href="/css/main.css" />
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        Files list - {{ Session::get('path') ?: '/' }} - {{ ftp_systype($conn) }}
                        <div class="pull-right action-buttons">
                            <div class="btn-group pull-right">
                                <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                                    <span class="glyphicon glyphicon-cog" style="margin-right: 0px;"></span>
                                </button>
                                <ul class="dropdown-menu slidedown">
                                    <li><a href="/disconnect" onclick="if(!confirm('Are you sure?')){ return false; }"><span class="glyphicon glyphicon-log-out"></span>Disconnect</a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="pull-right action-buttons">
                            <div class="btn-group pull-right">
                                <button type="button" class="btn btn-default btn-xs" onclick="location.reload()">
                                    <span class="glyphicon glyphicon-refresh" style="margin-right: 0px;"></span>
                                </button>
                            </div>
                        </div>
                        <div class="pull-right action-buttons">
                            <div class="btn-group pull-right">
                                <button type="button" class="btn btn-default btn-xs">
                                    <span class="glyphicon glyphicon-search" style="margin-right: 0px;"></span>
                                </button>
                                <ul class="dropdown-menu slidedown">
                                    <li>Search<input type="text" /></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <ul class="list-group">
                            @foreach($file_list as $file)
                                <?php $isDir = \App\Ftp::isDir($file, $conn); ?>
                                <li class="list-group-item">
                                    <div class="checkbox">
                                        @if(!$isDir)
                                            <!--<input type="checkbox" id="checkbox" />-->
                                        @endif
                                        @if(!$isDir)
                                            <small>{{ ftp_mdtm($conn, $file) ? \App\Ftp::formatDate(ftp_mdtm($conn, $file)) : '' }}</small>
                                        @endif
                                        <label for="checkbox">
                                            @if($isDir)
                                                <form action="/browse" method="POST">
                                                    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                                                    <button type="submit" name="path" value="{{ (ends_with(ftp_pwd($conn), '/') ? ftp_pwd($conn): ftp_pwd($conn).'/').$file }}" class="btn-link">{{ $file }}</button>
                                                </form>
                                            @else
                                                {{ $file }}
                                            @endif
                                        </label>
                                        @if(!$isDir)
                                            <small>{{ ftp_size($conn, $file) > 0 ? '('.\App\Ftp::formatSize(ftp_size($conn, $file)).')' : '(0 B)' }}</small>
                                        @endif
                                    </div>
                                    @if(!$isDir)
                                        <div class="pull-right action-buttons">
                                            @if(\App\Ftp::isFileExtension($file, '.txt') || \App\Ftp::isFileExtension($file, '.json'))
                                                <a href="/see/{{ $file }}" class="flag"><span class="glyphicon glyphicon-eye-open"></span></a>
                                            @endif
                                            <a href="/download/{{ $file }}"><span class="glyphicon glyphicon-download-alt"></span></a>
                                            <a href="/delete/{{ $file }}" class="trash"><span class="glyphicon glyphicon-trash"></span></a>
                                        </div>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="panel-footer">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Total Count <span class="label label-info">{{ count($file_list) }}</span></h6>
                            </div>
                            <div class="col-md-3">
                                <form action="/upload" method="POST" enctype="multipart/form-data" class="uploadForm">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                                    <label for="upload" style="display: flex;"><input type="file" name="uploads[]" class="inputfile" multiple="multiple" onchange="this.form.submit();" />
                                    </label>
                                </form>
                            </div>
                            <div class="col-md-3">
                                <form action="/createDir" method="POST" class="createDir">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                                    <label for="createDir" style="display: flex;">Create a directory: <input type="text" name="dir" onkeyup="if (event.keyCode === 13) {this.form.submit();}" />
                                    </label>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer')
    <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>
@endsection