$(document).ready(function () {
    $('#kt_login_signin_form').on('submit', function (e) {
        var curr_this = $(this);
        var redirect_url = $(this).data("form-success-redirect");
        $.ajax({
            url: curr_this.attr('action'),
            data: curr_this.serialize(),
            type: 'POST',
            beforeSend: function(){
                KTApp.block('#kt_login', {
                    overlayColor: '#000000',
                    type: 'v2',
                    state: 'primary',
                    message: 'Loading...'
                });
            },
            success: function (data) {
                Swal.fire({
                    title: 'Success!',
                    text: "You Are Successfully Logged In",
                    icon: 'success',
                    confirmButtonColor: '#3B3F51',
                }).then(function(result) {
                    if (result.value) {
                        window.setTimeout(function() {
                            window.location.href = redirect_url;
                        }, 1000);
                    }
                });
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
                }).then(function(result) {
                    if (result.value) {
                        window.setTimeout(function() {
                            location.reload();
                        }, 1000);
                    }
                });
            },
            complete:function(data){
                KTApp.unblock('#kt_login');
            }
        });
        e.preventDefault();
    });
});