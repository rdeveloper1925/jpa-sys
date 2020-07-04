<?php $this->extend('layouts/app') ?>
<?php $this->section('content'); ?>
<div class="row">
    <div class="col-12">
        <a href="<?=base_url('proforma/confirm/'.$invoiceId)?>" class="btn btn-success">Confirm items</a>
    </div>
    <div class="col-12 mt-3">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Items</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                        <tr>
                            <th>Item</th>
                            <th>Quantity</th>
                            <th>Unit Cost</th>
                            <th>Units</th>
                            <th>Total</th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th>Item</th>
                            <th>Quantity</th>
                            <th>Unit Cost</th>
                            <th>Units</th>
                            <th>Total</th>
                        </tr>
                        </tfoot>
                        <tbody>
                        <?php foreach ($rows as $r) : ?>
                            <tr>
                                <td><?=$r->inventoryItem?></td>
                                <td><?=$r->quantity?></td>
                                <td><?=$r->unitCost?></td>
                                <td><?=$r->units?></td>
                                <td><?=$r->total?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function del(id) {
        if(confirm("Are you sure you want to delete this invoice?")){
            window.location.href="<?=base_url('proforma/delete_invoice')?>/"+id;
        }
    }
</script>
<?php $this->endsection(); ?>
