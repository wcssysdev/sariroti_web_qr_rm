$(function () {
    $("#cost_center_select2").prop("disabled", true);
    $("#gl_acc_select2").prop("disabled", true);
    $("#expired_date_header").prop("disabled", true);
    $("#add_new_material_btn").hide();
    $("#add_new_material_btn").click(function (e) {
        e.preventDefault();
        if ($("#movement_select2").val() == null || $("#movement_select2").val() == "") {
            alert("Please choose movement code first");
        }
        else {
            if ($("#movement_select2").val() != "Y21") {
                $("#general_modal").modal('show');
            }
            else {
                $("#y21_modal").modal('show');
            }
        }
    });

    $('#general_modal').on('shown.bs.modal', function (e) {
        $('#material_select2').select2({
            placeholder: "Choose Material",
            allowClear: true
        })

        $('#gr_detail_select2').select2({
            placeholder: "Choose Material",
            allowClear: true
        })

        $.ajax({
            type: "GET",
            url: $("#form").data("get-materials-url"),
            data: {
            },
            dataType: "JSON",
            success: function (response) {
                $('#material_select2').empty().trigger("change");
                $("#material_select2").select2({
                    placeholder: 'Choose Material',
                    data: response.data,
                    width: '100%'
                });
                $("#material_select2").val('').trigger('change');

                $('#sloc_select2').empty().trigger("change");
                $("#sloc_select2").select2({
                    placeholder: 'Choose SLOC',
                    data: response.sloc_data,
                    width: '100%'
                });
                $("#sloc_select2").val('').trigger('change');
                $("#material_select2").on("change", function () {
                    if ($(this).val() != null && $(this).val() != "" && typeof $(this).val() != "undefined") {
                        $.ajax({
                            type: "GET",
                            url: $("#form").data("get-material-gr-url"),
                            data: {
                                "material_code": $(this).val(),
                                "movement_type": $("#movement_select2").val()
                            },
                            dataType: "JSON",
                            success: function (response) {
                                $('#gr_detail_select2').empty().trigger("change");
                                $('#gr_detail_select2').select2({
                                    placeholder: "Choose GR Detail",
                                    allowClear: true,
                                    data: response.data,
                                    width: '100%'
                                });
                                $("#gr_detail_select2").val('').trigger('change');

                                $("#gr_detail_select2").on("change", function () {
                                    if ($(this).val() != null && $(this).val() != "" && typeof $(this).val() != "undefined") {
                                        $.ajax({
                                            type: "GET",
                                            url: $("#form").data("get-material-status-url"),
                                            data: {
                                                "gr_detail_id": $(this).val()
                                            },
                                            dataType: "JSON",
                                            success: function (response) {
                                                $("#sloc_input").val(response.data.TR_GR_DETAIL_SLOC)
                                                if (typeof response.data.TR_GR_DETAIL_LEFT_QTY != "undefined" && typeof response.data.TR_GR_DETAIL_BASE_UOM != "undefined") {
                                                    $("#qty_left_input").val(response.data.TR_GR_DETAIL_LEFT_QTY + " " + response.data.TR_GR_DETAIL_BASE_UOM)
                                                }

                                                $("#batch_sap").val(response.data.TR_GR_DETAIL_SAP_BATCH)
                                                $("#expired_date").val(response.data.TR_GR_DETAIL_EXP_DATE)

                                            }
                                        })
                                    }
                                })
                            }
                        })
                    }
                })
            }
        })
    })

    $('#y21_modal').on('shown.bs.modal', function (e) {
        $('#batch_y21_select2').select2({
            placeholder: "Choose Batch",
            allowClear: true
        });
        $('#sloc_y21_from_select2').select2({
            placeholder: "Choose SLOC",
            allowClear: true
        });
        $('#sloc_y21_to_select2').select2({
            placeholder: "Choose SLOC",
            allowClear: true
        });
        $('#material_y21_select2').select2({
            placeholder: "Choose Material",
            allowClear: true
        })

        $.ajax({
            type: "GET",
            url: $("#form").data("get-materials-y21-url"),
            data: {
                "po_number": $("#po_number").val()
            },
            dataType: "JSON",
            success: function (response) {
                $('#material_y21_select2').empty().trigger("change");
                $("#material_y21_select2").select2({
                    placeholder: 'Choose Material',
                    data: response.data,
                    width: '100%'
                });
                $("#material_y21_select2").val('').trigger('change');

                $('#sloc_y21_from_select2').empty().trigger("change");
                $("#sloc_y21_from_select2").select2({
                    placeholder: 'Choose SLOC',
                    data: response.sloc_data,
                    width: '100%'
                });
                $("#sloc_y21_from_select2").val('').trigger('change');

                $('#sloc_y21_to_select2').empty().trigger("change");
                $("#sloc_y21_to_select2").select2({
                    placeholder: 'Choose SLOC',
                    data: response.sloc_data,
                    width: '100%'
                });
                $("#sloc_y21_to_select2").val('').trigger('change');

                $("#material_y21_select2").on("change", function () {
                    if ($(this).val() != null && $(this).val() != "" && typeof $(this).val() != "undefined") {
                        $.ajax({
                            type: "GET",
                            url: $("#form").data("get-material-batch-y21-url"),
                            data: {
                                "material_code": $(this).val()
                            },
                            dataType: "JSON",
                            success: function (response) {
                                $("#uom_text").html(`<b>BASE UOM: ${response.data.base_uom}</b>`)
                                $('#batch_y21_select2').empty().trigger("change");
                                $('#batch_y21_select2').select2({
                                    placeholder: "Choose Batch",
                                    allowClear: true,
                                    data: response.data.batch,
                                    width: '100%'
                                })
                                $("#batch_y21_select2").val('').trigger('change');
                                $("#batch_y21_select2").val(response.data.batch).trigger('change');
                            }
                        })
                    }
                })
            }
        })
    })

    $("#save_material_btn").click(function (e) {
        e.preventDefault();
        let sloc = $('select[name="TR_TP_DETAIL_SLOC"]').val();
        
        let status = true;
        if (typeof sloc == "undefined" || sloc == "" || sloc == '0' || sloc == null) {
            status = false;
            alert("Destination S.Loc is Required");
        }
        if (typeof $("#material_select2").val() == "undefined" || $("#material_select2").val() == "") {
            status = false
            alert("Material Code is Required");
        }

        if (typeof $("#expired_date").val() == "undefined" || $("#expired_date").val() == "") {
            status = false;
            alert("Expired Date is Required");
        }

        if (typeof $("#qty").val() == "undefined" || $("#qty").val() == "") {
            status = false
            alert("Qty is Required")
        }

        if (status === true) {
            $('#form').attr('action', $("#form").data("save-material-url"))
            $('#form').submit();
        }else{
            return false;
        }
    })

    $("#save_material_y21_btn").click(function (e) {
        e.preventDefault();
        let status = true
        if (typeof $("#material_y21_select2").val() == "undefined" || $("#material_y21_select2").val() == "") {
            status = false
            alert("Material Code is Required")
        }

        if (typeof $("#expired_date_y21").val() == "undefined" || $("#expired_date_y21").val() == "") {
            status = false
            alert("Expired Date is Required")
        }

        if (typeof $("#qty_y21").val() == "undefined" || $("#qty_y21").val() == "") {
            status = false
            alert("Posting Qty is Required")
        }

        if (typeof $("#sloc_y21_from_select2").val() == "undefined" || $("#sloc_y21_from_select2").val() == "") {
            status = false
            alert("SLOC from is Required")
        }

        if (typeof $("#sloc_y21_to_select2").val() == "undefined" || $("#sloc_y21_to_select2").val() == "") {
            status = false
            alert("SLOC to is Required")
        }

        if (status === true) {
            $('#form').attr('action', $("#form").data("save-material-y21-url"))
            $('#form').submit();
        }else{
            return false;
        }
    })

    // $(".delete_material_btn").click(function (e) {
    //     e.preventDefault();
    //     $("#uniqid").val($(this).data("uniqid"))
    //     $('#form').attr('action', $("#form").data("delete-material-url"))
    //     $('#form').submit()
    // })

    // $(".delete_material_y21_btn").click(function (e) {
    //     e.preventDefault();
    //     $("#uniqid").val($(this).data("uniqid"))
    //     $('#form').attr('action', $("#form").data("delete-material-y21-url"))
    //     $('#form').submit()
    // })

    $("#kt_datatable2").on("click", ".delete_material_btn", function(e){
        e.preventDefault();
        $("#uniqid").val($(this).data("uniqid"))
        $('#form').attr('action', $("#form").data("delete-material-url"))
        $('#form').submit()
    });

    $("#kt_datatable2").on("click", ".delete_material_y21_btn", function(e){
        e.preventDefault();
        $("#uniqid").val($(this).data("uniqid"))
        $('#form').attr('action', $("#form").data("delete-material-y21-url"))
        $('#form').submit()
    });

    $("#movement_select2").val('').trigger('change');
    $("#cost_center_select2").val('').trigger('change');
    $("#gl_acc_select2").val('').trigger('change');
    $("#movement_select2").on("change", function () {
        if ($(this).val() == "551" || $(this).val() == "Y21") {
            $("#expired_date_header").prop("disabled", false);
        }
        else {
            $("#expired_date_header").prop("disabled", true);
        }

        if ($(this).val() == "551") {
            $("#cost_center_select2").val('').trigger('change');
            $("#gl_acc_select2").val('').trigger('change');
            $("#cost_center_select2").prop("disabled", false);
            $("#gl_acc_select2").prop("disabled", false);
        }
        else {
            $("#cost_center_select2").val('').trigger('change');
            $("#gl_acc_select2").val('').trigger('change');
            $("#cost_center_select2").prop("disabled", true);
            $("#gl_acc_select2").prop("disabled", true);
        }

        if ($(this).val() != null && $(this).val() != "") {
            $("#add_new_material_btn").show();
        }
    })

    $("#movement_select2").val($("#movement_select2").data("mvt-selected")).trigger('change');
    $("#expired_date_header").val($("#expired_date_header").data("date-value"));
    $("#cost_center_select2").val($("#cost_center_select2").data("cc-selected")).trigger('change');
    $("#gl_acc_select2").val($("#gl_acc_select2").data("gl-selected")).trigger('change');

    $('#y21_modal').on('hidden.bs.modal', function (e) {
        $('#material_y21_select2').empty().trigger("change");
        $("#material_y21_select2").val('').trigger('change');

        $('#sloc_y21_from_select2').empty().trigger("change");
        $("#sloc_y21_from_select2").val('').trigger('change');

        $('#sloc_y21_to_select2').empty().trigger("change");
        $("#sloc_y21_to_select2").val('').trigger('change');

        $('#batch_y21_select2').empty().trigger("change");
        $("#batch_y21_select2").val('').trigger('change');

        $("#expired_date_y21").val('');
        $("#qty_y21").val('');
        $("#note_y21").val("");
    })

    $('#general_modal').on('hidden.bs.modal', function (e) {
        $('#material_select2').empty().trigger("change");
        $("#material_select2").val('').trigger('change');

        $('#gr_detail_select2').empty().trigger("change");
        $("#gr_detail_select2").val('').trigger('change');

        $('#sloc_select2').empty().trigger("change");
        $("#sloc_select2").val('').trigger('change');

        $("#batch_sap").val('');
        $("#expired_date").val('');
        $("#qty_left_input").val('');
        $("#qty").val('');
        $("#sloc_input").val('');

    })
})