$(document).ready(function () {
    $('#save_btn').on('click', function (e) {
        var action = $(this).data("action");
        var redirect = $(this).data("success-redirect");
        Swal.fire({
            title: "Are You Sure?",
            text: "Periksa kembali data yang telah Anda input sebelum melanjutkan",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: '#3B3F51',
            confirmButtonText: "Ya, Upload Data",
            cancelButtonText: "Cancel",
            showLoaderOnConfirm: true,
            allowOutsideClick: () => !Swal.isLoading(),
            preConfirm: function () {
                return new Promise(function (resolve, reject) {
                    $.ajax({
                        url: action,
                        type: 'GET',
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
                    text: "Data berhasil disimpan",
                    icon: 'success',
                    confirmButtonColor: '#3B3F51',
                }).then(function(result) {
                    if (result.value) {
                        window.setTimeout(function() {
                            window.location.href = redirect;
                        }, 1000);
                    }
                });
            }
        });
        e.preventDefault();
    });
});
