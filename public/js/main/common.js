$('.select2bs5').select2({
    // width: 'style',
    theme: 'bootstrap-5'
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
