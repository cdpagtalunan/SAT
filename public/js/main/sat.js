/**
 * Fetches dropdown options for each specified select element via AJAX.
 * Populates each dropdown (by id) with a default "-- SELECT --" option and the items returned from the server.
 * 
 * @param {Array} arrayParam - Array of dropdown ids to populate (e.g., ['assemblyLine', 'operationLine']).
 */
const getDropdownData = (arrayParam) => {
    $.ajax({
        type: "GET",
        url: "get_dropdown_data",
        data: { arrayParam: arrayParam },
        dataType: "json",
        success: function (response) {
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
        type: "POST",
        url: "save_sat",
        data: data,
        dataType: "json",
        beforeSend: function(){
        },
        success: function (response) {
        },
        error: function(xhr, status, error){
            console.log('xhr: ' + xhr + "\n" + "status: " + status + "\n" + "error: " + error);
        }
    });
}