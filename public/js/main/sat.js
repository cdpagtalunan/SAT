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
});


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
})

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
})
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
        ]

        // 'drawCallback': function( settings ) {
        //     let dtApi = this.api();
        // }
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
        },
        error: function(xhr, status, error){
            if(xhr.status == 422){
                toastr.error('Observation data should contain numbers only.')
            }
            console.log('xhr: ' + xhr + "\n" + "status: " + status + "\n" + "error: " + error);
        }
    });
}