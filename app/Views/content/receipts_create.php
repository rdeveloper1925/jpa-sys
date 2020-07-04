<?php $this->extend('layouts/app'); ?>
<?php $this->section ('content') ?>
<div class="row">
	<div class="col-md-12 container">
		<div class="card shadow mb-4">
			<div class="card-header py-3">
				<h6 class="m-0 font-weight-bold text-primary">Receipt Info</h6>
			</div>
			<div class="card-body">
				<form action="<?=base_url('receipts/save')?>" method="post">
					<div class="row">
						<div class="form-group col-4">
							<label for="">Receipt No.</label>
							<input type="text" class="form-control" value="Auto Generated" disabled >
						</div>
						<div class="form-group col-4">
							<label for="">Ref No: </label>
							<input type="number" pattern="[0-9]*" class="form-control" name="refNo" required>
						</div>
						<div class="form-group col-4">
							<label for="">Account Name: </label>
							<input type="text" class="form-control" name="accountName" required>
						</div>
						<div class="form-group col-4">
							<label for="">Narration: </label>
							<input type="text" class="form-control" name="narration" required>
						</div>
						<div class="form-group col-4">
							<label for="">Date: </label>
							<input type="date" class="form-control" name="date" required>
						</div>
						<div class="form-group col-4">
							<label for="">Ref Date: </label>
							<input type="date" class="form-control" name="refDate" required>
						</div>
						<div class="form-group col-4">
							<label for="">Received From: </label>
							<input type="text" class="form-control" name="receivedFrom" required>
						</div>
						<hr>
						<div class="form-group col-4">
							<label for="">Description: </label>
							<input type="text" class="form-control" name="description" required>
						</div>
						<div class="form-group col-4">
							<label for="">Currency: </label>
							<input type="text" class="form-control" name="currency" required>
						</div>
						<div class="form-group col-4">
							<label for="">Received by: </label>
							<input type="text" class="form-control" name="receivedBy" required>
						</div>

						<div class="col-12">
							<button type="submit" class="btn btn-success">Proceed to Receipt Items <i class="fa fa-forward" aria-hidden="true"></i></button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>

</div>
<?php $this->endsection();?>
