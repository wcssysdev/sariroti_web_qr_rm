$(function () {
    $('#exampleModalCenter').on('shown.bs.modal', function (e) {
        $('#batch_select2').select2({
            placeholder: "Choose Batch",
            allowClear: true
        });
        $('#sloc_select2').select2({
            placeholder: "Choose SLOC",
            allowClear: true
        });
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

                $('#sloc_select2').empty().trigger("change");
                $("#sloc_select2").select2({
                    placeholder: 'Choose SLOC',
                    data: response.sloc_data,
                    width: '100%'
                });
                $("#sloc_select2").val('').trigger('change');

                $("#material_select2").on("change", function(){
                    $.ajax({
                        type: "GET",
                        url: $("#form").data("get-material-status-url"),
                        data: {
                            "po_detail_id": $(this).val()
                        },
                        dataType: "JSON",
                        success: function (response) {
                            $("#sloc_select2").val(response.data.sloc).trigger('change');
                            $("#qty_left_input").val(response.data.qty_left)
                            $('#batch_select2').empty().trigger("change");
                            $('#batch_select2').select2({
                                placeholder: "Choose Batch",
                                allowClear: true,
                                data: response.data.batch_list,
                                width: '100%'
                            })
                            $("#batch_select2").val('').trigger('change');
                            $("#batch_select2").val(response.data.batch).trigger('change');
                        }
                    })
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

        $('#sloc_select2').empty().trigger("change");
        $("#sloc_select2").val('').trigger('change');

        $('#batch_select2').empty().trigger("change");
        $("#batch_select2").val('').trigger('change');

        $("#expired_date").val('');
        $("#gr_note").val('');
        $("#qty").val('');
        $("#qty_left_input").val('');
    })
})