<?php $this->extend('layouts/app') ?>
<?php $this->section('content'); ?>
<div class="row p-0">
    <div class="col-md-12 col-sm-12">
        <button type="button" class="btn btn-primary btn-md" data-toggle="modal" data-target="#modelId">
            New Customer
        </button>
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
                <h6 class="m-0 font-weight-bold text-primary">Customers</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm" id="datatable" width="100%" cellspacing="0">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Contact Person</th>
                            <th>Address</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Tin No.</th>
                            <th>Country</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th>Name</th>
                            <th>Contact Person</th>
                            <th>Address</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Tin No.</th>
                            <th>Country</th>
                            <th>Action</th>
                        </tr>
                        </tfoot>
                        <tbody>
                        <?php foreach ($customers as $c) : ?>
                            <tr>
                                <td><?=$c->customerName?></td>
                                <td><?=$c->contactPerson?></td>
                                <td><?=$c->address?></td>
                                <td><?=$c->email?></td>
                                <td><?=$c->phone?></td>
                                <td><?=$c->tinNo?></td>
                                <td><?=$c->areaCountry?></td>
                                <td>
                                    <a href="<?=base_url('customers/edit/'.$c->id)?>" class="btn btn-sm btn-warning">Edit</a>
                                    <!--<a href="<?=base_url('customers/delete/'.$c->id)?>" class="btn btn-sm btn-danger">Delete</a>-->
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
<!-- Button trigger modal -->


<!-- Modal -->
<div class="modal fade" id="modelId" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create New Customer</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="<?=base_url('customers/save')?>" method="post">
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="">Customer Name:</label>
                            <input type="text" class="form-control " name="customerName" required />
                        </div>
                        <div class="form-group col-md-6">
                            <label for="">Contact Person:</label>
                            <input type="text" class="form-control " name="contactPerson" required />
                        </div>
                        <div class="form-group col-md-6">
                            <label for="">Address:</label>
                            <input type="text" class="form-control " name="address" required />
                        </div>
                        <div class="form-group col-md-6">
                            <label for="">Phone:</label>
                            <input type="text" pattern="[-+]?\d*" class="form-control " name="phone" required />
                        </div>
                        <div class="form-group col-md-6">
                            <label for="">Email:</label>
                            <input type="text" class="form-control " name="email" required />
                        </div>
                        <div class="form-group col-md-6">
                            <label for="">Tin No:</label>
                            <input type="number" pattern="[0-9]*" class="form-control " name="tinNo" required />
                        </div>
                        <div class="form-group col-md-6">
                            <label for="">Area Country:</label>
                            <input type="text" class="form-control " name="areaCountry" required />
                        </div>
                        <div class="col-md-12">
                            <input type="submit" value="Save" class="btn btn-success btn-md"/>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php $this->endsection(); ?>
