<?php

use Illuminate\Support\Facades\Route;

//URL::forceScheme('https');
Auth::routes([
    'register' => false,
    'reset' => false,
    'verify' => false,
]);
//Routes without session checking
Route::get('/', 'Authentication\LoginController@index')->name('login_view');
Route::post('/authentication/login', 'Authentication\LoginController@process')->name('login_process');
Route::get('/authentication/logout', 'Authentication\LogoutController@index')->name('logout_process');
// SEND EMAIL FORGOT PASSWORD
Route::post('/authentication/forgotPassword', 'Authentication\ForgotPasswordController@send_email')->name('forgot_password');
// FORM FOR FORGOT PASSWORD (WEB VIEW)
Route::get('/authentication/forgotPasswordView', 'Authentication\ForgotPasswordController@forgot_password_view')->name('forgot_password_view');
Route::post('/authentication/forgotPasswordView', 'Authentication\ForgotPasswordController@forgot_password_save')->name('forgot_password_save');

//Routes with session checking
Route::group(['middleware' => 'check_user_session:1'], function () {
    Route::post('/master_data/plant/request_sap', 'MasterData\Plant\ViewController@master_data_request_sap')->name('master_data_plant_request_sap');
    Route::post('/master_data/plant/sync_sap', 'MasterData\Plant\ViewController@master_data_sync_sap')->name('master_data_plant_sync_sap');

    Route::post('/master_data/sloc/request_sap', 'MasterData\Sloc\ViewController@master_data_request_sap')->name('master_data_sloc_request_sap');
    Route::post('/master_data/sloc/sync_sap', 'MasterData\Sloc\ViewController@master_data_sync_sap')->name('master_data_sloc_sync_sap');

    Route::post('/master_data/Material/request_sap', 'MasterData\Material\ViewController@master_data_request_sap')->name('master_data_material_request_sap');
    Route::post('/master_data/Material/sync_sap', 'MasterData\Material\ViewController@master_data_sync_sap')->name('master_data_material_sync_sap');

    Route::post('/master_data/material_uom/request_sap', 'MasterData\MaterialUom\ViewController@master_data_request_sap')->name('master_data_material_uom_request_sap');
    Route::post('/master_data/material_uom/sync_sap', 'MasterData\MaterialUom\ViewController@master_data_sync_sap')->name('master_data_material_uom_sync_sap');

    //Master Data Vendor
    Route::get('/master_data/vendor/view', 'MasterData\Vendor\ViewController@index')->name('master_data_vendor_view');
    Route::post('/master_data/vendor/request_sap', 'MasterData\Vendor\ViewController@master_data_request_sap')->name('master_data_vendor_request_sap');
    Route::post('/master_data/vendor/sync_sap', 'MasterData\Vendor\ViewController@master_data_sync_sap')->name('master_data_vendor_sync_sap');

    Route::post('/master_data/movement_type/request_sap', 'MasterData\MovementType\ViewController@master_data_request_sap')->name('master_data_movement_type_request_sap');
    Route::post('/master_data/movement_type/sync_sap', 'MasterData\MovementType\ViewController@master_data_sync_sap')->name('master_data_movement_type_sync_sap');

    //Master Data Gl Account
    Route::get('/master_data/gl_account/view', 'MasterData\GlAccount\ViewController@index')->name('master_data_gl_account_view');
    Route::post('/master_data/gl_account/request_sap', 'MasterData\GlAccount\ViewController@master_data_request_sap')->name('master_data_gl_account_request_sap');
    Route::post('/master_data/gl_account/sync_sap', 'MasterData\GlAccount\ViewController@master_data_sync_sap')->name('master_data_gl_account_sync_sap');

    Route::post('/master_data/cost_center/request_sap', 'MasterData\CostCenter\ViewController@master_data_request_sap')->name('master_data_cost_center_request_sap');
    Route::post('/master_data/cost_center/sync_sap', 'MasterData\CostCenter\ViewController@master_data_sync_sap')->name('master_data_cost_center_sync_sap');

    // Master User

    Route::get('/master_data/users/view', 'MasterData\Users\ViewController@index')->name('master_data_users_view');
    Route::get('/master_data/users/add', 'MasterData\Users\AddController@index')->name('master_data_users_add');
    Route::post('/master_data/users/save', 'MasterData\Users\AddController@save')->name('master_data_users_save');
    Route::get('/master_data/users/edit', 'MasterData\Users\EditController@index')->name('master_data_users_edit');
    Route::post('/master_data/users/update', 'MasterData\Users\EditController@update')->name('master_data_users_update');
    Route::get('/master-data/users/upload/view', 'MasterData\Users\UploadController@index')->name('master_data_users_upload_view');
    Route::post('/master-data/users/upload', 'MasterData\Users\UploadController@upload')->name('master_data_users_upload');
    Route::get('/master-data/users/upload/save', 'MasterData\Users\UploadController@save')->name('master_data_users_upload_save');
    Route::get('/master-data/users/upload/clear', 'MasterData\Users\UploadController@clear')->name('master_data_users_upload_clear');

    Route::get('/purchase_order/master/view', 'PurchaseOrder\Master\ViewController@index')->name('purchase_order_master_view');
    Route::get('/purchase_order/master/detail', 'PurchaseOrder\Master\ViewController@detail')->name('purchase_order_master_view_detail');
    
    Route::post('/purchase_order/master/request_sap', 'PurchaseOrder\Master\ViewController@master_data_request_sap')->name('purchase_order_master_request_sap');
    Route::post('/purchase_order/master/sync_sap', 'PurchaseOrder\Master\ViewController@master_data_sync_sap')->name('purchase_order_master_sync_sap');
});

