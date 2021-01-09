<?php $this->extend('layouts/app') ?>
<?php $this->section('content'); ?>
<div class="row">
    <div class="col-md-12 container">
        <div id="alerter" class="col-12"></div>
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Select a Proforma</h6>
            </div>
            <div class="card-body">
                <form method="post" action="<?=base_url('invoices/search/'.$taxId)?>">
                    <div class="form-group">
                        <label for="proforma">Proforma No.</label>
                        <input type="number" class="form-control" name="invoiceNo" id="invoiceNo" required/>
                    </div>
                    <input type="submit" value="Search" class="btn btn-success"/>
                </form><br>
                <button type="button" class="btn btn-primary btn-md" data-toggle="modal" data-target="#modelId">
                    Add Item
                </button>
                <!--<button id="search" class="btn btn-success btn-md" onclick="search()">Search Proforma No</button>-->
                <p id="ttl"></p>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm"  width="100%" cellspacing="0">
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
                        <tbody>
                        <?php if(!empty($items)): ?>
                        <?php foreach ($items as $item): ?>
                            <tr>
                                <td><?=$item->id?></td>
                                <td><?=$item->inventoryItem?></td>
                                <td><?=$item->quantity?><?php echo ' ('.$item->units.')'?></td>
                                <td><?=$item->unitCost?></td>
                                <td><?=$item->quantity*$item->unitCost?></td>
                                <td><a href="<?=base_url('invoices/delete_temp/'.$item->id.'/'.$taxId.'/'.$proforma->invoiceId)?>" class="btn btn-sm btn-danger">Delete</a> </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                    <?php if (!empty($proforma)): ?>
                    <form method="post" action="<?=base_url('invoices/confirm')?>">
                        <div class="form-group">
                            <input type="hidden" name="invoiceId" value="<?=$proforma->invoiceId?>" id="confirm"/>
                            <input type="hidden" name="taxId" value="<?=$taxId?>"/>
                        </div>
                        <input type="submit" value="Confirm" class="btn btn-success btn-md"/>
                    </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function search() {
        let data={invoiceId:$('#invoiceNo').val()};
        $.post("<?=base_url('invoices/fetch_proforma_items')?>",data,function (d,s,x) {
            var d=JSON.parse(d);
            if (d.success){
                $('#theBody').empty();
                $('#confirm').val(d.proforma.invoiceId);
                $('#ttl').text("Invoice No."+d.proforma.invoiceId+", RegNo."+d.proforma.carRegNo+" Car Type:"+d.proforma.carType);
                $.each(d.items,function (i,v) {
                    console.log(v);
                    $('#theBody').append("<tr><td>"+v.id+"</td><td>"+v.inventoryItem+"</td><td>"+v.quantity+"</td><td>"+v.unitCost+"</td><td>"+v.quantity*v.unitCost+"</td></tr>");
                })
            }else{
                alert('The proforma Number was not found. Please try again');
            }
        })
    }
</script>
<!-- Button trigger modal -->


<!-- Modal -->
<div class="modal fade" id="modelId" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Item</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
            </div>
            <div class="modal-body">
                <?php if(isset($proforma->invoiceId)): ?>
                <form action="<?=base_url('invoices/new_temp_item/'.$proforma->invoiceId)?>" method="post">
                    <?php else: ?>
                    <form>
                        <?php endif; ?>
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
                            <input type="text" class="form-control" name='units' required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="">Unit Price</label>
                        <input type="number" class="form-control" name="unitPrice" required>
                        <input type="hidden" name="taxId" value="<?=$taxId?>"/>
                    </div>
            </div>
            <div class="modal-footer">
                <input type="submit" value="Save Item" class="btn btn-primary btn-md"/>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $this->endsection(); ?>
