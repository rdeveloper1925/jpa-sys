<?php $this->extend('layouts/app') ?>
<?php $this->section('content'); ?>
<div class="row">
    <div class="col-md-12 container">
        <div id="alerter" class="col-12"></div>
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Receipt Items (Invoice No: <?=$receiptNo?>)</h6>
            </div>
            <div class="card-body">
                <button type="button" class="btn btn-primary btn-md" data-toggle="modal" data-target="#modelId"><i class="fa fa-plus" aria-hidden="true"></i> Add Receipt Item</button>                <hr>
                <div class="table-responsive">
                    <table class="table table-bordered" id="datatable"  width="100%" cellspacing="0">
                        <thead>
                        <tr>
                            <th>Serial No.</th>
                            <th>Part Name</th>
                            <th>Quantity</th>
                            <th>Unit Price</th>
                            <th>Total</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th>Serial No.</th>
                            <th>Part Name</th>
                            <th>Quantity</th>
                            <th>Unit Price</th>
                            <th>Total</th>
                            <th>Action</th>
                        </tr>
                        </tfoot>
                        <tbody >
                        <?php if(!empty($items)): ?>
                            <?php $grandtotal=0;?>
                            <?php foreach($items as $key=>$item): ?>
                                <tr>
                                    <td><?=$key+1?></td>
                                    <td><?=$item->inventoryItem?></td>
                                    <td><?=$item->quantity?><?php echo ' '.$item->units.''?></td>
                                    <td><?=$item->unitCost?></td>
                                    <td><?=$item->quantity*$item->unitCost?></td>
                                    <td><a href="<?=base_url('receipts/delete_receipt_item/'.$item->id.'/'.$receiptNo)?>" class="btn btn-danger btn-sm">Remove</a>
                                        <button type="button" id="<?=$item->id?>" class="btn btn-warning btn-sm td" data-toggle="modal" data-target="#modelId2">
                                            Edit
                                        </button>
                                    </td><?php $grandtotal=$grandtotal+$item->quantity*$item->unitCost?>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                    <?php if (isset($grandtotal)): ?>
                    <h4 style="color: rgba(178,27,15,0.92)">Total Cost: <strong><?=number_format($grandtotal,2,'.',',')?></strong></h4>
                </div>
                <form method="post" action="<?=base_url('receipts/generate/'.$receiptNo)?>">
                    <div class="row">
                        <div class="form-group col-3">
                            <label for="">Amount Received:</label>
                            <input type="number" step=0.01 class="form-control" max="<?=$grandtotal?>" name="amount" required/>
                            <?php \CodeIgniter\Config\Services::session()->set('amountDue',$grandtotal); ?>
                        </div>
                    </div>
                    <input type="submit" value="Done, Download Receipt" class='btn btn-md btn-success'/>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<script>
    $('.td').on('click',function(){
        var id=$(this).attr('id');
        $.post("<?=base_url('receipts/fetch_invoice_item')?>", {id:id},
            function (data, textStatus, jqXHR) {
                if(data.success){
                    $("#inventoryItem").val(data.data.inventoryItem);
                    $("#quantity").val(data.data.quantity);
                    $("#units").val(data.data.units);
                    $("#unitPrice").val(data.data.unitCost);
                    $("#itemId").val(id);
                }
            },
            "JSON"
        );
    });
</script>

<!-- Button trigger modal -->


<!-- Modal -->
<div class="modal fade" id="modelId2" tabindex="-1" role="dialog" aria-labelledby="modelTitleId2" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Receipt Item</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="<?=base_url('receipts/receipt_items_save_edit/'.$receiptNo)?>" method="post">
                    <div class="form-group">
                        <label for="">Part Name:</label>
                        <input list="browsers" class="form-control" name="inventoryItem" id="inventoryItem">
                        <datalist id="browsers" >
                            <?php foreach ($stock as $s): ?>
                            <option value="<?=$s->partName?>">
                                <?php endforeach; ?>
                        </datalist>
                    </div>
                    <div class="row">
                        <div class="form-group col-6">
                            <label for="">Quantity: </label>
                            <input type="number" step="0.01" class="form-control" name='quantity' id="quantity" required>
                        </div>
                        <div class="form-group col-6">
                            <label for="">Unit Of Measure: </label>
                            <input list="units" class="form-control" name='units' id="units" required>
                            <datalist id="units">
                                <?php foreach($units as $unit): ?>
                                    <option value="<?=$unit->unit?>"/>
                                <?php endforeach; ?>
                            </datalist>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="">Unit Price</label>
                        <input type="number" class="form-control" id="unitPrice" name="unitPrice" required>
                        <input type="hidden" name="itemId" id="itemId"/>
                    </div>
            </div>
            <div class="modal-footer">
                <input type="submit" value="Save Edits" class="btn btn-warning btn-md"/>
                </form>
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
                <h5 class="modal-title">New Receipt Item</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="<?=base_url('receipts/receipt_items_save/'.$receiptNo)?>" method="post">
                    <div class="form-group">
                        <label for="">Part Name:</label>
                        <input list="browsers" class="form-control" name="inventoryItem">
                        <datalist id="browsers" >
                            <?php foreach ($stock as $s): ?>
                            <option value="<?=$s->partName?>">
                                <?php endforeach; ?>
                        </datalist>
                    </div>
                    <div class="row">
                        <div class="form-group col-6">
                            <label for="">Quantity: </label>
                            <input type="number" step=0.01 class="form-control" name='quantity' required>
                        </div>
                        <div class="form-group col-6">
                            <label for="">Unit Of Measure: </label>
                            <input list="units" class="form-control" name='units' required>
                            <datalist id="units">
                                <?php foreach($units as $unit): ?>
                                    <option value="<?=$unit->unit?>"/>
                                <?php endforeach; ?>
                            </datalist>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="">Unit Price</label>
                        <input type="number" class="form-control" name="unitPrice" required>
                        <input type="hidden" name="receiptId" value="<?=$receiptNo?>"/>
                    </div>
            </div>
            <div class="modal-footer">
                <input type="submit" value="Save Item" class="btn btn-primary btn-md"/>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    function fetchstock() {
        var data={id:$('#partName').val()};
        //alert(data.id);
        $.post("<?=base_url('receipts/fetch_inventory')?>", data,
            function (data, textStatus, jqXHR) {
                if(data.success){
                    $("#partNo").val(data.stock_item.partNo);
                    $("#inventoryId").val(data.stock_item.id);
                    $("#unit").text('( '+data.stock_item.unitOfMeasure+' )');
                }
            },
            "JSON"
        );
    }
</script>
<?php $this->endsection(); ?>
