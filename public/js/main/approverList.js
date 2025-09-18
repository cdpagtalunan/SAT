const getUserList = (cboElement) => {
    $.ajax({
        type: "GET",
        url: "get_user_approver",
        data: {
            fkDivision : [11,12],
            fkPosition : [12,16,52,53,80,97,124,10,8,7,4]
        },
        dataType: "json",
        beforeSend: function(){
            cboElement.empty();
        },
        success: function (response) {
            let options = "<option value='' selected disabled>--Select--</option>";
            response.forEach(element => {
                options += `<option value="${element.EmpNo}">${element.FirstName} ${element.LastName}</option>`;
            });
            cboElement.append(options);
        },
        error: function(xhr, status, error){
            console.log('xhr: ' + xhr + "\n" + "status: " + status + "\n" + "error: " + error);
        }
    });
}

const saveApprover = () => {
    $.ajax({
        type: "POST",
        url: "save_approver",
        data: $('#formApprover').serialize(),
        dataType: "json",
        beforeSend: function(){
            $('#btnSaveApprover').prop('disabled', true);
        },
        success: function (response) {
            if(response.result){
                dtApprovers.draw();
                toastr.success(response.msg);
                $('#modalApprovers').modal('hide');
            }
            $('#btnSaveApprover').prop('disabled', false);
        },
        error: function(xhr, status, error){
            $('#btnSaveApprover').prop('disabled', false);
             if(xhr.status == 422){
                toastr.error('Please input required fields!');
                handleValidatorErrors(xhr.responseJSON.errors)
            }
            console.log('xhr: ' + xhr + "\n" + "status: " + status + "\n" + "error: " + error);
        }
    });
}

const deleteApprover = (approverId) => {
    $.ajax({
        type: "POST",
        url: "delete_approver",
        data: {
            _token : token,
            ap_id : approverId
        },
        dataType: "json",
        success: function (response) {
            if(response.result){
                toastr.success(response.msg);
                dtApprovers.draw();
            }
        },
        error: function(xhr, status, error){
            toastr.error('xhr: ' + xhr + "\n" + "status: " + status + "\n" + "error: " + error);
        }
    });
}