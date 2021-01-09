<?php $this->extend('layouts/app') ?>
<?php $this->section('content'); ?>
<div class="row p-0">
	<div class="col-md-12 col-sm-12">
		<button type="button" class="btn btn-primary btn-md" data-toggle="modal" data-target="#modelId">
			New Inventory Item
		</button>
	</div>
	<div class="col-12 mt-3">
		<div class="card shadow mb-4">
			<div class="card-header py-3">
				<h6 class="m-0 font-weight-bold text-primary">Inventory Items</h6>
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
						<?php foreach ($inventory as $i) : ?>
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


<!-- Modal -->
<div class="modal fade" id="modelId" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Create Inventory Item</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form action="<?=base_url('inventory/save')?>" method="post">
                    <div class="row">
					<div class="form-group col-md-6">
						<label for="">Date In:</label>
						<input type="date" class="form-control " name="dateIn" required />
					</div>
					<div class="form-group col-md-6">
						<label for="">Part Name:</label>
						<input type="text" class="form-control " name="partName" required />
					</div>
					<div class="form-group col-md-6">
						<label for="">Part No:</label>
						<input type="text" class="form-control " name="partNo" required />
					</div>
					<div class="form-group col-md-6">
						<label for="">Quantity In Store:</label>
						<input type="number" step=0.01 class="form-control " name="quantityInStore" required />
					</div>
					<div class="form-group col-md-6">
						<label for="">Supplied By:</label>
						<input type="text" class="form-control " name="suppliedBy" required />
					</div>
                        <div class="form-group col-md-6">
                            <label for="">Unit of Measure:</label>
                            <input type="text" class="form-control " name="unitOfMeasure" required />
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
