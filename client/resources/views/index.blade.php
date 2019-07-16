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
                        <span class="glyphicon glyphicon-list"></span>Sortable Lists
                        <div class="pull-right action-buttons">
                            <div class="btn-group pull-right">
                                <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                                    <span class="glyphicon glyphicon-cog" style="margin-right: 0px;"></span>
                                </button>
                                <ul class="dropdown-menu slidedown">
                                    <li><a href="http://www.jquery2dotnet.com"><span class="glyphicon glyphicon-pencil"></span>Edit</a></li>
                                    <li><a href="http://www.jquery2dotnet.com"><span class="glyphicon glyphicon-trash"></span>Delete</a></li>
                                    <li><a href="http://www.jquery2dotnet.com"><span class="glyphicon glyphicon-flag"></span>Flag</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <ul class="list-group">
                            @foreach($file_list as $file)
                                <li class="list-group-item">
                                    @if($file !== '.' && $file !== '..')
                                        <div class="checkbox">
                                            <input type="checkbox" id="checkbox" />
                                            <label for="checkbox">
                                                {{ $file }}
                                            </label>
                                    @else
                                        <div class="checkbox">
                                            <label>
                                                {{ $file }}
                                            </label>
                                    @endif
                                            <strong>{{ ftp_size($conn, ' 906322336.txt') > 0 ?: '' }}</strong>
                                        </div>
                                    @if($file !== '.' && $file !== '..')
                                        <div class="pull-right actikalgor1
                                        on-buttons">
                                            <a href="#"><span class="glyphicon glyphicon-download-alt"></span></a>
                                            <a href="#" class="trash"><span class="glyphicon glyphicon-trash"></span></a>
                                            <a href="#" class="flag"><span class="glyphicon glyphicon-flag"></span></a>
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
                                    Total Count <span class="label label-info">25</span></h6>
                            </div>
                            <div class="col-md-6">
                                <ul class="pagination pagination-sm pull-right">
                                    <li class="disabled"><a href="javascript:void(0)">«</a></li>
                                    <li class="active"><a href="javascript:void(0)">1 <span class="sr-only">(current)</span></a></li>
                                    <li><a href="http://www.jquery2dotnet.com">2</a></li>
                                    <li><a href="http://www.jquery2dotnet.com">3</a></li>
                                    <li><a href="http://www.jquery2dotnet.com">4</a></li>
                                    <li><a href="http://www.jquery2dotnet.com">5</a></li>
                                    <li><a href="javascript:void(0)">»</a></li>
                                </ul>
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