<!-- Modal-->
<div class="modal fade" id="modal_detail" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Detail Batch #200101</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <table class="table table-bordered table-checkable">
                        <thead>
                            <tr>
                                <th>Batch No</th>
                                <th>Qty Left</th>
                                <th>UOM</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>210101</td>
                                <td>10</td>
                                <td>Pouch</td>
                                <td>
                                    <a class="btn btn-secondary btn-sm" data-toggle="modal" role="button">Choose</a>
                                </td>
                            </tr>
                            <tr>
                                <td>210110</td>
                                <td>50</td>
                                <td>Pouch</td>
                                <td>
                                    <a class="btn btn-secondary btn-sm" data-toggle="modal" role="button">Choose</a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Close</button>
                {{-- <button type="button" class="btn btn-primary font-weight-bold">Save changes</button> --}}
            </div>
        </div>
    </div>
</div>