Route::group(['middleware' => 'check_user_session:1,2,3,4,5,6'], function () {
    Route::get('/home/dashboard', 'HomeController@index')->name('home');
    //Master Data Plant
    Route::get('/master_data/plant/view', 'MasterData\Plant\ViewController@index')->name('master_data_plant_view');
    //Master Data sloc
    Route::get('/master_data/sloc/view', 'MasterData\Sloc\ViewController@index')->name('master_data_sloc_view');
    //Material UOM
    Route::get('/master_data/material_uom/view', 'MasterData\MaterialUom\ViewController@index')->name('master_data_material_uom_view');

    Route::get('/master_data/movement_type/view', 'MasterData\MovementType\ViewController@index')->name('master_data_movement_type_view');

    //Master Data Cost Center
    Route::get('/master_data/cost_center/view', 'MasterData\CostCenter\ViewController@index')->name('master_data_cost_center_view');

    //Laporan
    Route::get('/report/stock', 'Report\StockController@index')->name('stock_report_view');
    Route::get('/report/good_movement', 'Report\GoodMovementController@index')->name('good_movement_report_view');

    //Goods Movement - Tranfer Posting
    Route::get('/goods_movement/transfer_posting/view', 'GoodsMovement\TransferPosting\ViewController@index')->name('goods_movement_transfer_posting_view');
    Route::get('goods_movement/transfer_posting/detail', 'GoodsMovement\TransferPosting\DetailController@index')->name('goods_movement_transfer_posting_detail');
    Route::get('/goods_movement/transfer_posting/detail/detail', 'GoodsMovement\TransferPosting\DetailController@detail')->name('goods_movement_transfer_posting_detail_detail');
    Route::get('/goods_movement/transfer_posting/detail/detail/print_qr', 'GoodsMovement\TransferPosting\DetailController@print_qr')->name('goods_movement_transfer_posting_detail_detail_qr_code');
    Route::get('/goods_movement/transfer_posting/detail_print', 'GoodsMovement\TransferPosting\ViewController@print')->name('goods_movement_transfer_posting_detail_print');
});

