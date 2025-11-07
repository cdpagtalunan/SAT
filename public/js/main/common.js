$('.select2bs5').select2({
    // width: 'style',
    theme: 'bootstrap-5',
     minimumResultsForSearch: 0
});



const modalCloseResetForm = (formId, modal) => {
    formId.reset();
    console.log(`modal ${modal} closed`);
}

const getDropdownList = async (cboElement) => {
    $.ajax({
        type: "GET",
        url: "get_dropdown_list",
        // data: "data",
        dataType: "json",
        beforeSend: function(){
            cboElement.empty();
            cboElement.append('<option selected disabled>Loading...</option>');
        },
        success: function (response) {
            console.log(response);
            let options = "";
            if(response.length > 0){
                options += `<option selected disabled>-- Select an option --</option>`
                response.forEach(element => {
                    options += `<option value="${element.id}">${element.name}</option>`;
                });
                cboElement.html(options);
            }
        },
        error: function(xhr, status, error){
            cboElement.append('<option selected disabled>-- Error! Please reload.</option>');
            console.log('xhr: ' + xhr + "\n" + "status: " + status + "\n" + "error: " + error);
        }
    });
}


const handleValidatorErrors = (errors) => {
    
    document.querySelectorAll('div input').forEach(function(input) {
        input.classList.remove('is-invalid');
    });
    document.querySelectorAll('div select').forEach(function(input) {
        input.classList.remove('is-invalid');
    });
    document.querySelectorAll('div textarea').forEach(function(input) {
        input.classList.remove('is-invalid');
    });
    // Loop through each field in the errors object
    for (let field in errors) {
        if (errors.hasOwnProperty(field)) {
            // Extract the error messages for the field
            let fieldErrorMessage = errors[field];

            // Add invalid class & title validation
            if(field){
                document.querySelector(`[name="${field}"]`).classList.add('is-invalid');
                // document.querySelector(`[name="${field}"]`).classList.add('is-invalid');
                // document.querySelector(`[name="${field}"]`).classList.add('is-invalid');

            }
        }
    }
}

const addingSAT = () => {
    $('#txtDeviceName').prop('disabled', false)
    $('#operationLine').prop('disabled', false)
    $('#assemblyLine').prop('disabled', false)
    $('#txtNoPins').prop('disabled', false)
    $('#txtQSAT').prop('disabled', false)
    $('#addingSATProcessList').show();
    $('#btnSaveDataSAT').show();
    $('#obsSAT').hide();
}

const obsSAT = () => {
    $('#txtDeviceName').prop('disabled', true)
    $('#operationLine').prop('disabled', true)
    $('#assemblyLine').prop('disabled', true)
    $('#txtNoPins').prop('disabled', true)
    $('#txtQSAT').prop('disabled', true)
    $('#addingSATProcessList').hide();
    $('#btnSaveDataSAT').hide();
    $('#obsSAT').show();

}

const drawViewSatObservation = (satId) => {
    dtViewSatObservation = $("#tableViewSATObservation").DataTable({
        "processing": true,
        "serverSide": true,
        "ordering"  : false,
        "bDestroy"  : true,
        "info"      : false,
        "paging"    : false,
        "searching" : false,
        "ajax"      : {
            url : "dt_get_process_for_observation",
            data: function (param){
                param.id = satId;
            }
        },
        "fixedHeader": true,
        "columns"    : [
            // { "data" : "actions", orderable:false, searchable:false },
            { "data" : "attchmnt" },
            { "data" : "process_name" },
            // { "data" : "operator_name" },
            { "data" : "operator" },
            // { "data" : "obs_1" },
            // { "data" : "obs_2" },
            // { "data" : "obs_3" },
            // { "data" : "obs_4" },
            // { "data" : "obs_5" },
            { "data" : "obs1" },
            { "data" : "obs2" },
            { "data" : "obs3" },
            { "data" : "obs4" },
            { "data" : "obs5" },
            { "data" : "observed_time" },
            { "data" : "allowance" },
            { "data" : "nt" },
            { "data" : "st" },
            { "data" : "uph" },
        ],
        "columnDefs": [
            {"className": "dt-center", "targets": "_all"},
            {
                "targets"       : [1,2,3,4,5,6],
                "data"          : null,
                "defaultContent": "--"
            },
        ],
        'drawCallback': function( settings ) {
            let dtApi = this.api();
            let data = dtApi.data();
            let totalNt = 0;
            let totalSt = 0;
            let qsat = $('#txtQsatView').val();
            
            data.each(function(rowData, index) {
                totalNt =parseFloat(totalNt) + parseFloat(rowData.nt);
                if(rowData.st){
                    totalSt =parseFloat(totalSt) + parseFloat(rowData.st);
                }

            });


            let roundedUpNt = totalNt.toFixed(2);
            let roundedUpSt = totalSt.toFixed(2);   
            $('#totalNormalTimeView').html(roundedUpNt);
            if(parseFloat(roundedUpSt) > parseFloat(qsat)){
                $('#totalStandardTimeView').addClass('bg-danger');
                $('#txtQsatView').addClass('bg-danger');
            }
            else{
                $('#totalStandardTimeView').addClass('bg-success');
                $('#txtQsatView').addClass('bg-success');
            }
            $('#totalStandardTimeView').html(roundedUpSt);
        }
    });

    dtViewLineBalance = $("#tableViewLineBalance").DataTable({
        "processing": true,
        "serverSide": true,
        "paging"    : false,
        "info"      : false,
        "ordering"  : false,
        "bDestroy"  : true,
        "searching" : false,
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
            data.each(function(rowData, index) {
                sumStationSAT = parseFloat(sumStationSAT) + parseFloat(rowData.nt);
            });
            let roundedUp = sumStationSAT.toFixed(2);
            $('#TtlStationSatView').html(roundedUp);

            let totalOperators = 0;
            let highestTact = 0;

            $('#tableViewLineBalance tbody tr').each(function() {
                let cellValue = parseFloat($(this).find('td').eq(2).text()) || 0;
                totalOperators += cellValue;

                let tactVal = parseFloat($(this).find('td').eq(3).text()) || 0;
                if (tactVal > highestTact) {
                    highestTact = tactVal;
                }
               
            });
            assySAT = highestTact * totalOperators;
            lineBalanceValue = (parseFloat($('#TtlStationSatView').text()) / parseFloat(assySAT) ) * 100;
            outputPerHour = 3600 / highestTact;
            
            $('#assySatView').text(assySAT.toFixed(2))
            $('#lineBalView').text(lineBalanceValue.toFixed(2))
            if(lineBalanceValue < 80){
                
                $('#lineBalView').parent().addClass('text-danger')
            }
            else{
                $('#lineBalView').parent().addClass('text-success')
            }
            $('#outputPerHrView').text(outputPerHour.toFixed(2))
            $('#ttlNoOperatorView').text(totalOperators);
        }
    });//end of 
}