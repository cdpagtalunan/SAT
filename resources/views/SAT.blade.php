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
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text">Device Name</span>
                                <input type="text" class="form-control" id="">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text">Operations Line</span>
                                <select name="operation_line" id="operationLine" class="form-control select2bs5"></select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text">Assembly Line</span>
                                <select name="assembly_line" id="assemblyLine" class="form-control select2bs5"></select>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
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
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-sm btn-success" id="btnSaveDataSAT">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
@section('js_content')
<script>
    let dtProcessLists;
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
        dtProcessLists = $('#tableProcessLists').DataTable({
            "info" : false,
            "processing" : true,
            "searching" : false,
            "paging" : false,
            "columns": [
                { 
                    "data": "data",
                    render: function (data, type, row, meta) {
                        return `<button class="btn btn-sm btn-danger" title="Delete Process" onclick="dtProcessLists.row(${meta.row}).remove().draw();">
                            <i class="fa-solid fa-trash"></i>
                        </button>`;
                    },
                },
                { "data": "data1" },
                { "data": "data2" },
            ],
            "columnDefs": [
                {
                    targets: [1, 2], // which columns are editable (0-based index)
                    createdCell: function (td, cellData, rowData, row, col) {
                        // $(td).attr('contenteditable', 'true');
                        let headerText = $('#tableProcessLists thead th').eq(col).text().trim();
                        if (cellData === "*" || !cellData) {
                            $(td)
                                .attr('contenteditable', 'true')
                                .attr('title', `Enter ${headerText}`)
                                .addClass('placeholder-cell')
                                .text('Enter value');
                        }
                    }
                },
                { "className": "dt-center", "targets": [0]},
            ]
        })

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

        $('#btnAddProcessList').on('click', function(e){
            e.preventDefault();
            let data = {
                data: "",
                data1: "*",
                data2 : "*",
            }
	        arrayProcess.push(data);
            dtProcessLists.rows.add([data]).draw();
        });
        // Placeholder removal / restore
        $('#tableProcessLists').on('focus', '[contenteditable="true"]', function () {
            if ($(this).hasClass('placeholder-cell')) {
                $(this).text('').removeClass('placeholder-cell');
            }
        });

        /**
         * Handles validation and placeholder logic for editable cells in the Allowance column.
         * - On blur, checks if the value is empty or not a valid number.
         * - Shows an error using Swal if the input is invalid.
         * - Restores the placeholder text and style if the cell is left empty.
         */
        $('#tableProcessLists').on('blur', '[contenteditable="true"]', function () {
            var cell = dtProcessLists.cell(this);
            var columnIndex = cell.index().column;
            // Allowance column index (0-based) is 2
            if (columnIndex === 2) {
                var value = $(this).text().trim();
                if(value === ''){
                    console.log('data is empty');
                }
                // Validate float
                else if ( isNaN(value) || !/^\d+(\.\d+)?$/.test(value)) {
                    // alert('Please enter a valid percentage (numbers only)');
                    Swal.fire({
                        // title: "The Internet?",
                        text: "Please enter a valid percentage (numbers only)",
                        icon: "error"
                    });
                    console.log('Please enter a valid percentage (numbers only)');
                    $(this).text('Enter value').addClass('placeholder-cell');
                    return;
                }
            }

            // Restore placeholder if empty
            if ($(this).text().trim() === '') {
                $(this).addClass('placeholder-cell').text('Enter value');
            }
        });

        $('#btnSaveDataSAT').on('click', function(e){
            e.preventDefault();
            let data = {
                device_name: $('#deviceName').val(),
                operation_line: $('#operationLine').val(),
                assembly_line: $('#assemblyLine').val(),
                _token: '{{ csrf_token() }}',
                process_list: []
            };
            let error = false;
            dtProcessLists.rows().every(function(rowIdx, tableLoop, rowLoop){
                let rowNode = this.node();
                // Get the HTML of the "Process" cell (column index 1)
                let processCellHtml = $(rowNode).find('td').eq(1).html();
                // Get the HTML of the "Allowance" cell (column index 2)
                let allowanceCellHtml = $(rowNode).find('td').eq(2).html();

                if(processCellHtml === '' || processCellHtml === 'Enter value') {
                    // If the process cell is empty, skip this row
                    swal.fire({
                        title: "Error",
                        text: "Please fill in all required fields.",
                        icon: "error"
                    });
                    error = true;
                    return;
                }

                // Example: push the HTML into your data object
                data.process_list.push({
                    process: processCellHtml,
                    allowance: allowanceCellHtml
                });
            });

            // Call the save function here with the data object
            console.log('btnSaveDataSAT', data);
            if(!error) {
                saveDataSAT(data);
            }
        })

        
    });
</script>
@endsection