Route::group(['middleware' => 'check_user_session:1,2,3,5,6'], function () {
    //Purchase Order good receive

    Route::get('/purchase_order/good_receipt/view', 'PurchaseOrder\GoodReceipt\ViewController@index')->name('purchase_order_good_receipt_view');
    Route::get('/purchase_order/good_receipt/detail', 'PurchaseOrder\GoodReceipt\DetailController@index')->name('purchase_order_good_receipt_detail');
    Route::get('/purchase_order/good_receipt/detail_print', 'PurchaseOrder\GoodReceipt\DetailController@print')->name('purchase_order_good_receipt_detail_print');
    Route::get('/purchase_order/good_receipt/detail/detail', 'PurchaseOrder\GoodReceipt\DetailController@detail')->name('purchase_order_good_receipt_detail_detail');
    Route::get('/purchase_order/good_receipt/delete', 'PurchaseOrder\GoodReceipt\DetailController@delete')->name('purchase_order_good_receipt_detail_delete');
    Route::get('/purchase_order/good_receipt/detail/detail/print_qr', 'PurchaseOrder\GoodReceipt\DetailController@print_qr')->name('purchase_order_good_receipt_detail_detail_qr_code');

//    Route::get('/good_receive/purchase_order/view', 'GoodReceive\PurchaseOrder\ViewController@index')->name('transaction_purchase_order_view');
//    Route::get('/good_receive/purchase_order/detail', 'GoodReceive\PurchaseOrder\DetailController@index')->name('transaction_purchase_order_detail');


    //GI STO
    Route::get('purchase_order/good_issue/view', 'PurchaseOrder\GoodIssue\ViewController@index')->name('purchase_order_good_issue_view');
    Route::get('purchase_order/good_issue/detail', 'PurchaseOrder\GoodIssue\DetailController@index')->name('purchase_order_good_issue_detail');
    Route::get('/purchase_order/good_issue/detail/detail', 'PurchaseOrder\GoodIssue\DetailController@detail')->name('purchase_order_good_issue_detail_detail');
    Route::get('/purchase_order/good_issue/detail/detail/print_qr', 'PurchaseOrder\GoodIssue\DetailController@print_qr')->name('purchase_order_good_issue_detail_detail_qr_code');
    Route::get('/purchase_order/good_issue/detail_print', 'PurchaseOrder\GoodIssue\DetailController@print')->name('purchase_order_good_issue_detail_print');
    Route::get('/purchase_order/good_issue/delete', 'PurchaseOrder\GoodIssue\DetailController@delete')->name('purchase_order_good_issue_detail_delete');

});

Route::group(['middleware' => 'check_user_session:2,3,5,6'], function () {
    Route::get('/purchase_order/good_receipt/add', 'PurchaseOrder\GoodReceipt\AddController@index')->name('purchase_order_good_receipt_add');
    Route::get('/purchase_order/good_receipt/add/get_materials', 'PurchaseOrder\GoodReceipt\AddController@get_materials')->name('purchase_order_good_receipt_add_get_materials');
    Route::get('/purchase_order/good_receipt/add/get_material_status', 'PurchaseOrder\GoodReceipt\AddController@get_material_status')->name('purchase_order_good_receipt_add_get_material_status');
    Route::post('/purchase_order/good_receipt/add/save_material', 'PurchaseOrder\GoodReceipt\AddController@save_material')->name('purchase_order_good_receipt_save_material');
    Route::post('/purchase_order/good_receipt/add/delete_material', 'PurchaseOrder\GoodReceipt\AddController@delete_material')->name('purchase_order_good_receipt_delete_material');
    Route::post('/purchase_order/good_receipt/add/save', 'PurchaseOrder\GoodReceipt\AddController@save')->name('purchase_order_good_receipt_save');
//    Route::get('/good_receive/purchase_order/create_gr', 'GoodReceive\PurchaseOrder\CreateGRController@index')->name('transaction_purchase_order_create_gr');
});


