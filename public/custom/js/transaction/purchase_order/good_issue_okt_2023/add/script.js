$(function () {
    $('#exampleModalCenter').on('shown.bs.modal', function (e) {
        $('#material_select2').select2({
            placeholder: "Choose Material",
            allowClear: true
        })
        
        $.ajax({
            type: "GET",
            url: $("#form").data("get-materials-url"),
            data: {
                "po_number": $("#po_number").val()
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
                $("#material_select2").on("change", function(){
                    let po_detail_id = $(this).val()
                    if (po_detail_id != null && po_detail_id != "" && typeof po_detail_id !== "undefined") {
                        $.ajax({
                            type: "GET",
                            url: $("#form").data("get-material-gr-url"),
                            data: {
                                "po_detail_id": po_detail_id
                            },
                            dataType: "JSON",
                            success: function (response) {
                                $('#gr_detail_select2').empty().trigger("change");
                                $('#gr_detail_select2').select2({
                                    placeholder: "Choose GR Detail",
                                    allowClear: true,
                                    data: response.data,
                                    width: '100%'
                                })
                                $("#gr_detail_select2").val('').trigger('change');
                                $("#gr_detail_select2").on("change", function(){
                                    let gr_detail_id = $(this).val()
                                    if (gr_detail_id != null && gr_detail_id != "" && typeof gr_detail_id !== "undefined") {
                                        $.ajax({
                                            type: "GET",
                                            url: $("#form").data("get-material-status-url"),
                                            data: {
                                                "gr_detail_id": gr_detail_id
                                            },
                                            dataType: "JSON",
                                            success: function (response) {
                                                $("#sloc_input").val(response.data.TR_GR_DETAIL_SLOC)
                                                $("#qty_left_input").val(response.data.TR_GR_DETAIL_LEFT_QTY+" "+response.data.TR_GR_DETAIL_BASE_UOM)
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

    $("#save_material_btn").click(function (e) { 
        e.preventDefault();
        let status = true
        if (typeof $("#material_select2").val() == "undefined" || $("#material_select2").val() == "") {
            status = false
            alert("Material Code is Required")
        }

        if (typeof $("#expired_date").val() == "undefined" || $("#expired_date").val() == "") {
            status = false
            alert("Expired Date is Required")
        }

        if (typeof $("#qty").val() == "undefined" || $("#qty").val() == "") {
            status = false
            alert("Qty is Required")
        }
        
        if (typeof $("#gi_note").val() == "undefined" || $("#gi_note").val() == "") {
            status = false
            alert("GI Note is Required")
        }

        if (status === true) {
            $('#form').attr('action',$("#form").data("save-material-url"))
            $('#form').submit()
        }
    })

    $(".delete_material_btn").click(function (e) { 
        e.preventDefault();
        $("#uniqid").val($(this).data("uniqid"))
        $('#form').attr('action',$("#form").data("delete-material-url"))
        $('#form').submit()
    })

    $('#exampleModalCenter').on('hidden.bs.modal', function () {
        $('#material_select2').empty().trigger("change");
        $("#material_select2").val('').trigger('change');

        $('#gr_detail_select2').empty().trigger("change");
        $("#gr_detail_select2").val('').trigger('change');

        $("#sloc_input").val('');
        $("#qty_left_input").val('');
        $("#batch_sap").val('');
        $("#expired_date").val('');
        $("#qty").val("");
        $("#gi_note").val("");
    })
})