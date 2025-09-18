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
                        <li class="breadcrumb-item active">Approvers</li>
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
                        <div class="row">
                            <div class="col d-flex justify-content-end">
                                <button class="btn btn-sm btn-primary" id="btnAddApprover">Add Approver</button>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col">
                                <table class="table table-striped table-bordered table-sm w-100" id="tableApprovers">
                                    <thead>
                                        <tr>
                                            <th>Actions</th>
                                            <th>Name</th>
                                            <th>Approval Type</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<!-- Modal -->
<div class="modal fade" id="modalApprovers" tabindex="-1" role="dialog" aria-labelledby="modalTitleId">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formApprover">
                @csrf
                <input type="hidden" id="txtApproverId" name="approver_id">
                <div class="modal-body">
                    <div class="input-group">
                        <span class="input-group-text w-50">Name:</span>
                        {{-- <input type="text" class="form-control" id="" name="" required> --}}
                        <select name="name" id="selName" class="form-control select2bs"></select>
                    </div>

                    <div class="input-group mt-2">
                        <span class="input-group-text w-50">Approval Type:</span>
                        <select name="approval_type" id="selApprovalType" class="form-control">
                            <option value="" selected disabled>--Select-</option>
                            <option value="1">Engineering Section Head</option>
                            <option value="2">Production Section Head</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="submit" class="btn btn-primary" id="btnSaveApprover">Save</button>
                </div>
            </form>
           
        </div>
    </div>
</div>

@endsection
@section('js_content')
<script src="@php echo asset("public/js/main/approverList.js?".date("YmdHis")) @endphp"></script>
<script>
    $('.select2bs').select2({
        theme: 'bootstrap-5',
        minimumResultsForSearch: 0,
        dropdownParent: $('#modalApprovers') // <-- modal ID here
    });
    let dtApprovers

    getUserList($('#selName'));
    $(document).ready(function () {
       
        dtApprovers = $("#tableApprovers").DataTable({
            "processing" : true,
            "serverSide" : true,
            "ajax" : {
                url: "{{ route('dt_get_approver_list') }}",
            },
            fixedHeader: true,
            "columns":[
                { "data" : "actions", orderable:false, searchable:false },
                { 
                    "data" : 'employee_details',
                    render: function(data){
                        return `${data.FirstName} ${data.LastName}`;
                    }
                },
                { 
                    "data" : "approval_type",
                    render: function(data){
                        if(data == 1){
                            return 'Engineering Section Head'
                        }
                        else{
                            return 'Production Section Head'
                        }
                    }
                },
            ],
        });

        $('#modalApprovers').on('hidden.bs.modal', function(){
            $('#selName').val('').trigger('change');
            modalCloseResetForm($('#formApprover')[0], 'modalApprovers')
        })

        $('#btnAddApprover').on('click', function(){
            $('#modalTitle').html('Add Approver')
            $('#modalApprovers').modal('show');
        });

        $('#formApprover').on('submit', function(e){
            e.preventDefault();
            saveApprover();
        })
    });

    $(document).on('click', '.btnEditApprover', function(e){
        let details = $(this).data('details');
        $('#modalTitle').html('Edit Approver');
        $('#txtApproverId').val(details.id)
        $('#selName').val(details.emp_id).trigger('change')
        $('#selApprovalType').val(details.approval_type).trigger('change')
        $('#modalApprovers').modal('show');
    })

    $(document).on('click', '.btnDeleteApprover', function(){
        let approverId = $(this).data('id');
        Swal.fire({
            title: "Do you want to delete approver?",
            confirmButtonText: "Yes",
            showCancelButton: true,
        }).then((result) => {
        /* Read more about isConfirmed, isDenied below */
            if (result.isConfirmed) {
                deleteApprover(approverId);
            }
        });
    });
</script>
@endsection