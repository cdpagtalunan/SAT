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
                        <li class="breadcrumb-item active">Users</li>
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
                        <div class="row mt-2">
                            <div class="col">
                                <table class="table table-striped table-bordered table-sm w-100" id="tableUsers">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>
                                                Checker 
                                                <i class="fa-solid fa-info-circle" data-bs-toggle="tooltip" data-bs-placement="right" title="Line Balancer"></i>
                                            </th>
                                            <th>Admin</th>
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
@endsection
@section('js_content')
{{-- <script src="@php echo asset("public/js/main/user.js?".date("YmdHis")) @endphp"></script> --}}
<script>
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
    })

    let dtUsers;
    $(document).ready(function () {
        dtUsers = $("#tableUsers").DataTable({
            "processing" : true,
            "serverSide" : true,
            "ajax" : {
                url: "{{ route('dt_get_users') }}"
            },
            fixedHeader: true,
            "columns":[
                { "data" : "rapidx_user.name"},
                { "data" : "btn_checker" },
                { "data" : "btn_admin" },
            ],
            "columnDefs": [
                {"className": "dt-center", "targets": "_all"},
            ],
        });//end of dataTable
    });

    $(document).on('click', '.btnChecker', function(){
        let rapidxId = $(this).data('rapidx-id');
        let status = $(this).data('status');
        let type = $(this).data('type');
        let fn = 'checker';

        updateStatus(rapidxId, status, type, fn);
    })

    $(document).on('click', '.btnAdmin', function(){
        let rapidxId = $(this).data('rapidx-id');
        let status = $(this).data('status');
        let type = $(this).data('type');
        let fn = 'admin';
        updateStatus(rapidxId, status, type, fn);

      
        
    })

    const updateStatus = (rapidxId, status, type, fn) => {
        Swal.fire({
            title: 'Are you sure?',
            text: `You are about to change the ${fn} status of this user.`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url : "{{ route('update_status') }}",
                    type : "POST",
                    data : {
                        _token   : "{{ csrf_token() }}",
                        rapidx_id: rapidxId,
                        status   : status,
                        type     : type,
                        fn       : fn,
                    },
                    success : function(res){
                        if(res.result){
                            dtUsers.draw();
                            toastr.success('Update Successful.');
                        }
                        else{
                            toastr.error('Something went wrong.');
                        }
                    },
                    error : function(err){
                        toastr.error('An error occured while processing your request.', 'Error');
                    }
                });
            }
        })
    }
</script>
@endsection