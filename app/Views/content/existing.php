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
                </form>
                <!--<button id="search" class="btn btn-success btn-md" onclick="search()">Search Proforma No</button>-->
                <p id="ttl"></p>
                <div class="table-responsive">
                    <table class="table table-bordered"  width="100%" cellspacing="0">
                        <thead>
                        <tr>
                            <th>Serial No.</th>
                            <th>Part Name</th>
                            <th>Quantity</th>
                            <th>Unit Price</th>
                            <th>Total</th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th>Serial No.</th>
                            <th>Part Name</th>
                            <th>Quantity</th>
                            <th>Unit Price</th>
                            <th>Total</th>
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
                            </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                    <form method="post" action="<?=base_url('invoices/confirm')?>">
                        <div class="form-group">
                            <input type="hidden" name="invoiceId" value="<?=$proforma->invoiceId?>" id="confirm"/>
                            <input type="hidden" name="taxId" value="<?=$taxId?>"/>
                        </div>
                        <input type="submit" value="Confirm" class="btn btn-success btn-md"/>
                    </form>
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
<?php $this->endsection(); ?>
