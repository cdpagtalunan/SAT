$(document).ready(function () {
    $('#btnAddDataForSAT').on('click', function () {
        $('#modalDataSAT').modal('show');
        addingSAT();
    });

    $('#btnAddProcessList').on('click', function (e) {
        e.preventDefault();
        let data = {
            data        : "",
            process_name: "*",
            allowance   : "*",
        }
        // arrayProcess.push(data);
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
            if (value === '') {
                console.log('data is empty');
            }
            // Validate float
            else if (isNaN(value) || !/^\d+(\.\d+)?$/.test(value)) {
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

    $('#btnSaveDataSAT').on('click', function (e) {
        e.preventDefault();
        $('#formDataSAT').submit();
    })
    $('#formDataSAT').on('submit', function (e) {
        e.preventDefault();
        let process_list = [];
        let error = false;
        dtProcessLists.rows().every(function (rowIdx, tableLoop, rowLoop) {
            let rowNode = this.node();
            // Get the HTML of the "Process" cell (column index 1)
            let processCellHtml = $(rowNode).find('td').eq(1).html();
            // Get the HTML of the "Allowance" cell (column index 2)
            let allowanceCellHtml = $(rowNode).find('td').eq(2).html();

            if (processCellHtml === '' || processCellHtml === 'Enter value') {
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
            process_list.push({
                process_name: processCellHtml,
                allowance: allowanceCellHtml
            });
        });

        // Call the save function here with the data object
        if (!error) {
            let data = $.param({
                'process_list': process_list
            }) + "&" + $(this).serialize();
            saveDataSAT(data);
        }
    })

    // Placeholder removal / restore
    $('#tableSATObservation').on('focus', '[contenteditable="true"]', function () {
        if ($(this).hasClass('placeholder-cell')) {
            $(this).text('').removeClass('placeholder-cell');
        }
    });

    $('#tableSATObservation').on('blur', '[contenteditable="true"]', function () {
        // Restore placeholder if empty
        if ($(this).text().trim() === '') {
            $(this).addClass('placeholder-cell').text('Enter value');
        }
    });

    $('#btnSaveLineBalance').on('click', function(e){
        e.preventDefault();
        let data = [];
        let result = true;
        dtLineBalance.rows().every(function (rowIdx, tableLoop, rowLoop) {
            let rowNode = this.node();
            data.push( {
                'satProcessId' : $(rowNode).attr('id'),
                'noOfOperator'  : $(rowNode).find('td').eq(2).text()
            });
        });

        data.forEach(data => {
            if(data.noOfOperator == 'Enter value' || data.noOfOperator == ''){
                swal.fire({
                    title: "Error",
                    text: "Please enter a valid number for No. of Operators.",
                    icon: "error"
                });
                result = false;
                return;
            }
        });

        if(result){
            saveLineBalance(data);
        }
    })
});

/**
 * *Process Observation
 */
$(document).on('click', '.btnAddProcessObs', function(e){
     e.preventDefault();

    let row = $(this).closest('tr');
    let cells = row.find('td');
    let processId = $(this).data('id');
    
    let selectHtml = `
        <select class="form-control form-control-sm w-auto select2bs5" id="selOperatorName">
        </select>
    `;
    cells.eq(2).html(selectHtml);
    cells.eq(2).find('select').select2({
        theme: 'bootstrap-5',
        width: '100%',
        dropdownParent: cells.eq(2), // important if inside DataTables or modals
        minimumResultsForSearch: 0
    });

    // Editable columns range: 3 to 7
    for (let i = 3; i <= 7; i++) {
        let cell = cells.eq(i);
        cell.attr('contenteditable', 'true');
        if (cell.text().trim() === '--') {
            cell.addClass('placeholder-cell')
                .text('Enter value');
        }
    }

    $('#selOperatorName').html(operatorListSAT);

    // Show the process observation buttons
    row.find('#divButtonProcessObs').removeClass('d-none');
    $(this).addClass('d-none');

});

$(document).on('click', '.btnCancel', function(e){
   
     e.preventDefault();

    let row = $(this).closest('tr');
    let cells = row.find('td');
    let cellSelect = cells.eq(2);
    cellSelect.attr('contenteditable', 'false')
        .addClass('placeholder-cell')
        .text('--');

    // Editable columns range: 3 to 7
    for (let i = 3; i <= 7; i++) {
        let cell = cells.eq(i);
        cell.attr('contenteditable', 'false')
            .addClass('placeholder-cell')
            .text('--');
    }

    // Toggle button visibility
    row.find('.btnAddProcessObs').removeClass('d-none');
    row.find('#divButtonProcessObs').addClass('d-none');
});

$(document).on('click', '.btnSaveProcessObs', function(e){
    e.preventDefault();
    let processId = $(this).data('id');
    let row = dtSatObservation.row($(this).closest('tr'));
    if($('#selOperatorName').val() == null){
        Swal.fire({
            title: 'Error!',
            text: 'Please select operator',
            icon: "error"
        });
        return
    }
    row.every(function (rowIdx, tableLoop, rowLoop) {
        let rowNode = this.node();
        
        if($(rowNode).find('td').eq(3).html() == 'Enter value'){
            Swal.fire({
                title: 'Error!',
                text: 'Please enter observation',
                icon: "error"
            });
            return
        }
        dataToSave = {
            id      : processId,
            // operator: sessionEmpNo,
            operator: $('#selOperatorName').val(),
            obs1    : $(rowNode).find('td').eq(3).html() == 'Enter value' ? '': $(rowNode).find('td').eq(3).html(),
            obs2    : $(rowNode).find('td').eq(4).html() == 'Enter value' ? '': $(rowNode).find('td').eq(4).html(),
            obs3    : $(rowNode).find('td').eq(5).html() == 'Enter value' ? '': $(rowNode).find('td').eq(5).html(),
            obs4    : $(rowNode).find('td').eq(6).html() == 'Enter value' ? '': $(rowNode).find('td').eq(6).html(),
            obs5    : $(rowNode).find('td').eq(7).html() == 'Enter value' ? '': $(rowNode).find('td').eq(7).html(),
            _token  : token
        };
    });

    saveProcessObs(dataToSave);
});

$(document).on('click', '.btnDoneObs', function(){
    let satId = $(this).data('id');
    Swal.fire({
        title: "Are you sure?",
        text: "This will proceed for Line Balance",
        showCancelButton: true,
        confirmButtonText: "Yes",
        icon: 'question',
    }).then((result) => {
        /* Read more about isConfirmed, isDenied below */
        if (result.isConfirmed) {
            $.ajax({
                type: "POST",
                url: "done_obs",
                data: {
                    sat_id: satId,
                    _token: token
                },
                dataType: "json",
                beforeSend: function () {
                    $('.btnDoneObs').prop('disabled', false);
                },
                success: function (response) {
                    if (!response.result) {
                        toastr.error('Something went wrong. Please contact ISS!');
                        return;
                    }
                    toastr.success('Validation Complete!');
                    dtSat.draw();
                    $('.btnDoneObs').prop('disabled', false);
                },
                error: function (xhr, status, error) {
                    if (xhr.status == 409) {
                        Swal.fire({
                            title: 'Error!',
                            text: `${xhr.responseJSON.msg}`,
                            icon: "error"
                        });
                    }
                    $('.btnDoneObs').prop('disabled', false);

                    console.log('xhr: ' + xhr + "\n" + "status: " + status + "\n" + "error: " + error);
                }
            });
        }
    });
    
});
/**
 * !End Process Observation
 */


/**
 * *Line Balance
 */

$(document).on('click', '.btnAddLineBalance', function(){
    let satId = $(this).data('id');
    $.ajax({
        type: "GET",
        url: "get_sat_by_id",
        data: {
            id: satId
        },
        dataType: "json",
        success: function (response) {
            console.log(response);
            $('#lbDeviceName').val(response.device_name);
            $('#lbOperationLine').val(response.operation_line);
            $('#lbAssemblyLine').val(response.assembly_line);
            $('#lbNoOfPins').val(response.no_of_pins);
            $('#satHeaderId').val(response.id);
            drawProcessListTableForLineBalance(response.id);
            $('#modalLineBalance').modal('show');
        }
    });
});
/**
 * !End Line Balance
 */

/**
 * Fetches dropdown options for each specified select element via AJAX.
 * Populates each dropdown (by id) with a default "-- SELECT --" option and the items returned from the server.
 * 
 * @param {Array} arrayParam - Array of dropdown ids to populate (e.g., ['assemblyLine', 'operationLine']).
 */
const getDropdownData = (arrayParam) => {
    $.ajax({
        type    : "GET",
        url     : "get_dropdown_data",
        data    : { arrayParam: arrayParam },
        dataType: "json",
        success : function (response) {
            let option = "";
            arrayParam.forEach(function(item){
                console.log(item);
                option = "<option value='' selected disabled>-- SELECT --</option>";
                response[item].forEach(function(options){
                    option += `<option value="${options.name}">${options.name}</option>`;
                })
                $(`#${item}`).html(option);
            })
        },
        error: function(xhr, status, error){
            console.log('xhr: ' + xhr + "\n" + "status: " + status + "\n" + "error: " + error);
        }
    });
}

const saveDataSAT = (data) => {
    $.ajax({
        type      : "POST",
        url       : "save_sat",
        data      : data,
        dataType  : "json",
        beforeSend: function(){
            $('#btnSaveDataSAT').prop('disabled', true);
        },
        success: function (response) {
            if(!response.result){
                toastr.error('Saving Failed. Please try again.');
                return;
            }
            toastr.success(response.msg);
            dtSat.draw();
            $('#btnSaveDataSAT').prop('disabled', false);
            $('#modalDataSAT').modal('hide');
        },
        error: function(xhr, status, error){
              // console.log(xhr.status);
            if(xhr.status == 422){
                handleValidatorErrors(xhr.responseJSON.errors)
            }
            $('#btnSaveDataSAT').prop('disabled', false);
            console.log('xhr: ' + xhr + "\n" + "status: " + status + "\n" + "error: " + error);
        }
    });
}

const drawProcessListTableForObservation = (satId) => {
    dtSatObservation = $("#tableSATObservation").DataTable({
        "processing": true,
        "serverSide": true,
        "ordering"  : false,
        "bDestroy"  : true,
        "info"      : false,
        "paging"    : false,
        "ajax"      : {
            url : "dt_get_process_for_observation",
            data: function (param){
                param.id = satId;
            }
        },
        "fixedHeader": true,
        "columns"    : [
            { "data" : "actions", orderable:false, searchable:false },
            { "data" : "process_name" },
            { "data" : "operator_name" },
            { "data" : "obs_1" },
            { "data" : "obs_2" },
            { "data" : "obs_3" },
            { "data" : "obs_4" },
            { "data" : "obs_5" },
            { "data" : "observed_time" },
            { "data" : "allowance" },
            { "data" : "nt" },
            { "data" : "st" },
            { "data" : "uph" },
        ],
        "columnDefs": [
            {"className": "dt-center", "targets": "_all"},
            {
                "targets"       : [2,3,4,5,6,7],
                "data"          : null,
                "defaultContent": "--"
            },
        ],
        'drawCallback': function( settings ) {
            let dtApi = this.api();
            let data = dtApi.data();
            let totalNt = 0;
            let totalSt = 0;
                console.log(data);
            
            data.each(function(rowData, index) {
                totalNt =parseFloat(totalNt) + parseFloat(rowData.nt);
                if(rowData.st){
                    totalSt =parseFloat(totalSt) + parseFloat(rowData.st);
                }

            });


            let roundedUpNt = totalNt.toFixed(2);
            let roundedUpSt = totalSt.toFixed(2);   
            $('#totalNormalTime').html(roundedUpNt);
            $('#totalStandardTime').html(roundedUpSt);
        }
    });//end of dataTableDevices
}

const saveProcessObs = (data) => {
    $.ajax({
        type: "POST",
        url: "save_process_obs",
        data: data,
        dataType: "json",
        beforeSend: function(){
            $('.btnSaveProcessObs').prop('disabled', true);
        },
        success: function (response) {
            if(response.result){
                toastr.success('Successfully Saved!');
                drawProcessListTableForObservation($('#txtSATId').val())
            }
            else{
                toastr.error('Something went wrong. Please call ISS!');
            }

            $('.btnSaveProcessObs').prop('disabled', false);

        },
        error: function(xhr, status, error){
            if(xhr.status == 422){
                toastr.error('Observation data should contain numbers only.')
            }
            toastr.error('Something went wrong. Please call ISS!');
            console.log('xhr: ' + xhr + "\n" + "status: " + status + "\n" + "error: " + error);
            $('.btnSaveProcessObs').prop('disabled', false);

        }
    });
}

const drawProcessListTableForLineBalance = (satId) => {
    dtLineBalance = $("#tableLineBalance").DataTable({
        "processing" : true,
        "serverSide" : true,
        "paging": false,
        "info" : false,
        "ordering": false,
        "bDestroy"  : true,
        "ajax" : {
            url: "dt_get_process_for_line_balance",
            data: function (param){
                param.id = satId;
            }
        },
        fixedHeader: true,
        "columns":[
            { "data" : "process_name" },
            { "data" : "nt" },
            { 
                "data" : "lb_no_operator", 
                createdCell: function(td) {
                    if (!$(td).text().trim()) {
                        $(td).addClass('placeholder-cell').text('Enter value');
                    }
                    $(td).attr('contenteditable', 'true')
                    .on('focus', function() {
                        if ($(td).hasClass('placeholder-cell')) {
                            $(td).text('').removeClass('placeholder-cell');
                            return;

                        }

                    })
                    .on('blur', function() {
                        let row = $(td).closest('tr');

                        if ($(td).text().trim() === '') {
                            $(td).addClass('placeholder-cell').text('Enter value');
                            row.find('td').eq(3).text('');
                            row.find('td').eq(4).text('');
                            return;
                        }
                        if (isNaN($(td).text().trim()) || !/^\d+(\.\d+)?$/.test($(td).text().trim())) {
                            // alert('Please enter a valid percentage (numbers only)');
                            Swal.fire({
                                // title: "The Internet?",
                                text: "Please enter a valid percentage (numbers only)",
                                icon: "error"
                            });
                            console.log('Please enter a valid percentage (numbers only)');
                            $(this).text('Enter value').addClass('placeholder-cell');
                            row.find('td').eq(3).text('');
                            row.find('td').eq(4).text('');
                            return;
                        }
                        let tact = 0;
                        let uph = 0;

                        // Force numeric format (2 decimals if needed)
                        let val = parseFloat($(td).text()) || 0;
                        noOfOperatorToFixed = val.toFixed(2);
                        $(td).text(noOfOperatorToFixed);

                        // Caluculate Tact on front end (Viewing Purpose only)
                        let rowStationSat =  row.find('td').eq(1).text();
                        tact = parseFloat(rowStationSat) / parseFloat(noOfOperatorToFixed);
                        tactDecimal = tact.toFixed(2)
                        row.find('td').eq(3).text(tactDecimal);
                        uph = 3600/parseFloat(tactDecimal);
                        row.find('td').eq(4).text(uph.toFixed(2));

                        // // Recalculate Total No. of Operators
                        // let totalOperators = 0;
                        // let highestTact = 0;
                        // let completeTact = true;
                        // $('#tableLineBalance tbody tr').each(function() {
                        //     let cellValue = parseFloat($(this).find('td').eq(2).text()) || 0;
                        //     totalOperators += cellValue;

                        //     if($(this).find('td').eq(3).text() == ''){
                        //         completeTact = false;
                        //         return;
                        //     }
                        //     let tactVal = parseFloat($(this).find('td').eq(3).text()) || 0;
                        //     if (tactVal > highestTact) {
                        //         highestTact = tactVal;
                        //     }
                        // });
                        // let assySAT = 0;
                        // let lineBalanceValue = 0;
                        // let outputPerHour = 0;
                        // // Calculate Assembly SAT and Line Balance Value if all tact is complete or doesnt have empty tact
                        // if(completeTact){
                        //     assySAT = highestTact * totalOperators;
                        //     lineBalanceValue = (parseFloat($('#TtlStationSat').text()) / parseFloat(assySAT) ) * 100;
                        //     outputPerHour = 3600 / highestTact;
                            
                        //     $('#txtLineBalVal').val(lineBalanceValue.toFixed(2))
                        //     $('#txtLineBalAssySAT').val(assySAT.toFixed(2))
                        //     $('#txtOutputPerHr').val(outputPerHour.toFixed(2))
                        // }
                        // $('#ttlNoOperator').text(totalOperators);

                        calculateLineBalance();
                    });
                },
            },
            { "data" : "tact" },
            { "data" : "lb_uph" },
        ],
        "columnDefs": [
            {"className": "dt-center", "targets": "_all"},
        ],
        'drawCallback': function( settings ) {
            let dtApi = this.api();
            let data = dtApi.data();
            let sumStationSAT = 0;
            console.log('drawcallback', data);
            data.each(function(rowData, index) {
                sumStationSAT = parseFloat(sumStationSAT) + parseFloat(rowData.nt);
                
            });
            let roundedUp = sumStationSAT.toFixed(2);
            $('#TtlStationSat').html(roundedUp);

            calculateLineBalance();

        }
    });//end of 
}

const saveLineBalance = (data) => {
    $.ajax({
        type: "POST",
        url: "save_line_balance",
        // data: params,
        data : {
            tbl_line_bal     : data,
            ppc_output_per_hr: $('#txtPPCOutputPerHr', $('#formLineBalance')).val(),
            sat_header_id    : $('#satHeaderId', $('#formLineBalance')).val(),
            _token           : token
        },
        dataType: "json",
        beforeSend: function(){
            $('#btnSaveLineBalance').prop('disabled', true);
        },
        success: function (response) {
            if(!response.result){
                toastr.error('Saving Failed. Please try again.');
                return;
            }
            toastr.success('Successfully Saved!');
            dtSat.draw();
            $('#modalLineBalance').modal('hide');
            $('#btnSaveLineBalance').prop('disabled', false);
        },
        error: function(xhr, status, error){
            if(xhr.status == 422){
                handleValidatorErrors(xhr.responseJSON.errors);
            }
            console.log('xhr: ' + xhr + "\n" + "status: " + status + "\n" + "error: " + error);
            toastr.error('Something went wrong. please call ISS!');
            $('#btnSaveLineBalance').prop('disabled', false);
        }
    });
}

const getOperatorList = (cboElement) => {
    $.ajax({
        type: "GET",
        url: "get_operator_list",
        // data: "data",
        dataType: "json",
        beforeSend: function(){
        },
        success: function (response) {
            operatorListSAT = "";
            operatorListSAT += `<option value="" selected disabled>--SELECT--</option>`;
            operatorListSAT += `<option value="N/A">N/A</option>`;
            response.forEach(element => {
                operatorListSAT += `<option value="${element.fullname}">${element.fullname}</option>`;
            });
        },
        error: function(xhr, status, error){
            console.log('xhr: ' + xhr + "\n" + "status: " + status + "\n" + "error: " + error);
        }
    });
}

const calculateLineBalance = () => {
    let totalOperators = 0;
    let highestTact = 0;
    let completeTact = true;
    $('#tableLineBalance tbody tr').each(function() {
        let cellValue = parseFloat($(this).find('td').eq(2).text()) || 0;
        totalOperators += cellValue;

        if($(this).find('td').eq(3).text() == ''){
            completeTact = false;
            return;
        }
        let tactVal = parseFloat($(this).find('td').eq(3).text()) || 0;
        if (tactVal > highestTact) {
            highestTact = tactVal;
        }
    });
    let assySAT = 0;
    let lineBalanceValue = 0;
    let outputPerHour = 0;
    // Calculate Assembly SAT and Line Balance Value if all tact is complete or doesnt have empty tact
    if(completeTact){
        assySAT = highestTact * totalOperators;
        lineBalanceValue = (parseFloat($('#TtlStationSat').text()) / parseFloat(assySAT) ) * 100;
        outputPerHour = 3600 / highestTact;
        $('#txtLineBalVal').val(lineBalanceValue.toFixed(2))
        $('#txtLineBalAssySAT').val(assySAT.toFixed(2))
        $('#txtOutputPerHr').val(outputPerHour.toFixed(2))
    }
    $('#ttlNoOperator').text(totalOperators);
}

const proceedForApproval = (satId) => {
    $.ajax({
        type: "POST",
        url: "proceed_for_approval",
        data: {
            _token : token,
            sat_id : satId
        },
        dataType: "json",
        beforeSend: function(){
            $('.btnDoneLineBal[data-id="${satId}"]').prop('disabled', true);
        },
        success: function (response) {
            $('.btnDoneLineBal[data-id="${satId}"]').prop('disabled', false);
        },
        error: function(xhr, status, error){
            console.log('xhr: ' + xhr + "\n" + "status: " + status + "\n" + "error: " + error);
            $('.btnDoneLineBal[data-id="${satId}"]').prop('disabled', false);
        }
    });
}