Route::group(['middleware' => 'check_user_session:2,6'], function () {
    Route::get('purchase_order/good_issue/add', 'PurchaseOrder\GoodIssue\AddController@index')->name('transaction_gi_sto_add');
    Route::get('/purchase_order/good_issue/add', 'PurchaseOrder\GoodIssue\AddController@index')->name('purchase_order_good_issue_add');
    Route::get('/purchase_order/good_issue/add/get_materials', 'PurchaseOrder\GoodIssue\AddController@get_materials')->name('purchase_order_good_issue_add_get_materials');
    Route::get('/purchase_order/good_issue/add/get_material_gr', 'PurchaseOrder\GoodIssue\AddController@get_material_gr')->name('purchase_order_good_issue_add_get_material_gr');
    Route::get('/purchase_order/good_issue/add/get_material_status', 'PurchaseOrder\GoodIssue\AddController@get_material_status')->name('purchase_order_good_issue_add_get_material_status');
    Route::post('/purchase_order/good_issue/add/save_material', 'PurchaseOrder\GoodIssue\AddController@save_material')->name('purchase_order_good_issue_save_material');
    Route::post('/purchase_order/good_issue/add/delete_material', 'PurchaseOrder\GoodIssue\AddController@delete_material')->name('purchase_order_good_issue_delete_material');
    Route::post('/purchase_order/good_issue/add/save', 'PurchaseOrder\GoodIssue\AddController@save')->name('purchase_order_good_issue_save');

    Route::get('/goods_movement/cancellation/get_doc_number', 'GoodsMovement\Cancellation\AddController@get_doc_number')->name('cancellation_get_doc_number');
    Route::get('/goods_movement/cancellation/get_doc_number_detail', 'GoodsMovement\Cancellation\AddController@get_doc_number_detail')->name('cancellation_get_doc_number_detail');

    Route::get('/goods_movement/cancellation/add', 'GoodsMovement\Cancellation\AddController@index')->name('transaction_goods_movement_cancellation_add');
    Route::get('/goods_movement/cancellation/add/get_detail_sap_doc', 'GoodsMovement\Cancellation\AddController@get_detail_sap_doc')->name('transaction_goods_movement_cancellation_add_get_sap_doc');
    Route::get('/goods_movement/cancellation/add/get_detail_sap_doc', 'GoodsMovement\Cancellation\AddController@get_detail_sap_doc')->name('transaction_goods_movement_cancellation_add_get_sap_doc');
    Route::post('/goods_movement/cancellation/save', 'GoodsMovement\Cancellation\AddController@save')->name('transaction_goods_movement_cancellation_save');
    Route::get('/goods_movement/cancellation/detail', 'GoodsMovement\Cancellation\DetailController@index')->name('transaction_goods_movement_cancellation_detail');
});

Route::group(['middleware' => 'check_user_session:1,2,4,5,6'], function () {
    //Goods Movement - Cancellation
    Route::get('/goods_movement/cancellation/view', 'GoodsMovement\Cancellation\ViewController@index')->name('transaction_goods_movement_cancellation_view');
    Route::get('/goods_movement/cancellation/view_detail', 'GoodsMovement\Cancellation\DetailController@view_cancellation_detail')->name('transaction_goods_movement_cancellation_view_detail');

});

Route::group(['middleware' => 'check_user_session:4'], function () {
    Route::post('/stock_opname/view/detail/approval', 'StockOpname\DetailController@approval')->name('transaction_stock_opname_submit_approval');
});

Route::group(['middleware' => 'check_user_session:1,4'], function () {
    Route::get('/stock_opname/view', 'StockOpname\ViewController@index')->name('transaction_stock_opname_view');
    Route::get('/stock_opname/view/detail', 'StockOpname\DetailController@index')->name('transaction_stock_opname_view_detail');

    Route::get('/stock_opname/view/detail/material', 'StockOpname\DetailController@material_detail')->name('transaction_stock_opname_view_detail_material');
    Route::get('/stock_opname/view/detail/edit/material', 'StockOpname\DetailController@edit_material_detail')->name('transaction_stock_opname_view_detail_edit_material');

    Route::post('/stock_opname/view/detail/save', 'StockOpname\DetailController@submit')->name('transaction_stock_opname_submit');
});

