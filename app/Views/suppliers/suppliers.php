<?php $this->extend('layouts/app') ?>
<?php $this->section('content'); ?>
<div class="row">
    <div class="col-md-12 col-sm-12">
        <button type="button" class="btn btn-success btn-md" data-toggle="modal" data-target="#modelId">
            New Supplier
        </button>
    </div>
    <div class="col-12 mt-3">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Suppliers</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                        <tr>
                            <th>Supplier Id</th>
                            <th>Supplier Name</th>
                            <th>Contact</th>
                            <th>Balance</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th>Supplier Id</th>
                            <th>Supplier Name</th>
                            <th>Contact</th>
                            <th>Balance</th>
                            <th>Actions</th>
                        </tr>
                        </tfoot>
                        <tbody>
                        <?php foreach ($suppliers as $supplier): ?>
                        <tr>
                            <td><?=$supplier->id?></td>
                            <td><?=$supplier->supplier_name?></td>
                            <td><?=$supplier->contact?></td>
                            <td><?=$supplier->balance?></td>
                            <td>
                                <a href="<?=base_url('suppliers/view_ledger/'.$supplier->id)?>" class="btn btn-primary btn-sm">View Ledger</a>
                                <button type="button" class="btn btn-warning btn-sm editing" id="<?=$supplier->id?>" data-toggle="modal" data-target="#modelId1">
                                    Edit Details
                                </button>
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
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">New Supplier</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
            </div>
            <div class="modal-body">
                <form action="<?=base_url('suppliers/save')?>" method="post">
                    <div class="form-group">
                        <label for="name">Supplier Name: </label>
                        <input class="form-control" name="name" required id="name"/>
                    </div>
                    <div class="form-group">
                        <label for="supplier_name">Supplier Contact</label>
                        <input type="text" class="form-control" name="supplier_contact" required/>
                    </div>

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
<script>
    $(".editing").on('click',function () {
        var id=$(this).attr('id');
        $.post("<?=base_url('suppliers/fetch')?>", {id:id},
            function (data, textStatus, jqXHR) {
                if(data.success){
                    $('#sup_name').val(data.supplier_name);
                    $('#sup_contact').val(data.contact);
                    $('#id').val(data.id);
                }
            },
            "JSON"
        );
    })
</script>
<script>
    function del(id) {
        if(confirm("Are you sure you want to delete this invoice?")){
            window.location.href="<?=base_url('proforma/delete_invoice')?>/"+id;
        }
    }
</script>
<?php $this->endsection(); ?>
