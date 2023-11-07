$(document).ready(function () {
    $('#clear_btn').on('click', function (e) {
        var action = $(this).data("redirect");
        Swal.fire({
            title: "Are You Sure?",
            text: "Data yang sudah diupload akan dihapus dan tidak bisa dikembalikan!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: '#3B3F51',
            confirmButtonText: "Ya, Hapus Data",
            cancelButtonText: "Cancel",
        }).then(function(result) {
            if (result.value) {
                window.location = action;

            }
        });
        e.preventDefault();
    });
});
