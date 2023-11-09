// $(document).ready(function () {
//     $('#kt_datatable1').on('click', '.delete_btn', function () {
//         var curr_this = $(this);
//         var user_code = $(this).data("user-code");
//         var action = $(this).data("action");
//         Swal.fire({
//             title: "Are You Sure?",
//             text: "Data yang sudah dihapus tidak dapat dikembalikan!",
//             icon: "warning",
//             showCancelButton: true,
//             confirmButtonColor: '#D94148',
//             confirmButtonText: "Ya, Hapus Data",
//             cancelButtonText: "Cancel",
//         }).then(function(result) {
//             if (result.value) {
//                 $.ajax({
//                     url: action,
//                     headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
//                     data: {"user_code": user_code},
//                     type: 'POST',
//                     success: function (data) {
//                         Swal.fire({
//                             title: 'Success!',
//                             text: "Data berhasil dihapus",
//                             icon: 'success',
//                             confirmButtonColor: '#D94148',
//                         }).then(function(result) {
//                             if (result.value) {
//                                 window.setTimeout(function() {
//                                     location.reload();
//                                 }, 1000);
//                             }
//                         });
//                     },
//                     error: function (data) {
//                         var data = data.responseJSON;
//                         if (Array.isArray(data.message)) {
//                             var err_message = "<ol>";
//                             for (let i = 0; i < data.message.length; i++) {
//                                 err_message += "<li>"+data.message[i]+"</li>";
//                             }
//                             err_message += "</ol>";
//                         }
//                         else{
//                             err_message = data.message;
//                         }
//                         Swal.fire({
//                             title: 'Error!',
//                             width: "35%",
//                             html: err_message,
//                             icon: 'error',
//                             confirmButtonColor: '#D94148',
//                         })
//                     }
//                 });
                
//             }
//         });
//         e.preventDefault();
//     });
// });