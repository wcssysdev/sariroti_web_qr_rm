$(document).ready(function () {
    $("#submit_sync_sap").click(function (e) { 
        var formData = new FormData($("#form")[0]);
        var submit = {
            action: $("#form").attr("action"),
            redirect_url: $("#form").data("form-success-redirect"),
            data: formData,
            method: "POST",
            modal_title: "Are you sure to sync master data ?",
            modal_text: "Please make sure if you want to sync with new master data",
            modal_icon_type: "warning",
            modal_confirmation_text: "OK!",
            modal_confirmation_color: "#3e507e",
            modal_cancel_text: "Cancel",
            modal_res_success_text: "",
            modal_res_error_text: "",
            modal_approval_status: "1"
        };
        sweet_alert_submit(submit);
        e.preventDefault();
    });
    
    $("#submit_request_sap").click(function (e) {
        var formData = new FormData($("#form")[0]);
        var submit = {
            action: $("#form").attr("action2"),
            redirect_url: $("#form").data("form-success-redirect"),
            data: formData,
            method: "POST",
            // modal_input: "textarea",
            modal_title: "Do you want to request new master data ?",
            modal_text: "Please make sure if you want to request new master data to sap",
            modal_icon_type: "warning",
            modal_confirmation_text: "OK!",
            modal_confirmation_color: "#3e507e",
            modal_cancel_text: "Cancel",
            modal_res_success_text: "",
            modal_res_error_text: "",
            modal_approval_status: "2"
        };
        sweet_alert_submit(submit);
        e.preventDefault();
    });
});