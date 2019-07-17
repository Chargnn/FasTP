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
                        Files list
                        <div class="pull-right action-buttons">
                            <div class="btn-group pull-right">
                                <!--<button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                                    <span class="glyphicon glyphicon-cog" style="margin-right: 0px;"></span>
                                </button>-->
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <ul class="list-group">
                            @foreach($file_list as $file)
                                <li class="list-group-item">
                                    <div class="checkbox">
                                        @if($file !== '.' && $file !== '..')
                                            <input type="checkbox" id="checkbox" />
                                        @endif
                                        <label for="checkbox">
                                            {{ $file }}
                                        </label>
                                        <strong>{{ ftp_size(\App\Ftp::instance(), $file) > 0 ?: '' }}</strong>
                                    </div>
                                    @if($file !== '.' && $file !== '..')
                                        <div class="pull-right actikalgor1
                                        on-buttons">
                                            <a href="#"><span class="glyphicon glyphicon-download-alt"></span></a>
                                            <a href="#" class="trash"><span class="glyphicon glyphicon-trash"></span></a>
                                        </div>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="panel-footer">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>
                                    Total Count <span class="label label-info">{{ count($file_list) }}</span></h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer')
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>
    <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
@endsection