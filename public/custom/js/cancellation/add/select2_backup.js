$(function () {

    $('#doc_type_select2').select2({
        placeholder: "Choose Document Type"
    });

    $('#sap_doc_gi_select2').select2({
        placeholder: "Choose Document Type"
    });

    $('#sap_doc_tp_select2').select2({
        placeholder: "Choose Document Type"
    });

    $("#doc_type_select2").on("change", function () {
        $.ajax({
            type: "GET",
            url: $("#form").data("get-doc-number"),
            data: {
                "doc_type": $(this).val()
            },
            dataType: "JSON",
            success: function (response) {
                $('#doc_number_select2').empty().trigger("change");
                $('#doc_number_select2').select2({
                    placeholder: "Choose Document Number",
                    allowClear: true,
                    data: response,
                    width: '100%'
                })
                $("#doc_number_select2").val('').trigger('change');

                $("#doc_number_select2").on("change", function () {
                    $.ajax({
                        type: "GET",
                        url: $("#form").data("get-doc-number-detail"),
                        data: {
                            "doc_number": $(this).val(),
                            "doc_type": $('#doc_type_select2').val()
                        },
                        dataType: "JSON",
                        success: function (response) {
                            if (response.type == "GR") {
                                $('#view_doc_btn').attr("href", $("#form").data("detail-gr-url") + "?gr_header_id=" + response.data.TR_GR_HEADER_ID)
                            }
                            else if (response.type == "GI") {
                                $('#view_doc_btn').attr("href", $("#form").data("detail-gi-url") + "?gi_header_id=" + response.data.TR_GI_SAPHEADER_ID)
                            }
                            else if (response.type == "TP") {
                                $('#view_doc_btn').attr("href", $("#form").data("detail-tp-url") + "?tp_header_id=" + response.data.TR_TP_HEADER_ID)
                            }
                        }
                    })
                })
            }
        })
    })

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