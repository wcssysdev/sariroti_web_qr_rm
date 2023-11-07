$(document).ready(function () {
    $('#kt_datatable1').on('click', '.delete_btn', function (e) {
        var curr_this = $(this);
        var tag_id = $(this).data("tag-id");
        var action = $(this).data("action");
        Swal.fire({
            title: "Are You Sure?",
            text: "Data yang sudah dihapus tidak dapat dikembalikan!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: '#3B3F51',
            confirmButtonText: "Ya, Hapus Data",
            cancelButtonText: "Cancel",
            showLoaderOnConfirm: true,
            allowOutsideClick: () => !Swal.isLoading(),
            preConfirm: function () {
                return new Promise(function (resolve, reject) {
                    $.ajax({
                        url: action,
                        data: {"tag_id": tag_id},
                        type: 'POST',
                        success: function (data) {
                            resolve(data);
                        },
                        error: function (data) {
                            var data = data.responseJSON;
                            if (Array.isArray(data.message)) {
                                var err_message = "<ol>";
                                for (let i = 0; i < data.message.length; i++) {
                                    err_message += "<li>"+data.message[i]+"</li>";
                                }
                                err_message += "</ol>";
                            }
                            else{
                                err_message = data.message;
                            }
                            Swal.fire({
                                title: 'Error!',
                                width: "35%",
                                html: err_message,
                                icon: 'error',
                                confirmButtonColor: '#3B3F51',
                            })
                        
                        }
                    });
                })
            },
        }).then(function(result) {
            if (result.value) {
                Swal.fire({
                    title: 'Success!',
                    text: "Data berhasil dihapus",
                    icon: 'success',
                    confirmButtonColor: '#3B3F51',
                }).then(function(result) {
                    if (result.value) {
                        window.setTimeout(function() {
                            location.reload();
                        }, 1000);
                    }
                });
            }
        });
        e.preventDefault();
    });
});