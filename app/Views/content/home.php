<?php $this->extend("layouts/app") ?>
<?php $this->section('content')?>
<div class="row">
			<div class="col-xl-3 col-md-6 mb-4">
              <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="text-md font-weight-bold text-primary text-uppercase mb-1">Invoices Issued</div>
                      <div class="h5 mb-0 font-weight-bold text-gray-800"><?=$invoices?></div>
						<p class="mb-0"style="text-align: right;"><a href="<?=base_url('invoices')?>" class="btn btn-sm btn-warning">View more</a> </p>
					</div>
                    <div class="col-auto">
                      <i class="fas fa-list-ol fa-2x text-gray-300"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>
	<div class="col-xl-3 col-md-6 mb-4">
		<div class="card border-left-success shadow h-100 py-2">
			<div class="card-body">
				<div class="row no-gutters align-items-center">
					<div class="col mr-2">
						<div class="text-md font-weight-bold text-primary text-uppercase mb-1">Receipts Issued</div>
						<div class="h5 mb-0 font-weight-bold text-gray-800"><?=$receipts?></div>
						<p class="mb-0"style="text-align: right;"><a href="<?=base_url('receipts')?>" class="btn btn-sm btn-success">View more</a> </p>
					</div>
					<div class="col-auto">
						<i class="fas fa-list-ol fa-2x text-gray-300"></i>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-xl-3 col-md-6 mb-4">
		<div class="card border-left-primary shadow h-100 py-2">
			<div class="card-body">
				<div class="row no-gutters align-items-center">
					<div class="col mr-2">
						<div class="text-md font-weight-bold text-primary text-uppercase mb-1">Registered Users</div>
						<div class="h5 mb-0 font-weight-bold text-gray-800"><?=$users?></div>
						<p class="mb-0"style="text-align: right;"><a href="<?=base_url('users')?>" class="btn btn-sm btn-primary">View more</a> </p>
					</div>
					<div class="col-auto">
						<i class="fas fa-list-ol fa-2x text-gray-300"></i>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-xl-3 col-md-6 mb-4">
		<div class="card border-left-danger shadow h-100 py-2">
			<div class="card-body">
				<div class="row no-gutters align-items-center">
					<div class="col mr-2">
						<div class="text-md font-weight-bold text-primary text-uppercase mb-1">Inventory Items</div>
						<div class="h5 mb-0 font-weight-bold text-gray-800"><?=$inventory?></div>
						<p class="mb-0"style="text-align: right;"><a href="<?=base_url('inventory')?>" class="btn btn-sm btn-danger">View more</a> </p>
					</div>
					<div class="col-auto">
						<i class="fas fa-list-ol fa-2x text-gray-300"></i>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-12">
		<center>
		<img src="<?=base_url('assets/img/logo.png')?>" alt="Logo" height="200px" width="300px" class="img-fluid">
		</center>
	</div>
</div>
<?php $this->endsection()?>
