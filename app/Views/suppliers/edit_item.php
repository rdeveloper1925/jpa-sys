<?php $this->extend('layouts/app') ?>
<?php $this->section('content'); ?>
    <div class="row">
        <div class="col-12 mt-3">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Edit ledger Item</h6>
                </div>
                <div class="card-body">
                    <form method="post" action="<?=base_url('suppliers/save_edited_item/'.$item_id)?>">
                        <div class="row">
                            <div class="form-group col-6">
                                <label for="date">Supply Date:</label>
                                <input type="date" class="form-control" value="<?=date('Y-m-d',strtotime($item->supply_date))?>" required name="date"/>
                            </div>
                            <div class="form-group col-6">
                                <label for="date">Item Supplied:</label>
                                <input list="browsers" class="form-control" value="<?=$item->item?>" name="itemSupplied" id="inventoryItem">
                                <datalist id="browsers" >
                                    <?php foreach ($stock as $s): ?>
                                    <option value="<?=$s->partName?>">
                                        <?php endforeach; ?>
                                </datalist>
                            </div>
                            <div class="form-group col-6">
                                <label for="date">Invoice No:</label>
                                <input type="text" class="form-control" value="<?=$item->invoice_no?>" required name="invoiceNo"/>
                            </div>
                            <div class="form-group col-6">
                                <label for="date">Quantity:</label>
                                <input type="text" class="form-control" value="<?=$item->quantity?>" required name="quantity"/>
                            </div>
                            <div class="form-group col-6">
                                <label for="date">Car Type:</label>
                                <input type="text" class="form-control" value="<?=$item->cartype?>" required name="carType"/>
                            </div>
                            <div class="form-group col-6">
                                <label for="date">Unit cost:</label>
                                <input type="number" step="0.1" class="form-control" value="<?=$item->unitCost?>" required name="unitCost"/>
                            </div>
                            <div class="form-group col-6">
                                <label for="date">Part no:</label>
                                <input type="text" class="form-control" value="<?=$item->part_no?>" required name="partNo"/>
                            </div>
                            <div class="form-group col-6">
                                <label for="date">Debit Note no:</label>
                                <input type="text" class="form-control" value="<?=$item->debit_note_no?>"  name="debitNoteNo"/>
                                <input type="hidden" name="supplier_id" value="<?=$item->supplier_id?>">
                            </div>
                            <div class="form-group col-6">
                                <label for="date">Settled:</label>
                                <input type="text" class="form-control" value="<?=$item->settled?>" required name="settled" placeholder="Yes / No"/>
                            </div>
                        </div>
                    <button type="submit" class="btn btn-primary">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

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

                </div>
            </div>
        </div>
    </div>

<?php $this->endsection(); ?>