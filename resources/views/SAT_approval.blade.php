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
                { "data" : "sat_header_id" },
                { "data" : "sat_header_id" },
                { "data" : "sat_header_id" },
                { "data" : "sat_header_id" },
            ],
        });
    });

    $(document).on('click', '.btnSeeDetails', function(){
        let satId = $(this).data('satId');

        getSatDetails(satId);
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