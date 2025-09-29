const getSatDetails = (satId) => {
    $.ajax({
        type: "get",
        url: "get_sat_details",
        data: {
            sat_id : satId
        },
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

const satProcessApproval = (approvalId, approverType) => {
    $.ajax({
        type: "POST",
        url: "approve_sat",
        data: {
            _token       : token,
            approval_id  : approvalId,
            approval_type: approverType
        },
        dataType: "json",
        beforeSend: function(){
            $('.btnApprove').prop('disabled', true);
        },
        success: function (response) {
            $('.btnApprove').prop('disabled', false);
            if(response.result){
                toastr.success('SAT Approved!');
                dtSatApproval.draw();
            }
        },
        error: function(xhr, status, error){
            $('.btnApprove').prop('disabled', false);
            console.log('xhr: ' + xhr + "\n" + "status: " + status + "\n" + "error: " + error);
        }
    });
}