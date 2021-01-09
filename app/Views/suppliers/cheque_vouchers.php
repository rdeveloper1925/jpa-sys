<?php $this->extend('layouts/app') ?>
<?php $this->section('content'); ?>
<div class="row">
    <div class="col-md-6 col-sm-6">
        <a href="<?=base_url('suppliers/cheque_voucher')?>" class="btn btn-warning btn-md" >
            New cheque voucher
        </a>
    </div>
    <div class="col-12 mt-3">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Suppliers</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm" id="datatable" width="100%" cellspacing="0">
                        <thead>
                        <tr>
                            <th>Voucher Id</th>
                            <th>Name</th>
                            <th>Company</th>
                            <th>Date</th>
                            <th>ChequeNo.</th>
                            <th>Prepared By.</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th>Voucher Id</th>
                            <th>Name</th>
                            <th>Company</th>
                            <th>Date</th>
                            <th>ChequeNo.</th>
                            <th>Prepared By.</th>
                            <th>Actions</th>
                        </tr>
                        </tfoot>
                        <tbody>
                        <?php foreach ($vouchers as $v): ?>
                            <tr>
                                <td><?=$v->id?></td>
                                <td><?=$v->name?></td>
                                <td><?=$v->supplier?></td>
                                <td><?=date('Y-M-d',strtotime($v->date))?></td>
                                <td><?=$v->chequeNo?></td>
                                <td><?=$v->maker?></td>
                                <td>
                                    <a href="<?=base_url('suppliers/view_cheque_voucher/'.$v->id)?>" class="btn btn-primary btn-sm">View Cheque Voucher</a>
                                    <a href="<?=base_url('suppliers/edit_cheque_voucher/'.$v->id)?>" class="btn btn-warning btn-sm">
                                        Edit Voucher Details
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Button trigger modal -->


<!-- Modal -->
<div class="modal fade" id="modelId" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">New Supplier</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="<?=base_url('suppliers/save')?>" method="post">
                    <div class="form-group">
                        <label for="name">Supplier Name: </label>
                        <input class="form-control" name="name" required id="name"/>
                    </div>
                    <div class="form-group">
                        <label for="supplier_name">Supplier Contact</label>
                        <input type="text" class="form-control" name="supplier_contact" required/>
                    </div>

            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Save</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Button trigger modal -->



<!-- Modal -->
<div class="modal fade" id="modelId1" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit supplier Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="<?=base_url('suppliers/edit')?>">
                    <div class="form-group">
                        <label for="supplier_name">Supplier Name</label>
                        <input type="text" class="form-control" id="sup_name" name="supplier_name" required/>
                    </div>
                    <div class="form-group">
                        <label for="supplier_name">Supplier Contact</label>
                        <input type="hidden" id="id" name="id"/>
                        <input type="text" class="form-control" id="sup_contact" name="supplier_contact" required/>
                    </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Save Edits</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $this->endsection(); ?>
