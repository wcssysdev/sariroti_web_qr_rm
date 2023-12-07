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
    
    /** 
     * 2023 Sept
     * 
     * Master Material diambil hanya dari table GR yang sudah dikirim ke SAP
     * API Master Material dibuat setelah API List GR dan List GI
     */
    Route::get('master/goods/cc', 'Api\Master\GoodsController@get_cc');
    Route::get('master/goods/gl', 'Api\Master\GoodsController@get_gl');
    Route::get('master/goods/mvt', 'Api\Master\GoodsController@get_mvt_type');
    Route::get('master/goods/uom', 'Api\Master\GoodsController@get_uom');
    Route::get('master/goods/mat', 'Api\Master\GoodsController@get_list_mat');
    Route::get('master/goods/po_gr', 'Api\Master\GoodsController@get_list_po_gr');
    Route::get('master/goods/po_gi', 'Api\Master\GoodsController@get_list_po_gi');
    
    Route::get('transaction/purchase_order/good_issue/scan_qr_code', 'Api\Transaction\PurchaseOrder\GoodIssueController@scan_qr_non_plan');
    /**
     * 2023 Nov
     * 
     * TP - List Material
     */
    Route::get('master/goods/tp_materials', 'Api\Master\GoodsController@get_tp_list_materials');    
    Route::get('master/goods/tp_materials_y21', 'Api\Master\GoodsController@get_materials_for_type_y21');    
    Route::get('master/goods/tp_gr_detail', 'Api\Master\GoodsController@get_tp_list_gr_details_by_mat_code');    
    Route::get('master/goods/tp_get_batch', 'Api\Master\GoodsController@get_material_batch_y21');    
    
    Route::post('transaction/purchase_order/good_issue/submit_gi', 'Api\Transaction\PurchaseOrder\GoodIssueController@submit_gi');
    Route::get('transaction/purchase_order/good_issue/history_header_gi', 'Api\Transaction\PurchaseOrder\GoodIssueController@history_header_created_by');    
    Route::get('transaction/good_movement/transfer_posting/history_header_tp', 'Api\Transaction\GoodMovement\TransferPostingController@history_header_created_by');    
    
    Route::post('transaction/good_movement/transfer_posting/submit_tp', 'Api\Transaction\GoodMovement\TransferPostingController@submit_tp');
});