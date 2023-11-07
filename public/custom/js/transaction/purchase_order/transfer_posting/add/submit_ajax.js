$(function () {
    $("#submit_btn").click(function (e) {
        var formData = new FormData($("#form")[0]);
        var submit = {
            action: $("#form").attr("action"),
            redirect_url: $("#form").data("form-success-redirect"),
            data: formData,
            method: "POST",
            modal_title: "Are You Sure ?",
            modal_text: "Please check the form before submit",
            modal_icon_type: "warning",
            modal_confirmation_text: "Yes",
            modal_cancel_text: "Cancel",
            modal_res_success_text: "Data Successfully Saved",
            modal_res_error_text: ""
        };
        sweet_alert_submit(submit);
        e.preventDefault();
    });
});