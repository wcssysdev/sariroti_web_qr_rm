$(function () {

    $('#doc_type_select2').select2({
        placeholder: "Choose Document Type"
    });

   
    $(document).on("submit", "#form", function (e) {
        if ($("#doc_number_select2").val() == null || $("#doc_number_select2").val() == "") {
            alert("Document Number is Required")
            e.preventDefault();
        }
        if ($("#doc_type_select2").val() == null || $("#doc_type_select2").val() == "") {
            alert("Document Type is Required")
            e.preventDefault();
        }
    })
});