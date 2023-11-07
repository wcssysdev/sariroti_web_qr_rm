$(document).ready(function () {
    $("#kt_datatable1,#kt_datatable2").on("click", "#delete_transaction_button", function(){
        var action = $(this).data("action");
        var redirect = $(this).data("form-success-redirect");
        var formData = new FormData($("#form")[0]);
        var submit = {
            action: action,
            redirect_url: redirect,
            data: formData,
            method: "GET",
            modal_title: "Are you sure to delete this data ?",
            modal_text: "Deleted data can't be restored anymore.",
            modal_icon_type: "warning",
            modal_confirmation_text: "YES, Delete this data",
            modal_confirmation_color: "#3e507e",
            modal_cancel_text: "No / Cancel",
            modal_res_success_text: "",
            modal_res_error_text: "",
            modal_approval_status: "1"
        };
        sweet_alert_submit(submit);
        e.preventDefault();
    });
});