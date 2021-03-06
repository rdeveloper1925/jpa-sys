<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>JAPAN AUTO CARE SYSTEM</title>

  <!-- Custom fonts for this template-->
  <link href="<?=base_url('assets/vendor/fontawesome-free/css/all.min.css')?>" rel="stylesheet" type="text/css">

  <!-- Custom styles for this template-->
  <link href="<?=base_url('assets/css/sb-admin-2.min.css')?>" rel="stylesheet">

</head>

<body class="bg-gradient-primary">

  <div class="container">

    <!-- Outer Row -->
    <div class="row justify-content-center">

      <div class="col-xl-6 col-lg-6 col-md-6">

        <div class="card o-hidden border-0 shadow-lg my-5">
          <div class="card-body p-0">
            <!-- Nested Row within Card Body -->
            <div class="row">
              <div class="col-lg-12">
                <div class="p-5">
                  <div class="text-center">
                    <h1 class="h4 text-gray-900 mb-4">JAPAN AUTO CARE</h1>
					  <h5 class="h5 text-black-50">Business Management System</h5>
                    <img src="<?=base_url('assets/img/logo.png')?>" height="150px" width="300px" alt="Image here" srcset="">
                  </div>
					<?php if(\Config\Services::session()->has('error')): ?>
                  <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                    <strong>ERROR! </strong> <?=\Config\Services::session()->get('error')?>
                  </div>
					<?php endif; ?>
                  
                  <script>
                    $(".alert").alert();
                  </script>
                  <form class="user" action="<?=base_url('auth/doLogin')?>" method="post">
                    <div class="form-group">
                      <input type="text" name="username" class="form-control form-control-user" required  placeholder="Username">
                    </div>
                    <div class="form-group">
                      <input type="password" name="password" class="form-control form-control-user" required placeholder="Password">
                    </div>
                    <input type="submit" class="btn btn-primary btn-user btn-block" value="Login"/>
					  <hr>
					<p class="small" style="text-align: center"> Copyright &copy; JAPAN AUTO CARE 2020 <br> Powered by <a href="alcore-tech.com">Alcore-tech</a></p>
                  </form>
                </div>

              </div>
            </div>
          </div>
        </div>

      </div>

    </div>

  </div>

  <!-- Bootstrap core JavaScript-->
  <script src="<?=base_url('assets/vendor/jquery/jquery.min.js')?>"></script>
  <script src="<?=base_url('assets/vendor/bootstrap/js/bootstrap.bundle.min.js')?>"></script>

  <!-- Core plugin JavaScript-->
  <script src="<?=base_url('assets/vendor/jquery-easing/jquery.easing.min.js')?>"></script>

  <!-- Custom scripts for all pages-->
  <script src="<?=base_url('assets/js/sb-admin-2.min.js')?>"></script>

</body>

</html>
