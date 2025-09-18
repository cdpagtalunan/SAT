@extends('layouts.admin_layout')

@section('title', 'Home')
@section('content_page')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>SAT</h1>
                </div>
                <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item active">Approval</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="row">
            <div class="col">
                <div class="card"> 
                    <div class="card-body">
                        <table class="table table-sm table-bordered table-striped w-100" id="tableSatApproval">
                            <thead>
                                <tr>
                                    <th>Action</th>
                                    <th>Device Name</th>
                                    <th>Operations Line</th>
                                    <th>Assembly Line</th>
                                    <th>QSAT</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
@section('js_content')
<script src="@php echo asset("public/js/main/satApproval.js?".date("YmdHis")) @endphp"></script>
<script>
    $(document).ready(function () {
        
    });
</script>
@endsection