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