<?php $this->extend('layouts/app'); ?>
<?php $this->section('content'); ?>
<div class="row">
    <?php if(isset($fail)): ?>
    <div class="col-8">
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        <strong>Error: </strong> <?php echo $fail; ?>
    </div>
    </div>
    <?php endif; ?>
    <?php if(isset($success)): ?>
        <div class="col-8">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <strong>Success: </strong> <?php echo $success; ?>
            </div>
        </div>
    <?php endif; ?>

    <script>
        $(".alert").alert();
    </script>
    <div class="container col-6">
        <form action="<?=base_url('users/save_password_change')?>" method="post">
            <div class="form-group">
                <label for="">New Password:</label>
                <input type="password" maxlength="20" minlength="8" class="form-control" name="password" required/>
            </div>
            <div class="form-group">
                <label for="">Retype Password:</label>
                <input type="password" class="form-control" minlength="8" name="passwordRetype" required/>
            </div>
            <input type="submit" class="btn btn-md btn-success"/>
        </form>
    </div>
</div>
<?php $this->endsection(); ?>
