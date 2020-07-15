<?php $this->extend('layouts/app') ?>
<?php $this->section('content'); ?>
    <div class="row">
        <div class="col-md-6 col-sm-6">
            <button type="button" class="btn btn-primary btn-md" data-toggle="modal" data-target="#modelId2">
                New Supplied Item
            </button>
        </div>
        <div class="col-md-6 col-sm-6" style="text-align: right">

        </div>
        <div class="col-12 mt-3">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Suppliers</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm"  width="100%" cellspacing="0">
                            <thead>
                            <tr>
                                <th>Date</th>
                                <th>Item Supplied</th>
                                <th>Quantity</th>
                                <th>Invoice No.</th>
                                <th>Car type</th>
                                <th>Part no</th>
                                <th>Debit note no</th>
                                <th>Amount</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tfoot>
                            <tr>
                                <th>Date</th>
                                <th>Item Supplied</th>
                                <th>Quantity</th>
                                <th>Invoice No.</th>
                                <th>Car type</th>
                                <th>Part no</th>
                                <th>Debit note no</th>
                                <th>Amount</th>
                                <th>Action</th>
                            </tr>
                            </tfoot>
                            <tbody>
                            <?php if(!empty($ledger_item)): ?>
                            <?php foreach($ledger_item as $item): ?>
                            <tr>
                                <td><?=$item->supply_date?></td>
                                <td><?=$item->item?></td>
                                <td><?=$item->quantity?></td>
                                <td><?=$item->invoice_no?></td>
                                <td><?=$item->cartype?></td>
                                <td><?=$item->part_no?></td>
                                <td><?=$item->debit_note_no?></td>
                                <td><?=$item->amount?></td>
                                <td><a href="<?=base_url('suppliers/edit/'.$item->id)?>" class="btn btn-sm btn-warning">Edit</a>
                                    <a href="<?=base_url('suppliers/delete/'.$item->id)?>" class="btn btn-sm btn-danger">Delete</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
<!-- Button trigger modal -->
<button type="button" class="btn btn-primary btn-md" data-toggle="modal" data-target="#modelId">
  Launch
</button>

<!-- Modal -->
<div class="modal fade" id="modelId2" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">sm</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
            </div>
            <div class="modal-body">
                <form method="post" action="<?=base_url('suppliers/save_supplied_item')?>">
                    <div class="row">
                        <div class="form-group col-6">
                            <label for="date">Supply Date:</label>
                            <input type="date" class="form-control" required name="date"/>
                        </div>
                        <div class="form-group col-6">
                            <label for="date">Item Supplied:</label>
                            <input type="text" class="form-control" required name="item"/>
                        </div>
                        <div class="form-group col-6">
                            <label for="date">Invoice No:</label>
                            <input type="date" class="form-control" required name="invoiceNo"/>
                        </div>
                        <div class="form-group col-6">
                            <label for="date">Quantity:</label>
                            <input type="text" class="form-control" required name="quantity"/>
                        </div>
                        <div class="form-group col-6">
                            <label for="date">Quantity:</label>
                            <input type="text" class="form-control" required name="quantity"/>
                        </div>
                    </div>


                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save</button>
            </div>
        </div>
    </div>
</div>

<!-- Button trigger modal -->


<!-- Modal -->
<div class="modal fade" id="modelId" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">sm</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
            </div>
            <div class="modal-body">
                Body
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save</button>
            </div>
        </div>
    </div>
</div>
<?php $this->endsection(); ?>