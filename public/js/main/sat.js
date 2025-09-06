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

     $('#modalDataSAT').on('hidden.bs.modal', function(){
        dtProcessLists.clear().draw();
        $('#operationLine').val('').trigger('change');
        $('#assemblyLine').val('').trigger('change');
        modalCloseResetForm($('#formDataSAT')[0], 'modalDataSAT');
    });

    
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
        dtLineBalance.rows().every(function (rowIdx, tableLoop, rowLoop) {
            console.log(rowIdx);
            let rowNode = this.node();
            console.log('rowNode', rowNode);
            console.log('tr ID: ',$(rowNode).attr('id'));
            console.log('no of operator: ',$(rowNode).find('td').eq(2).text());
            data.push( {
                'satProcessId' : $(rowNode).attr('id'),
                'noOfOperator'  : $(rowNode).find('td').eq(2).text()
            });
        });

        console.log('data', data);
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

    // Set session name
    cells.eq(2).html(sessionName);

    // Editable columns range: 3 to 7
    for (let i = 3; i <= 7; i++) {
        let cell = cells.eq(i);
        cell.attr('contenteditable', 'true');
        if (cell.text().trim() === '--') {
            cell.addClass('placeholder-cell')
                 .text('Enter value');
        }
    }

    // Show the process observation buttons
    row.find('#divButtonProcessObs').removeClass('d-none');
    $(this).addClass('d-none');
    // e.preventDefault();
    // // console.log($(this).closest('tr').find('#divButtonProcessObs'));
    // let processId = $(this).data('id');
    // let tds = $(this).closest('tr').find('td');
    // tds.eq(2).html(sessionName)
    // if(tds.eq(3).text() == '--'){
    //     tds.eq(3).attr('contenteditable', 'true');
    //     tds.eq(3).addClass('placeholder-cell').text('Enter value')
    // }
    // if(tds.eq(4).text() == '--'){
    //     tds.eq(4).attr('contenteditable', 'true');
    //     tds.eq(4).addClass('placeholder-cell').text('Enter value')
    // }
    // if(tds.eq(5).text() == '--'){
    //     tds.eq(5).attr('contenteditable', 'true');
    //     tds.eq(5).addClass('placeholder-cell').text('Enter value')
    // }
    // if(tds.eq(6).text() == '--'){
    //     tds.eq(6).attr('contenteditable', 'true');
    //     tds.eq(6).addClass('placeholder-cell').text('Enter value')
    // }
    // if(tds.eq(7).text() == '--'){
    //     tds.eq(7).attr('contenteditable', 'true');
    //     tds.eq(7).addClass('placeholder-cell').text('Enter value')
    // }
    // $(this).closest('tr').find('#divButtonProcessObs').removeClass('d-none');
    // $(this).addClass('d-none')
});

$(document).on('click', '.btnCancel', function(e){
    // e.preventDefault();
    // let tds = $(this).closest('tr').find('td');
    // if(tds.eq(3).text() == 'Enter value'){
    //     tds.eq(3).attr('contenteditable', 'false');
    //     tds.eq(3).addClass('placeholder-cell').text('--')
    // }
    // if(tds.eq(4).text() == 'Enter value'){
    //     tds.eq(4).attr('contenteditable', 'false');
    //     tds.eq(4).addClass('placeholder-cell').text('--')
    // }
    // if(tds.eq(5).text() == 'Enter value'){
    //     tds.eq(5).attr('contenteditable', 'false');
    //     tds.eq(5).addClass('placeholder-cell').text('--')
    // }
    // if(tds.eq(6).text() == 'Enter value'){
    //     tds.eq(6).attr('contenteditable', 'false');
    //     tds.eq(6).addClass('placeholder-cell').text('--')
    // }
    // if(tds.eq(7).text() == 'Enter value'){
    //     tds.eq(7).attr('contenteditable', 'false');
    //     tds.eq(7).addClass('placeholder-cell').text('--')
    // }

    // $(this).closest('tr').find('.btnAddProcessObs').removeClass('d-none');
    // $(this).closest('tr').find('#divButtonProcessObs').addClass('d-none')

     e.preventDefault();

    let row = $(this).closest('tr');
    let cells = row.find('td');

    // Editable columns range: 3 to 7
    for (let i = 3; i <= 7; i++) {
        let cell = cells.eq(i);
        if (cell.text().trim() === 'Enter value') {
            cell.attr('contenteditable', 'false')
                 .addClass('placeholder-cell')
                 .text('--');
        }
    }

    // Toggle button visibility
    row.find('.btnAddProcessObs').removeClass('d-none');
    row.find('#divButtonProcessObs').addClass('d-none');
});

$(document).on('click', '.btnSaveProcessObs', function(e){
    e.preventDefault();
    let processId = $(this).data('id');
    let row = dtSatObservation.row($(this).closest('tr'));
    row.every(function (rowIdx, tableLoop, rowLoop) {
        let rowNode = this.node();
        dataToSave = {
            id      : processId,
            operator: sessionEmpNo,
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
            { "data" : "rapidx_user_details.name" },
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
            data.each(function(rowData, index) {
                totalNt =parseFloat(totalNt) + parseFloat(rowData.nt);
                totalSt =parseFloat(totalSt) + parseFloat(rowData.st);
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
        },
        success: function (response) {
            if(response.result){
                toastr.success('Successfully Saved!');
                drawProcessListTableForObservation($('#txtSATId').val())
            }
            else{
                toastr.error('Something went wrong. Please call ISS!');

            }
        },
        error: function(xhr, status, error){
            if(xhr.status == 422){
                toastr.error('Observation data should contain numbers only.')
            }
            toastr.error('Something went wrong. Please call ISS!');
            console.log('xhr: ' + xhr + "\n" + "status: " + status + "\n" + "error: " + error);
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

                        // Recalculate Total No. of Operators
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

                        // Calculate Assembly SAT and Line Balance Value if all tact is complete or doesnt have empty tact
                        if(completeTact){
                            assySAT = highestTact * totalOperators;
                            lineBalanceValue = (parseFloat($('#TtlStationSat').text()) / parseFloat(assySAT) ) * 100;
                            $('#txtLineBalVal').val(lineBalanceValue.toFixed(2))
                            $('#txtLineBalAssySAT').val(assySAT.toFixed(2))
                        }
                        $('#ttlNoOperator').text(totalOperators);
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
                sumStationSAT =parseFloat(sumStationSAT) + parseFloat(rowData.st);
                
            });
            let roundedUp = sumStationSAT.toFixed(2);
            $('#TtlStationSat').html(roundedUp);
        }
    });//end of 
}