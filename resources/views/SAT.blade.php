@extends('layouts.admin_layout')

@section('title', 'SAT')
@section('content_page')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>SAT</h1>
                </div>
                {{-- <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active"></li>
                    </ol>
                </div> --}}
            </div>
        </div>
    </section>

    <section class="content">
        <div class="row">
            <div class="col">
                <div class="card"> 
                    <div class="card-header d-flex justify-content-end">
                        <button class="btn btn-primary" id="btnAddDataForSAT"><i class="fa-solid fa-plus"></i> Add SAT</button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover w-100" id="tableSAT">
                                <thead>
                                    <tr>
                                        <th>Action</th>
                                        <th>ID</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<div class="modal fade" id="modalAddDataSAT" data-bs-backdrop="static" data-bs-formid="" tabindex="-1" role="dialog" aria-labelledby="">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title"><i class="fas fa-info-circle fa-sm"></i> Add SAT</h3>
                <button id="close" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span>&times;</span>
                </button>
            </div>
            <form id="formDataSAT">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text">Device Name</span>
                                <input type="text" class="form-control" id="">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text">Operations Line</span>
                                <input type="text" class="form-control" id="">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text">Assembly Line</span>
                                <input type="text" class="form-control" id="">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </form>
           
        </div>
    </div>
</div>

@endsection
@section('js_content')
<script>
    $(document).ready(function () {
        // $("#tableSAT").DataTable({
        //     "processing" : true,
        //     "serverSide" : true,
        //     "ajax" : {
        //         // url: "view_first_stamp_prod",
        //         //  data: function (param){
        //         //     param.po = $("#id").val();
        //         // }
        //     },
        //     fixedHeader: true,
        //     "columns":[
        //         { "data" : "action", orderable:false, searchable:false },
        //         { "data" : "label" },
        //     ],
        //     // "columnDefs": [
        //     //     {"className": "dt-center", "targets": "_all"},
        //     //     {
        //     //         "targets": [7],
        //     //         "data": null,
        //     //         "defaultContent": "---"
        //     //     },
        //     // ],
        //     // 'drawCallback': function( settings ) {
        //     //     let dtApi = this.api();
        //     // }
        // });//end of dataTableDevices

        $('#btnAddDataForSAT').on('click', function(){
            $('#modalAddDataSAT').modal('show');
        });
    });
</script>
@endsection