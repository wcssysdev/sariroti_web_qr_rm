"use strict";
jQuery(document).ready(function() {
	var table1 = $('#kt_datatable1').DataTable({
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
    table1.buttons().remove();

	var table2 = $('#kt_datatable2').DataTable({
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
    table2.buttons().remove();

	var table3 = $('#kt_datatable3').DataTable({
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
    table3.buttons().remove();

	var table4 = $('#kt_datatable4').DataTable({
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
    table4.buttons().remove();

    var table5 = $('#kt_datatable5').DataTable({
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
    table5.buttons().remove();
});
