<?php $this->extend('layouts/app') ?>
<?php $this->section('content'); ?>
<div class="row">
    <div class="col-md-6 col-sm-6">
        <button type="button" class="btn btn-success btn-md" data-toggle="modal" data-target="#modelId">
            New Voucher item
        </button>
    </div>
    <div class="col-12 mt-3">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Add Voucher Items</h6>
            </div>
            <div class="card-body">
                <form method="post" >
                    <div class="row">
                        <div class="form-group col-6">
                            <label>Name of Payee</label>
                            <input type="text" class="form-control" disabled value="<?=$voucher['name']?>" required name="name" />
                        </div>
                        <div class="form-group col-6">
                            <label>Company</label>
                            <input list="browsers1" class="form-control" disabled value="<?=$voucher['supplier']?>" name="supplier" id="supplier">
                            <datalist id="browsers1" >
                                <?php foreach ($suppliers as $s): ?>
                                <option value="<?=$s->supplier_name?>">
                                    <?php endforeach; ?>
                            </datalist>
                        </div>

                        <div class="form-group col-6">
                            <label>Address</label>
                            <input type="text" class="form-control" disabled value="<?=$voucher['address']?>" required name="address" />
                        </div>
                        <div class="form-group col-6">
                            <label>Cheque no</label>
                            <input type="text" class="form-control" disabled value="<?=$voucher['chequeNo']?>" required name="chequeNo" />
                        </div>
                        <div class="form-group col-6">
                            <label>Prepared By:</label>
                            <input type="text" class="form-control" disabled value="<?=$voucher['maker']?>" required name="maker" />
                        </div>
                        <div class="form-group col-6">
                            <label>Date</label>
                            <input type="date" class="form-control" disabled required value="<?=$voucher['date']?>" name="date" />
                        </div>
                        <div class="form-group col-6">
                            <label>Passed by;</label>
                            <input type="text" class="form-control" disabled value="<?=$voucher['passer']?>" name="passer" />
                        </div>
                        <div class="form-group col-6">
                            <label>Authorized by:</label>
                            <input type="text" class="form-control" disabled value="<?=$voucher['authorizer']?>" name="authorizer" />
                        </div>
                        <div class="form-group col-6">
                            <label>Received/Posted/Delivered by:</label>
                            <input type="text" class="form-control" disabled value="<?=$voucher['receiver']?>" name="receiver" />
                        </div>
                        <div class="col-12">
                            <!--<input type="submit" class="btn btn-md btn-success" value="Save Voucher Details">-->
                            <br>
                        </div>

                        <div class="col-12">
                            <table class="table table-sm table-bordered table-success">
                                <thead>
                                <th>Particulars</th>
                                <th>Code</th>
                                <th>Amount</th>
                                <th>Action</th>
                                </thead>
                                <tfoot>
                                <th>Particulars</th>
                                <th>Code</th>
                                <th>Amount</th>
                                <th>Action</th>
                                </tfoot>
                                <tbody>
                                <?php if (isset($items)): ?>
                                <?php foreach ($items as $i): ?>
                                    <tr>
                                        <td><?=$i->particulars?></td>
                                        <td><?=$i->code?></td>
                                        <td><?=$i->amount?></td>
                                        <td>
                                            <a href="<?=base_url('suppliers/delete_voucher_item/'.$i->id.'/'.$voucher['id'])?>" class="btn btn-sm btn-danger">Delete</a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </form>
            </div>
            <a href="<?=base_url('suppliers/generate_voucher/'.$voucher['id'])?>" class="btn btn-success btn-md">Generate Voucher</a>
        </div>
    </div>
</div>

<!-- Button trigger modal -->


<!-- Modal -->
<div class="modal fade" id="modelId" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">New Supplier</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="<?=base_url('suppliers/save_voucher_item')?>" method="post">
                    <div class="form-group">
                        <label for="name">Particulars: </label>
                        <input type="text" class="form-control" name="particulars" required id="name"/>
                    </div>
                    <div class="form-group">
                        <label for="name">Code: </label>
                        <input type="text" class="form-control" name="code" required id="name"/>
                    </div>
                    <div class="form-group">
                        <label for="name">Amount: </label>
                        <input type="number" class="form-control" name="amount" required id="name"/>
                    </div>
                    <input type="hidden" name="voucherId" value="<?=$voucher['id']?>">

            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Save</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Button trigger modal -->



<!-- Modal -->
<div class="modal fade" id="modelId1" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit supplier Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="<?=base_url('suppliers/edit')?>">
                    <div class="form-group">
                        <label for="supplier_name">Supplier Name</label>
                        <input type="text" class="form-control" id="sup_name" name="supplier_name" required/>
                    </div>
                    <div class="form-group">
                        <label for="supplier_name">Supplier Contact</label>
                        <input type="hidden" id="id" name="id"/>
                        <input type="text" class="form-control" id="sup_contact" name="supplier_contact" required/>
                    </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Save Edits</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $this->endsection(); ?>
