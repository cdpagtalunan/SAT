@extends('layouts.admin_layout')

@section('title', 'Approvals')
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
                                    <th>Status</th>
                                    <th>Device Name</th>
                                    <th>Operations Line</th>
                                    <th>Assembly Line</th>
                                    <th>QSAT</th>
                                    <th>SAT Status</th>
                                    <th>Line Balance Status</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

@include('components.view_sat_modal')
@endsection
@section('js_content')
<script src="@php echo asset("public/js/main/satApproval.js?".date("YmdHis")) @endphp"></script>
<script>
    let dtSatApproval;
    $(document).ready(function () {
        dtSatApproval = $("#tableSatApproval").DataTable({
            "processing" : true,
            "serverSide" : true,
            "ajax" : {
                url: "{{ route('dt_sat_approval') }}"
                // data: function (param){
                //     param.po = $("#id").val();
                // }
            },
            fixedHeader: true,
            "columns":[
                { "data" : "action", orderable:false, searchable:false },
                { "data" : "status" },
                { "data" : "sat_details.device_name" },
                { "data" : "sat_details.operation_line" },
                { "data" : "sat_details.assembly_line" },
                { "data" : "sat_details.qsat" },
                { 
                    "data" : "SAT_status",
                    render: function(data, type, row, meta){
                        if(data){
                            return 'Good';
                        }
                        return 'Not Good'
                        // return '<span style="height: 100%; display:block;background-color:'+color+';color:white;padding:2px 8px;border-radius:4px;">' + data + '</span>';
                    },
                    createdCell: function (td, cellData, rowData, row, col) {
                        if (cellData) {
                            $(td).addClass('bg-success'); // red for high tact_time
                        } else {
                            $(td).addClass('bg-danger'); // green for low tact_time
                        }
                    }
                },
                {
                    "data" : "lb_status",
                    render: function(data, type, row, meta){
                        if(data){
                            return 'Good';
                        }
                        return 'Not Good'
                        // return '<span style="height: 100%; display:block;background-color:'+color+';color:white;padding:2px 8px;border-radius:4px;">' + data + '</span>';
                    },
                    createdCell: function (td, cellData, rowData, row, col) {
                        if (cellData) {
                            $(td).addClass('bg-success'); // red for high tact_time
                        } else {
                            $(td).addClass('bg-danger'); // green for low tact_time
                        }
                    }
                }
            ],
            "columnDefs": [
                {"className": "dt-center", "targets": [6,7]},
            ],
        });
    });

    $(document).on('click', '.btnSeeSatDetails', function(){
        let satId = $(this).data('id');
        let assemblyLine = $(this).data('assemblyLine');
        let deviceName = $(this).data('deviceName');
        let noOfPins = $(this).data('noOfPins');
        let operationLine = $(this).data('operationLine');
        let qsat = $(this).data('qsat');

        $('#txtSatDeviceNameView').val(deviceName)
        $('#txtOpLineView').val(operationLine)
        $('#txtAssyLineView').val(assemblyLine)
        $('#txtNoPinsView').val(noOfPins)
        $('#txtQsatView').val(qsat)
        // getSatDetails(satId);
        drawViewSatObservation(satId)
        $('#modalViewSatDetails').modal('show');
    });

    $(document).on('click', '.btnApprove', function(){
        let approverType = $(this).data('approver');
        let approvalId = $(this).data('approveId');
        
        Swal.fire({
            // title: "Do you want to proceed to observation?",
            text: "Are you sure you want approve this?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: "Save",
        }).then((result) => {
            /* Read more about isConfirmed, isDenied below */
            if (result.isConfirmed) {
                satProcessApproval(approvalId,approverType);
            }
        })
    })
</script>
@endsection