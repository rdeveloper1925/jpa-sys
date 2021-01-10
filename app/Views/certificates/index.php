<?php $this->extend('layouts/app') ?>
<?php $this->section('content'); ?>
<div class="row p-0">
    <div class="col-md-12 col-sm-12">
        <a href="<?=base_url('certificates/new')?>" class="btn btn-primary btn-md">
            <i class="fa fa-plus-square"></i> New Completion Certificate
        </a>
    </div>
    <?php if(\CodeIgniter\Config\Services::session()->has('success')): ?>
    <div class="col-10 pt-3">
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong><i class="fa fa-heart" style="color:red"></i> Success!</strong> <?=\CodeIgniter\Config\Services::session()->get('success')?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    </div>
    <?php endif; ?>
    <?php if(\CodeIgniter\Config\Services::session()->has('fail')): ?>
        <div class="col-10 pt-3">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong><i class="fa fa-heart-broken" style="color:red"></i> Error!</strong> <?=\CodeIgniter\Config\Services::session()->get('fail')?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        </div>
    <?php endif; ?>
    <div class="col-12 mt-3">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fa fa-certificate"></i> Completion Certificates</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered" id="datatable" width="100%" cellspacing="0">
                        <thead>
                        <tr>
                            <th>Cert No.</th>
                            <th>Customer</th>
                            <th>Invoice No.</th>
                            <th>Engineer Name</th>
                            <th>Date Completed</th>
                            <th>Car Reg No.</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th>Cert No.</th>
                            <th>Customer</th>
                            <th>Invoice No.</th>
                            <th>Engineer Name</th>
                            <th>Date Completed</th>
                            <th>Car Reg No.</th>
                            <th>Action</th>
                        </tr>
                        </tfoot>
                        <tbody>
                        <?php foreach ($certificates as $i) : ?>
                            <tr>
                                <td><?=$i->id?></td>
                                <td><?=$i->customerName?></td>
                                <td><?=$i->invoiceNo?></td>
                                <td><?=$i->engineerName?></td>
                                <td><?=date('d-m-Y',strtotime($i->dateCompleted))?></td>
                                <td><?=$i->carRegNo?></td>
                                <td>
                                    <a href="<?=base_url('certificates/view/'.$i->id)?>" class="btn btn-sm btn-primary"><i class="fa fa-eye"></i> View</a>
                                    <a href="<?=base_url('certificates/edit/'.$i->id)?>"  class="btn btn-sm btn-warning"><i class="fa fa-pen"></i> Edit</a>
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
