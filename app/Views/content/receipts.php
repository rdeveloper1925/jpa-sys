<?php $this->extend('layouts/app') ?>
<?php $this->section('content'); ?>
<div class="row">
	<div class="col-md-12 col-sm-12">
		<a href="<?=base_url('receipts/create')?>" class="btn btn-success"><i class="fa fa-plus" aria-hidden="true"></i> Create New Receipt</a>
	</div>
	<div class="col-12 mt-3">
		<div class="card shadow mb-4">
			<div class="card-header py-3">
				<h6 class="m-0 font-weight-bold text-primary">Issued Receipts</h6>
			</div>
			<div class="card-body">
				<div class="table-responsive">
					<table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
						<thead>
						<tr>
							<th>Receipt no</th>
							<th>Narration</th>
							<th>Ref No.</th>
							<th>Currency</th>
							<th>Account Name</th>
							<th>Actions</th>
						</tr>
						</thead>
						<tfoot>
						<tr>
							<th>Receipt no</th>
							<th>Narration</th>
							<th>Ref No.</th>
							<th>Currency</th>
							<th>Account Name</th>
							<th>Actions</th>
						</tr>
						</tfoot>
						<tbody>
						<?php foreach ($receipts as $receipt) : ?>
							<tr>
								<td><?=$receipt->receiptId?></td>
								<td><?=$receipt->narration?></td>
								<td><?=$receipt->refNo?></td>
								<td><?=$receipt->currency?></td>
                                <td><?=$receipt->accountName?></td>
								<td>
									<a href="<?=base_url('receipts/receipt_items2/'.$receipt->receiptId)?>" class="btn btn-sm btn-primary"><i class="fa fa-eye" aria-hidden="true"></i>View </a>
									<a href="#" onclick="del(<?=$receipt->receiptId?>)" class="btn btn-sm btn-danger"><i class="fa fa-trash" aria-hidden="true"></i>Delete </a>
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
        if(confirm("Are you sure you want to delete this Receipt?")){
            window.location.href="<?=base_url('receipts/delete_receipt')?>/"+id;
        }
    }
</script>
<?php $this->endsection(); ?>
