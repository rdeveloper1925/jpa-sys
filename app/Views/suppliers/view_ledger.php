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
                            <?php if(!empty($ledger_items)): ?>
                            <?php foreach($ledger_items as $item): ?>
                            <tr>
                                <td><?=date('Y-m-d',strtotime($item->supply_date))?></td>
                                <td><?=$item->item?></td>
                                <td><?=$item->quantity?></td>
                                <td><?=$item->invoice_no?></td>
                                <td><?=$item->cartype?></td>
                                <td><?=$item->part_no?></td>
                                <td><?=$item->debit_note_no?></td>
                                <td><?=$item->amount?></td>
                                <td><a href="<?=base_url('suppliers/edit_item/'.$item->id)?>" class="btn btn-sm btn-warning">Edit</a>
                                    <a href="<?=base_url('suppliers/delete_item/'.$item->id)?>" class="btn btn-sm btn-danger">Delete</a>
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
                            <input list="browsers" class="form-control" name="itemSupplied" id="inventoryItem">
                            <datalist id="browsers" >
                                <?php foreach ($stock as $s): ?>
                                <option value="<?=$s->partName?>">
                                    <?php endforeach; ?>
                            </datalist>
                        </div>
                        <div class="form-group col-6">
                            <label for="date">Invoice No:</label>
                            <input type="text" class="form-control" required name="invoiceNo"/>
                        </div>
                        <div class="form-group col-6">
                            <label for="date">Quantity:</label>
                            <input type="text" class="form-control" required name="quantity"/>
                        </div>
                        <div class="form-group col-6">
                            <label for="date">Car Type:</label>
                            <input type="text" class="form-control" required name="carType"/>
                        </div>
                        <div class="form-group col-6">
                            <label for="date">Unit cost:</label>
                            <input type="number" step="0.1" class="form-control" required name="unitCost"/>
                        </div>
                        <div class="form-group col-6">
                            <label for="date">Part no:</label>
                            <input type="text" class="form-control" required name="partNo"/>
                        </div>
                        <div class="form-group col-6">
                            <label for="date">Debit Note no:</label>
                            <input type="text" class="form-control"  name="debitNoteNo"/>
                            <input type="hidden" name="supplier_id" value="<?=$supplier->id?>">
                        </div>
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