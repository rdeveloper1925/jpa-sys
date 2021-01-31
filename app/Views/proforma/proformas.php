<?php $this->extend('layouts/app') ?>
<?php $this->section('content'); ?>
<div class="row">
    <div class="col-md-12 col-sm-12">
        <a href="<?=base_url('proforma/create')?>" class="btn btn-success"><i class="fa fa-plus" aria-hidden="true"></i> Create New Proforma</a>
    </div>
    <?php if(\Config\Services::session()->has('success')): ?>
        <div class="alert alert-success alert-dismissible fade show col-12" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <strong>Success! </strong> <?=\Config\Services::session()->get('success')?>
        </div>
    <?php endif; ?>
    <div class="col-12 mt-3">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Issued Proforma Invoices</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm" id="datatable" width="100%" cellspacing="0">
                        <thead>
                        <tr>
                            <th>Proforma no</th>
                            <th>Date</th>
                            <th>Customer</th>
                            <th>Phone</th>
                            <th>Contact Person</th>
                            <th>Car Reg No</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th>Proforma no</th>
                            <th>Date</th>
                            <th>Customer</th>
                            <th>Phone</th>
                            <th>Contact Person</th>
                            <th>Car Reg No</th>
                            <th>Actions</th>
                        </tr>
                        </tfoot>
                        <tbody>
                        <?php foreach ($invoices as $invoice) : ?>
                            <tr>
                                <td><?=$invoice->invoiceId?></td>
                                <td><?=date('d-M-Y',strtotime($invoice->date))?></td>
                                <td><?=$invoice->customerName?></td>
                                <td><?=$invoice->phone?></td>
                                <td><?=$invoice->contactPerson?></td>
                                <td><?=$invoice->carRegNo?></td>
                                <td>
                                    <a href="<?=base_url('proforma/tax_and_discounts/'.$invoice->invoiceId)?>" class="btn btn-sm btn-primary"><i class="fa fa-eye" aria-hidden="true"></i>View </a>
                                    <a href="#" onclick="del(<?=$invoice->invoiceId?>)" class="btn btn-sm btn-danger"><i class="fa fa-trash" aria-hidden="true"></i>Delete </a>
                                    <a href="<?=base_url('proforma/invoice_items_edit/'.$invoice->invoiceId)?>" class="btn btn-warning">Edit items</a>
                                    <a href="<?=base_url('proforma/custdetails_edit/'.$invoice->invoiceId)?>" class="btn btn-warning">Edit details</a>
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
        if(confirm("Are you sure you want to delete this invoice?")){
            window.location.href="<?=base_url('proforma/delete_invoice')?>/"+id;
        }
    }
</script>
<?php $this->endsection(); ?>
