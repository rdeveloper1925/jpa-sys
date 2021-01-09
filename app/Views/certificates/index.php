<?php $this->extend('layouts/app') ?>
<?php $this->section('content'); ?>
<div class="row p-0">
    <div class="col-md-12 col-sm-12">
        <a href="<?=base_url('certificates/new')?>" class="btn btn-primary btn-md">
            New Completion Certificate
        </a>
    </div>
    <div class="col-12 mt-3">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Completion Certificates</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered" id="datatable" width="100%" cellspacing="0">
                        <thead>
                        <tr>
                            <th>Date In</th>
                            <th>Part Name</th>
                            <th>Part No</th>
                            <th>Qty In Store</th>
                            <th>Bal In Store</th>
                            <th>Supplied By</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th>Date In</th>
                            <th>Part Name</th>
                            <th>Part No</th>
                            <th>Qty In Store</th>
                            <th>Bal In Store</th>
                            <th>Supplied By</th>
                            <th>Action</th>
                        </tr>
                        </tfoot>
                        <tbody>
                        <?php foreach ($certificates as $i) : ?>
                            <tr>
                                <td><?=date('d-m-Y',strtotime($i->dateIn))?></td>
                                <td><?=$i->partName?></td>
                                <td><?=$i->partNo?></td>
                                <td><?=$i->quantityInStore?></td>
                                <td><?=$i->balanceInStore?></td>
                                <td><?=$i->suppliedBy?></td>
                                <td>
                                    <a href="<?=base_url('inventory/see/'.$i->id)?>" class="btn btn-sm btn-primary">View</a>
                                    <a href="<?=base_url('inventory/edit/'.$i->id)?>"  class="btn btn-sm btn-warning">Edit</a>

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
<script>
    function del(id) {
        if(confirm("Are you sure you want to delete this Inventory Item?")){
            window.location.href="<?=base_url('inventory/delete_inventory')?>/"+id;
        }
    }
</script>
<!-- Button trigger modal -->

<?php $this->endsection(); ?>
