@extends('layouts.admin_layout')

@section('title', 'SAT')
@section('content_page')
@php
@endphp
<style>
    .placeholder-cell {
        color: #aaa;
        font-style: italic;
    }
</style>
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
                                        <th>Device Name</th>
                                        <th>Operations Line</th>
                                        <th>Assembly Line</th>
                                        <th>QSAT</th>
                                        <th>No. of Pins</th>
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

<div class="modal fade" id="modalDataSAT" data-bs-backdrop="static" data-bs-formid="" tabindex="-1" role="dialog" aria-labelledby="">
    <div class="modal-dialog modal-xl-custom" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title"><i class="fas fa-info-circle fa-sm"></i> Add SAT</h3>
                <button id="close" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span>&times;</span>
                </button>
            </div>
            <form id="formDataSAT">
                @csrf
                <input type="hidden" id="txtSATId" name="sat_id">
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text w-50">Device Name</span>
                                <input type="text" class="form-control" id="txtDeviceName" name="device_name" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text w-50">Operations Line</span>
                                <select name="operation_line" id="operationLine" class="form-control select2bs5" required></select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text w-50">Assembly Line</span>
                                <select name="assembly_line" id="assemblyLine" class="form-control select2bs5" required></select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                          <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text w-50">No. of Pins</span>
                                <input type="number" min="0" class="form-control" id="txtNoPins" name="no_of_pins" required>
                            </div>
                        </div>
                          <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text w-50">QSAT</span>
                                <input type="number" min="0" class="form-control" id="txtQSAT" name="qsat" required>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row" id="addingSATProcessList">
                        <div class="col-sm-12">
                            <div class="card"> 
                               <div class="card-header d-flex">
                                    <div>
                                        Process List
                                    </div>
                                    <div class="ms-auto">
                                        <button class="btn btn-sm btn-primary" title="Add Process" id="btnAddProcessList">
                                            <i class="fa-solid fa-plus"></i> Add Process
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover table-sm w-100" id="tableProcessLists">
                                            <thead>
                                                <tr>
                                                    <th>Action</th>
                                                    <th>Process</th>
                                                    <th>Allowance (%)</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row" id="obsSAT">
                        <div class="col-sm-12">
                            <div class="card"> 
                               <div class="card-header d-flex justify-content-between">
                                    Process List
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover table-sm w-100" id="tableSATObservation">
                                            <thead>
                                                <tr>
                                                    <th rowspan="2">Action</th>
                                                    <th rowspan="2">Process</th>
                                                    <th rowspan="2">Operator</th>
                                                    <th colspan="5" class="text-center">Observation (sec. per cycle-unit)</th>
                                                    <th rowspan="2">Observed Time (secs.)</th>
                                                    <th rowspan="2">Allowance Factor (%)</th>
                                                    <th rowspan="2">Normal Time (secs.)</th>
                                                    <th rowspan="2">Standard Time (secs.)</th>
                                                    <th rowspan="2">UPH</th>
                                                </tr>
                                                <tr class="text-center">
                                                    <th>1</th>
                                                    <th>2</th>
                                                    <th>3</th>
                                                    <th>4</th>
                                                    <th>5</th>
                                                </tr>
                                            </thead>
                                            <tfoot>
                                                <tr>
                                                    <th colspan="10" class="text-end">Total</th>
                                                    <th id="totalNormalTime">0</th>
                                                    <th id="totalStandardTime">0</th>
                                                    <th></th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-sm btn-success" id="btnSaveDataSAT">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalLineBalance" data-bs-backdrop="static" data-bs-formid="" tabindex="-1" role="dialog" aria-labelledby="" >
    <div class="modal-dialog modal-xl-custom" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title"><i class="fas fa-info-circle fa-sm"></i> Line Balance</h3>
                <button id="close" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span >&times;</span>
                </button>
            </div>
            <form id="formLineBalance">
                @csrf
                <input type="hidden" id="satHeaderId" name="sat_header_id">
                <div class="modal-body">
                    <div class="row mb-2">
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-text w-50">Device Name</span>
                                <input type="text" class="form-control" id="lbDeviceName" name="" readonly>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-text w-50">Operations Line</span>
                                <input type="text" class="form-control" id="lbOperationLine" name="" readonly>

                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-text w-50">Assembly Line</span>
                                <input type="text" class="form-control" id="lbAssemblyLine" name="" readonly>

                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-text w-50">No. of Pins</span>
                                <input type="number" min="0" class="form-control" id="lbNoOfPins" name="" readonly>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="input-group">
                                <span class="input-group-text w-50">Assembly SAT</span>
                                <input type="text"class="form-control" id="txtLineBalAssySAT" name="assy_sat" readonly>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="input-group">
                                <span class="input-group-text w-50">Line Balance (%)</span>
                                <input type="text"class="form-control" id="txtLineBalVal" name="line_balance" readonly>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="input-group">
                                <span class="input-group-text w-50">Output/hr</span>
                                <input type="text"class="form-control" id="txtOutputPerHr" name="output_per_hr" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-sm-12">
                            <div class="card" id="id"> 
                                <div class="card-header">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            Line Balance
                                        </div>
                                        <div class="col-sm-6 d-flex justify-content-around">
                                            
                                            <div>
                                                Total Station SAT:
                                                <label id="TtlStationSat"> 0</label>
                                            </div>
                                            <div>
                                                Total No. of Operators:
                                                <label id="ttlNoOperator"> 0</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover table-bordered w-100" id="tableLineBalance">
                                            <thead>
                                                <tr>
                                                    <th>Process</th>
                                                    <th>Station SAT</th>
                                                    <th>No. of Operators</th>
                                                    <th>TACT</th>
                                                    <th>UPH</th>
                                                </tr>
                                            </thead>
                                        
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-sm btn-success" title="Save Line Balance" id="btnSaveLineBalance">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>


