$(document).ready(function () {
    $('#form').on('submit', function (e) {
        var formData = new FormData($(this)[0]);
        var redirect_url = $(this).data("form-success-redirect");
        var curr_this = $(this);
        Swal.fire({
            title: "Are You Sure?",
            text: "Please check the form You input before submit",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: '#3B3F51',
            confirmButtonText: "Yes, Save Data",
            cancelButtonText: "Cancel",
            showLoaderOnConfirm: true,
            allowOutsideClick: () => !Swal.isLoading(),
            preConfirm: function () {
                return new Promise(function (resolve, reject) {
                    $.ajax({
                        url: curr_this.attr('action'),
                        data: formData,
                        type: 'POST',
                        processData: false,
                        contentType: false,
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
                    text: "Data berhasil diupdate",
                    icon: 'success',
                    confirmButtonColor: '#3B3F51',
                }).then(function(result) {
                    if (result.value) {
                        window.setTimeout(function() {
                            window.location.href = redirect_url;
                        }, 1000);
                    }
                });
            }
        });
        e.preventDefault();
    });
});