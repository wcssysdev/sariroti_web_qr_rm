$(document).ready(function () {
    var table = $('#kt_datatable2').DataTable({
        aaSorting: [],
        responsive: true,
        dom: `<'row'<'col-sm-6 text-left'f><'col-sm-6 text-right'B>>
        <'row'<'col-sm-12'tr>>
        <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 dataTables_pager'lp>>`,
        buttons: [
            'print',
            'copyHtml5',
            'excelHtml5',
            'csvHtml5',
            'pdfHtml5',
        ],
        columnDefs: [
            { 
                "orderable": false,
                "searchable": false,
                "targets": -1 
            }
        ]
    });
    table.buttons().remove();
    $("#sap_doc_select2").change(function (table) { 
        var code = $(this).val();
        $.ajax({
            type: "GET",
            url: "/SariRotiMobileBarcodeWeb/goods_movement/cancellation/add/get_detail_sap_doc",
            data: {code:code},
            dataType: "JSON",
            success: function (response) {
                var datatable = $('#kt_datatable2').DataTable();
                $("#doc_number").val(response.header["header_id"]);
                $("#doc_year").val(response.header["year"]);
                $("#mvt").val(response.header["mvt"]);
                for (let index = 0; index < response.detail.length; index++) {
                    datatable.row.add(
                        [
                            index+1,
                            response.detail[index]["material_code"],
                            response.detail[index]["material_name"],
                            response.detail[index]["qty"],
                            response.detail[index]["uom"],
                        ]
                    ).draw( false );
                }
            }
        });
    });
});

