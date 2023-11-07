<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//AUTH
Route::post('authentication/login', 'Api\Transaction\Auth\LoginController@process');
Route::post('authentication/logout', 'Api\Transaction\Auth\LogoutController@process');

Route::group(['middleware' => 'check_user_token'], function () {
    //PO - GR
    Route::get('transaction/purchase_order/good_receipt/po_view', 'Api\Transaction\PurchaseOrder\GoodReceiptController@po_view');

    Route::get('transaction/purchase_order/good_receipt/po_header', 'Api\Transaction\PurchaseOrder\GoodReceiptController@po_header');

    Route::get('transaction/purchase_order/good_receipt/po_detail', 'Api\Transaction\PurchaseOrder\GoodReceiptController@po_detail');

    Route::get('transaction/purchase_order/good_receipt/gr_material', 'Api\Transaction\PurchaseOrder\GoodReceiptController@gr_material');

    Route::get('transaction/purchase_order/good_receipt/gr_material_info', 'Api\Transaction\PurchaseOrder\GoodReceiptController@gr_material_info');

    Route::post('transaction/purchase_order/good_receipt/submit', 'Api\Transaction\PurchaseOrder\GoodReceiptController@submit');

    Route::get('transaction/purchase_order/good_receipt/scan_qr', 'Api\Transaction\PurchaseOrder\GoodReceiptController@scan_qr');

    Route::get('transaction/purchase_order/good_receipt/history_header', 'Api\Transaction\PurchaseOrder\GoodReceiptController@history_header');

    Route::get('transaction/purchase_order/good_receipt/history_detail', 'Api\Transaction\PurchaseOrder\GoodReceiptController@history_detail');

    //PO - GI

    Route::get('transaction/purchase_order/good_issue/po_view', 'Api\Transaction\PurchaseOrder\GoodIssueController@po_view');

    Route::get('transaction/purchase_order/good_issue/po_header', 'Api\Transaction\PurchaseOrder\GoodIssueController@po_header');

    Route::get('transaction/purchase_order/good_issue/po_detail', 'Api\Transaction\PurchaseOrder\GoodIssueController@po_detail');

    Route::get('transaction/purchase_order/good_issue/gi_detail', 'Api\Transaction\PurchaseOrder\GoodIssueController@gi_detail');

    Route::get('transaction/purchase_order/good_issue/scan_qr', 'Api\Transaction\PurchaseOrder\GoodIssueController@scan_qr');

    Route::post('transaction/purchase_order/good_issue/submit', 'Api\Transaction\PurchaseOrder\GoodIssueController@submit');

    Route::get('transaction/purchase_order/good_issue/history_header', 'Api\Transaction\PurchaseOrder\GoodIssueController@history_header');

    Route::get('transaction/purchase_order/good_issue/history_detail', 'Api\Transaction\PurchaseOrder\GoodIssueController@history_detail');

    //TP
    Route::get('transaction/good_movement/transfer_posting/tp_view', 'Api\Transaction\GoodMovement\TransferPostingController@tp_view');

    Route::get('transaction/good_movement/transfer_posting/tp_detail', 'Api\Transaction\GoodMovement\TransferPostingController@tp_detail');

    Route::get('transaction/good_movement/transfer_posting/scan_qr', 'Api\Transaction\GoodMovement\TransferPostingController@scan_qr');

    Route::post('transaction/good_movement/transfer_posting/submit', 'Api\Transaction\GoodMovement\TransferPostingController@submit');

    Route::get('transaction/good_movement/transfer_posting/history_header', 'Api\Transaction\GoodMovement\TransferPostingController@history_header');

    Route::get('transaction/good_movement/transfer_posting/history_detail', 'Api\Transaction\GoodMovement\TransferPostingController@history_detail');

    Route::get('master/sloc/get', 'Api\Master\SlocController@index');

    Route::get('transaction/pid/pid_view', 'Api\Transaction\StockOpname\StockOpnameController@view');
    Route::get('transaction/pid/pid_view_detail', 'Api\Transaction\StockOpname\StockOpnameController@view_detail');
    Route::get('transaction/pid/pid_view_material_detail', 'Api\Transaction\StockOpname\StockOpnameController@view_material_detail');

    Route::get('transaction/pid/history_header', 'Api\Transaction\StockOpname\StockOpnameController@history_header');

    Route::get('transaction/pid/history_detail', 'Api\Transaction\StockOpname\StockOpnameController@history_detail');
    
    Route::get('transaction/pid/history_detail_material', 'Api\Transaction\StockOpname\StockOpnameController@history_detail_material');
    
    Route::post('transaction/pid/submit', 'Api\Transaction\StockOpname\StockOpnameController@submit');

    Route::post('transaction/pid/manual_submit', 'Api\Transaction\StockOpname\ManualAdjustmentController@submit');
});