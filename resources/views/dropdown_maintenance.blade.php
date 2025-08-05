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
                        <li class="breadcrumb-item">Dropdown Maintenance</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="card"> 
            <div class="card-header d-flex justify-content-center">
                <div class="input-group input-group-sm w-auto">
                    <span class="input-group-text">Dropdown Name</span>
                    <select id="selDropdown" class="form-control form-control-sm"></select>
                    <button class="btn btn-sm btn-success" title="Add Dropdown" disabled="true" id="btnAddDropdownItems"><i class="fas fa-plus"></i></button>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover table-sm w-100" id="tableDropdownItems">
                                <thead>
                                    <tr>
                                        <th>Actions</th>
                                        <th>Name</th>
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

<div class="modal fade" id="modalDropdownItem" data-bs-backdrop="static" data-bs-formid="" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="modalDropdownItemTitle"></h3>
                <button id="close" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formDropdownItem">
                @csrf
                <input type="hidden" name="dropdown_id" id="txtDropdownId">
                <div class="modal-body">
                    <div class="form-group">
                        <label id="dropdownItemName"></label>
                        <input type="text" class="form-control" id="txtDropdownItemName" name="dropdown_item_name" required>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-sm btn-primary" type="submit">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
@section('js_content')
<script>
    let dtDropdownItems;
    let selectedVal;
    $(document).ready(function () {
        getDropdownList($('#selDropdown'));
        dtDropdownItems = $('#tableDropdownItems').DataTable({
            "columnDefs": [
                { orderable: false, targets: [0] } // Disable sort on columns 0
            ]
        });

        $('#selDropdown').on('change', function(){
            selectedVal = $(this).val();
            if(selectedVal != null){
                $('#btnAddDropdownItems').prop('disabled', false)
            }
            drawDropdownItemsDT();
        });

        $('#btnAddDropdownItems').on('click', function(){
            let selectedDropdown = $('#selDropdown option:selected').text();
            $('#txtDropdownId').val(selectedVal);
            $('#modalDropdownItemTitle').html(`<i class="fas fa-plus fa-sm"></i> Add ${selectedDropdown}`);
            $('#dropdownItemName').html(`${selectedDropdown} Name`)
            $('#modalDropdownItem').modal('show');
        });

        $('#formDropdownItem').on('submit', function(e){
            e.preventDefault();
            $.ajax({
                type: "POST",
                url: "{{ route('save_dropdown_item') }}",
                data: $(this).serialize(),
                dataType: "json",
                beforeSend: function(){
                },
                success: function (response) {
                    drawDropdownItemsDT();
                    modalCloseResetForm($('#formDropdownItem')[0], '#modalDropdownItem');
                    $('#modalDropdownItem').modal('hide')
                },
                error: function(xhr, status, error){
                    console.log('xhr: ' + xhr + "\n" + "status: " + status + "\n" + "error: " + error);
                }
            });
        })
    });

    $(document).on('click', '.btnDelete', function(){
        let id = $(this).data('id');
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "POST",
                    url: "{{ route('delete_dropdown_item') }}",
                    data: {
                        id: id,
                        dropdown_id : selectedVal,
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: "json",
                    success: function (response) {
                        drawDropdownItemsDT();
                        toastr.success('Dropdown item deleted successfully.');
                    },
                    error: function(xhr, status, error){
                        console.log('xhr: ' + xhr + "\n" + "status: " + status + "\n" + "error: " + error);
                    }
                });
            }
        })
    })

    const drawDropdownItemsDT = () => {
        dtDropdownItems = $("#tableDropdownItems").DataTable({
            "processing" : true,
            "serverSide" : true,
            "bDestroy": true,
            "ajax" : {
                url: "{{ route('dt_get_dropdown_items') }}",
                data: function (param){
                    param.selected_dropdown_id = selectedVal;
                }
            },
            fixedHeader: true,
            "columns":[
                { "data" : "action", orderable:false, searchable:false },
                { "data" : "name" },
            ],
            // "columnDefs": [
            //     {"className": "dt-center", "targets": "_all"},
            //     {
            //         "targets": [7],
            //         "data": null,
            //         "defaultContent": "---"
            //     },
            // ],
            // 'drawCallback': function( settings ) {
            //     let dtApi = this.api();
            // }
        });//end of dataTableDevices
    }
</script>
@endsection