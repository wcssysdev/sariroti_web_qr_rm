$(function () {
    $(".approval_btn").click(function (e) { 
        e.preventDefault();
        $("#hidden_approval_status").val($(this).data("approval-type"))
    });
});