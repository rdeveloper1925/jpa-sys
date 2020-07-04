<?php $this->extend('layouts/app') ?>
<?php $this->section('content'); ?>
<div class="row">
	<div class="col-md-12 col-sm-12">
		<button type="button" class="btn btn-primary btn-md" data-toggle="modal" data-target="#modelId">
			Create New User
		</button>
	</div>
	<div class="col-12 mt-3">
		<div class="card shadow mb-4">
			<div class="card-header py-3">
				<h6 class="m-0 font-weight-bold text-primary">Registered Users</h6>
			</div>
			<div class="card-body">
				<div class="table-responsive">
					<table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
						<thead>
						<tr>
							<th>User no.</th>
							<th>Username</th>
							<th>Full Name</th>
							<th>Access Level</th>
							<th>Actions</th>
						</tr>
						</thead>
						<tfoot>
						<tr>
							<th>User no.</th>
							<th>Username</th>
							<th>Full Name</th>
							<th>Access Level</th>
							<th>Actions</th>
						</tr>
						</tfoot>
						<tbody>
						<?php foreach ($users as $user) : ?>
							<tr>
								<td><?=$user->id?></td>
								<td><?=$user->username?></td>
								<td><?=$user->fullName?></td>
								<td><?=$user->accessLevel?></td>
								<td>
									<a href="<?=base_url('users/reset_password/'.$user->id)?>" class="btn btn-sm btn-primary"><i class="fa fa-eye" aria-hidden="true"></i>Reset Password </a>
									<a href="#" onclick="del(<?=$user->id?>)" class="btn btn-sm btn-danger"><i class="fa fa-trash" aria-hidden="true"></i>Delete </a>
                                    <a href="#" id="<?=$user->id?>" class="btn btn-sm btn-warning editor">Edit</a>
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

<!-- Modal -->
<div class="modal fade" id="modelId2" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit User Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
			<form action="<?=base_url('users/update')?>" method="post">
					<div class="form-group">
						<label for="">Username: </label>
						<input type="text" class="form-control" name="username" id="username" required/>
					</div>
					<div class="form-group">
						<label for="">Full Name: </label>
						<input type="text" class="form-control" name="fullName" id="fullName" required/>
						<input type='hidden' name='userId' id="userId"/>
					</div>
					<div class="form-group">
						<label for="">Access Level</label>
						<select class="form-control" required name="accessLevel" >
							<option>ADMINISTRATOR</option>
							<option>SUPERVISOR</option>
							<option>ACCOUNTANT</option>
                            <option>RECEPTIONIST</option>
                            <option>MARKETEER</option>
                            <option>PROCUREMENT</option>
                            <option>STORE-KEEPER</option>
						</select>
					</div>
					<input type="submit" class="btn btn-success btn-md" value="Save Edits"/>
				</form>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
		$('.editor').on('click',function(){
			var id=$(this).attr('id');
			$('#modelId2').modal('show');
			$.post("<?=base_url('users/edit/')?>", {id:id},
				function (data, textStatus, jqXHR) {
					//alert(data.username);
					$('#username').val(data.username);
					$('#fullName').val(data.fullName);
                    $('#userId').val(data.id);
				},
				"JSON"
			);
		});
	});
</script>


<!-- Button trigger modal -->


<!-- Modal -->
<div class="modal fade" id="modelId" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Create New User</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form action="<?=base_url('users/save')?>" method="post">
					<div class="form-group">
						<label for="">Username: </label>
						<input type="text" class="form-control" name="username" required/>
					</div>
					<div class="form-group">
						<label for="">Full Name: </label>
						<input type="text" class="form-control" name="fullName" required/>
					</div>
					<div class="form-group">
						<label for="">Password: </label>
						<input type="password" class="form-control" name="password" required/>
					</div>
					<div class="form-group">
						<label for="">Re-type Password: </label>
						<input type="password" class="form-control" name="password2" required/>
					</div>
					<div class="form-group">
						<label for="">Access Level</label>
						<select class="form-control" required name="accessLevel" >
							<option>ADMINISTRATOR</option>
							<option>SUPERVISOR</option>
							<option>ACCOUNTANT</option>
                            <option>RECEPTIONIST</option>
                            <option>MARKETEER</option>
                            <option>PROCUREMENT</option>
                            <option>STORE-KEEPER</option>
						</select>
					</div>
					<input type="submit" class="btn btn-success btn-md" value="Save User"/>
				</form>
			</div>
		</div>
	</div>
</div>
<script>
    function del(id) {
        if(confirm("Are you sure you want to delete this user?")){
            window.location.href="<?=base_url('users/delete')?>/"+id;
        }
    }
</script>
<?php $this->endsection(); ?>
