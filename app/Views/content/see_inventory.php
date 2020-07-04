<?php $this->extend('layouts/app') ?>
<?php $this->section('content'); ?>
<div class="row">
    <div class="col-md-12 container">
        <table width="100%" class="table table-bordered">
            <tr>
                <td style="background-color: rgba(44,159,175,0.44)"><b>Part No.</b></td>
                <td style="background-color: rgba(44,159,175,0.44)"><?=$item->partNo?></td>
                <td><b>Part Name.</b></td>
                <td><?=$item->partName?></td>
                <td style="background-color: rgba(44,159,175,0.44)"><b>Quantity In Store.</b></td>
                <td style="background-color: rgba(44,159,175,0.44)"><?=$item->quantityInStore?></td>
            </tr>
            <tr>
                <td><b>Date Out.</b></td>
                <td><?=$item->dateOut?></td>
                <td style="background-color: rgba(44,159,175,0.44)"><b>Balance In Store.</b></td>
                <td style="background-color: rgba(44,159,175,0.44)"><?=$item->balanceInStore?></td>
                <td><b>Supplied By.</b></td>
                <td><?=$item->suppliedBy?></td>
            </tr>
            <tr>
                <td style="background-color: rgba(44,159,175,0.44)"><b>Part Name.</b></td>
                <td style="background-color: rgba(44,159,175,0.44)"><?=$item->partName?></td>
                <td ><b>Unit Of Measure.</b></td>
                <td ><?=$item->unitOfMeasure?></td>
                <td style="background-color: rgba(44,159,175,0.44)"><b>Taken By.</b></td>
                <td style="background-color: rgba(44,159,175,0.44)"><?=$item->takenBy?></td>
            </tr>
        </table>
    </div>
    <div class="col-4">
        <button type="button" class="btn btn-success btn-md" data-toggle="modal" data-target="#modelId">
            Restock
        </button>
    </div>
    <div class="col-4" style="text-align: center">
        <button type="button" class="btn btn-warning btn-md" data-toggle="modal" data-target="#modelId3">
            Record Sale
        </button>
    </div>
    <div class="col-4" style="text-align: right;">
        <button type="button" class="btn btn-danger btn-md" data-toggle="modal" data-target="#modelId2">
            Manually Adjust Stock
        </button>
    </div>
    <div class="col-12 mt-3">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Inventory Tracker</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                        <tr>
                            <th>Item Name</th>
                            <th>Stock Before</th>
                            <th>Stock Action</th>
                            <th>Quantity</th>
                            <th>Stock After</th>
                            <th>Date</th>
                            <th>Done By</th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th>Item Name</th>
                            <th>Stock Before</th>
                            <th>Stock Action</th>
                            <th>Quantity</th>
                            <th>Stock After</th>
                            <th>Date</th>
                            <th>Done By</th>
                        </tr>
                        </tfoot>
                        <tbody>
                        <?php foreach ($log as $i) : ?>
                            <tr>
                                <td><?=$i->partName?></td>
                                <td><?=$i->quantityBefore?></td>
                                <td><?=$i->stockAction?></td>
                                <td><?=$i->quantity?></td>
                                <td><?=$i->quantityAfter?></td>
                                <td><?=date('d-M-Y',strtotime($i->date))?></td>
                                <td><?=$i->fullName?></td>
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
<div class="modal fade" id="modelId3" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Record Sale</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="<?=base_url('inventory/record_sale/'.$item->id)?>" method="post">
                    <div class="form-group">
                        <label for="">Item Name:</label>
                        <input type="text" class="form-control" name="itemName" value="<?=$item->partName?>" disabled/>
                    </div>
                    <div class="form-group">
                        <label for="">Quantity Sold: (<?=$item->unitOfMeasure?>)</label>
                        <input type="number" step=0.01 max="<?=$item->quantityInStore?>" class="form-control" name="quantity"  required/>
                        <input type="hidden" name="qtyInStore" value="<?=$item->quantityInStore?>"/>
                    </div>
            </div>
            <div class="modal-footer">
                <input type="submit" class="btn btn-warning" value="Save Sale" />
                </form>
            </div>
        </div>
    </div>
</div>


<!-- Modal -->
<div class="modal fade" id="modelId" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Restock / Adjust Stock</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                        <h5>Restock</h5>
                        <hr>
                        <form action="<?=base_url('inventory/restock/'.$item->id)?>" method="post">
                            <div class="form-group">
                                <label for="">Item Name:</label>
                                <input type="text" class="form-control" name="itemName" value="<?=$item->partName?>" disabled/>
                            </div>
                            <div class="form-group">
                                <label for="">Quantity Re-stocked: (<?=$item->unitOfMeasure?>)</label>
                                <input type="number" step=0.01 class="form-control" name="quantity"  required/>
                            </div>
                            <input type="submit" value="Save Restock" class="btn btn-sm btn-warning"/>
                        </form>
            </div>
        </div>
    </div>
</div>

<!-- Button trigger modal -->


<!-- Modal -->
<div class="modal fade" id="modelId2" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Manually Adjust Stock Numbers</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h5>Adjust Stock</h5>
                <hr>
                <form action="<?=base_url('inventory/adjust/'.$item->id)?>" method="post">
                    <div class="form-group">
                        <label for="">Item Name:</label>
                        <input type="text" class="form-control" name="itemName" value="<?=$item->partName?>" disabled/>
                    </div>
                    <div class="form-group">
                        <label for="">Quantity To Adjust: (<?=$item->unitOfMeasure?>)</label>
                        <input type="number" step=0.01 class="form-control" name="quantity"  required/>
                    </div>
            </div>
            <div class="modal-footer">
                <input type="submit" class="btn btn-danger" value="Save Manual Adjustments" />
                </form>
            </div>
        </div>
    </div>
</div>
<?php $this->endsection(); ?>