@endsection
@section('js_content')

<script>
    
    let dtProcessLists, dtSat, dtSatObservation, dtLineBalance;
    let operatorListSAT;
    $(document).ready(function () {
        // Calls the function to fetch dropdown data for Assembly Line and Operation Line when the page loads.
        // The data will be used to populate the corresponding select elements in the modal form.
        getDropdownData(['assemblyLine', 'operationLine']);
        arrayProcess = [];

        /**
         * Initialize the DataTable for the Process List modal.
         * Sets up columns for action (delete), process, and allowance.
         * Makes "Process" and "Allowance" columns editable with placeholder logic for empty cells.
         * Adds a delete button for each row.
         * Applies center alignment to the action column.
         */
        // <button class="btn btn-sm btn-secondary" type="button" title="Edit Process" onclick="editProcessList(${meta.row})">
        //                         <i class="fa-solid fa-edit"></i>
        //                     </button>
        dtProcessLists = $('#tableProcessLists').DataTable({
            "info" : false,
            "processing" : true,
            "searching" : false,
            "paging" : false,
            "columns": [
                { 
                    "data": "data",
                    render: function (data, type, row, meta) {
                        return `
                            <button class="btn btn-sm btn-danger" title="Delete Process" onclick="dtProcessLists.row(${meta.row}).remove().draw();">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                            
                        `;
                    },
                },
                { "data": "process_name" },
                { "data": "allowance" },
            ],
            "columnDefs": [
                {
                    targets: [1, 2], // which columns are editable (0-based index)
                    createdCell: function (td, cellData, rowData, row, col) {
                        console.log(rowData);
                        // $(td).attr('contenteditable', 'true');
                        let headerText = $('#tableProcessLists thead th').eq(col).text().trim();
                        if (cellData === "*" || !cellData) {
                            if(col == 1){
                               $(td)
                                .attr('contenteditable', 'true')
                                .attr('title', `Enter ${headerText}`)
                                .addClass('placeholder-cell')
                                .text(`Enter ${headerText}`);
                            } else {
                               $(td)
                                .attr('contenteditable', 'true')
                                .attr('title', `Enter ${headerText}`)
                                .text('0.00');
                            }
                        }
                        else{
                            $(td).attr('contenteditable', 'true').text(cellData);
                        }
                    }
                },
                { "className": "dt-center", "targets": [0]},
            ]
        })

        dtSat = $("#tableSAT").DataTable({
            "processing" : true,
            "serverSide" : true,
            "ajax" : {
                url: "{{ route('dt_get_sat') }}",
            },
            fixedHeader: true,
            "columns":[
                { "data" : "actions", orderable:false, searchable:false },
                { "data" : "device_name" },
                { "data" : "operation_line" },
                { "data" : "assembly_line" },
                { "data" : "qsat" },
                { "data" : "no_of_pins" },
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
        });//end

        $('#saveLineBalance').on('submit', function(e){
            e.preventDefault();
            console.log('qwe');
        })

        $('#modalDataSAT').on('hidden.bs.modal', function(){
            dtProcessLists.clear().draw();
            $('#operationLine').val('').trigger('change');
            $('#assemblyLine').val('').trigger('change');
            modalCloseResetForm($('#formDataSAT')[0], 'modalDataSAT');
        });

        $('#modalLineBalance').on('hidden.bs.modal', function(){
            modalCloseResetForm($('#formLineBalance')[0], 'modalLineBalance');
        });
    });

    $(document).on('click', '.btnEditSAT', function(){
        let satId = $(this).data('id');
        $.ajax({
            type: "GET",
            url: "{{ route('get_sat_by_id') }}",
            data: {
                id: satId
            },
            dataType: "json",
            success: function (response) {
                $('#txtSATId').val(response.id)
                $('#txtDeviceName').val(response.device_name);
                $('#operationLine').val(response.operation_line).trigger('change');
                $('#assemblyLine').val(response.assembly_line).trigger('change');
                $('#txtNoPins').val(response.no_of_pins);
                $('#txtQSAT').val(response.qsat);
                // console.log(response.sat_process_details);
                
                dtProcessLists.rows.add(response.sat_process_details).draw();
                addingSAT();
                $('#modalDataSAT').modal('show');
            }
        });
    });

    $(document).on('click', '.btnProceedObs', function(){
        let satId = $(this).data('id');
        Swal.fire({
            title: "Do you want to proceed to observation?",
            text: "Editing the data will be disabled.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: "Save",
        }).then((result) => {
            /* Read more about isConfirmed, isDenied below */
            if (result.isConfirmed) {
                $.ajax({
                    type: "POST",
                    url: "{{ route('proceed_obs') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        id: satId
                    },
                    dataType: "json",
                    beforeSend: function () {
                        $(this).prop('disabled', true);
                    },
                    success: function (response) {
                        if (!response.result) {
                            toastr.error('Proceed failed! Please try again.')
                            return;
                        }
                        dtSat.draw();
                        toastr.success(response.msg);
                        $(this).prop('disabled', false);

                    },
                    error: function (xhr, status, error) {
                        $(this).prop('disabled', false);
                        console.log('xhr: ' + xhr + "\n" + "status: " + status + "\n" + "error: " + error);
                    }
                });
            }
        });
    });

    $(document).on('click', '.btnAddObs', function(){
        let satId = $(this).data('id');
        $.ajax({
            type: "GET",
            url: "{{ route('get_sat_by_id') }}",
            data: {
                id: satId
            },
            dataType: "json",
            success: function (response) {
                $('#txtSATId').val(response.id)
                $('#txtDeviceName').val(response.device_name);
                $('#operationLine').val(response.operation_line).trigger('change');
                $('#assemblyLine').val(response.assembly_line).trigger('change');
                $('#txtNoPins').val(response.no_of_pins);
                $('#txtQSAT').val(response.qsat);
                obsSAT();
                getOperatorList($('#selOperatorName'));
                drawProcessListTableForObservation(response.id);
                $('#modalDataSAT').modal('show');
            }
        });
    });

    $(document).on('click', '.btnDoneLineBal', function(){
        let satId = $(this).data('id');
        proceedForApproval(satId);
    });
</script>
@endsection