Route::group(['middleware' => 'check_user_session:2,4,6'], function () {
    Route::get('/goods_movement/transfer_posting/add', 'GoodsMovement\TransferPosting\AddController@index')->name('goods_movement_transfer_posting_add');
    Route::get('/goods_movement/transfer_posting/add', 'GoodsMovement\TransferPosting\AddController@index')->name('goods_movement_transfer_posting_add');
    Route::get('/goods_movement/transfer_posting/add/get_materials', 'GoodsMovement\TransferPosting\AddController@get_materials')->name('goods_movement_transfer_posting_add_get_materials');
    Route::get('/goods_movement/transfer_posting/add/get_materials_y21', 'GoodsMovement\TransferPosting\AddController@get_materials_y21')->name('goods_movement_transfer_posting_add_get_materials_y21');
    Route::get('/goods_movement/transfer_posting/add/get_material_batch_y21', 'GoodsMovement\TransferPosting\AddController@get_material_batch_y21')->name('goods_movement_transfer_posting_add_get_material_batch_y21');
    Route::get('/goods_movement/transfer_posting/add/get_material_gr', 'GoodsMovement\TransferPosting\AddController@get_material_gr')->name('goods_movement_transfer_posting_add_get_material_gr');
    Route::get('/goods_movement/transfer_posting/add/get_material_status', 'GoodsMovement\TransferPosting\AddController@get_material_status')->name('goods_movement_transfer_posting_add_get_material_status');
    Route::post('/goods_movement/transfer_posting/add/save_material', 'GoodsMovement\TransferPosting\AddController@save_material')->name('goods_movement_transfer_posting_save_material');
    Route::post('/goods_movement/transfer_posting/add/delete_material', 'GoodsMovement\TransferPosting\AddController@delete_material')->name('goods_movement_transfer_posting_delete_material');

    Route::post('/goods_movement/transfer_posting/add/save_material_y21', 'GoodsMovement\TransferPosting\AddController@save_material_y21')->name('goods_movement_transfer_posting_save_material_y21');
    Route::post('/goods_movement/transfer_posting/add/delete_material_y21', 'GoodsMovement\TransferPosting\AddController@delete_material_y21')->name('goods_movement_transfer_posting_delete_material_y21');

    Route::post('/goods_movement/transfer_posting/add/save', 'GoodsMovement\TransferPosting\AddController@save')->name('goods_movement_transfer_posting_save');

    Route::get('/goods_movement/transfer_posting/delete', 'GoodsMovement\TransferPosting\ViewController@delete')->name('goods_movement_transfer_posting_delete');
});

Route::group(['middleware' => 'check_user_session:1,2,6'], function () {
    //Master Data Material
    Route::get('/master_data/material/view', 'MasterData\Material\ViewController@index')->name('master_data_material_view');
    Route::get('/master_data/material/detail', 'MasterData\Material\DetailController@index')->name('master_data_material_detail');

    Route::get('/good_receipt/repost', 'PurchaseOrder\GoodReceipt\RepostController@index')->name('repost_gr');
    Route::get('/good_issue/repost', 'PurchaseOrder\GoodIssue\RepostController@index')->name('repost_gi');
    Route::get('/transfer_posting/repost', 'GoodsMovement\TransferPosting\RepostController@index')->name('repost_tp');
    Route::get('/cancellation/repost', 'GoodsMovement\Cancellation\RepostController@index')->name('repost_cancellation');
    Route::get('/pid/repost', 'StockOpname\RepostController@index')->name('repost_pid');
});

//CRON
// Route::get('/purchase_order/good_receipt/get_sap_response', 'PromiseController@index');
// Route::get('/purchase_order/good_receipt/get_pid_sap_response', 'PromiseController@pid');
Route::get('/purchase_order/receive', 'PurchaseOrder\ReceiveController@index');
// Route::get('/pid/receive', 'StockOpname\ReceiveController@index');
// Route::get('/mail/gr_reminder', 'PromiseController@mail_cron');