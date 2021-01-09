<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">
	<link rel="icon" href="<?=base_url('assets/img/logo.png')?>"/>
    <script src="<?=base_url('assets/vendor/jquery/jquery.min.js')?>"></script>
  <title>Japan Auto Care - Invoicer</title>

  <!-- Custom fonts for this template-->
  <link href="<?=base_url('assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet')?>" type="text/css">

  <!-- Custom styles for this template-->
  <link href="<?=base_url('assets/css/sb-admin-2.min.css')?>" rel="stylesheet">
  <link href="<?=base_url('assets/vendor/datatables/dataTables.bootstrap4.min.css')?>" rel="stylesheet">
	<link href="<?=base_url('assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet')?>" type="text/css">
	<style>
		.my-nav{
			font-weight: bolder;font-size: 17px;
		}
	</style>
</head>

<body id="page-top">

  <!-- Page Wrapper -->
  <div id="wrapper">

    <!-- Sidebar -->
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

      <!-- Sidebar - Brand -->
      <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
        <div class="sidebar-brand-icon rotate-n-15">
          <i class="fas fa-laugh-wink"></i>
        </div>
        <div class="sidebar-brand-text mx-3">Japan Auto Care</div>
      </a>

      <!-- Divider -->
      <hr class="sidebar-divider my-0">

      <!-- Nav Item - Dashboard -->
      <li class="nav-item">
        <a class="nav-link" href="<?=base_url('pages/')?>">
          <i class="fas fa-fw fa-tachometer-alt"></i>
          <span class="my-nav">Dashboard</span></a>
      </li>

      

      <!-- Nav Item - Charts -->
		<li class="nav-item">
			<a class="nav-link" href="<?=base_url('inventory')?>">
				<i class="fas fa-fw fa-list-ul"></i>
				<span class="my-nav">Inventory</span></a>
		</li>
        <li class="nav-item">
            <a class="nav-link" href="<?=base_url('suppliers')?>">
                <i class="fas fa-fw fa-list-ul"></i>
                <span class="my-nav">Suppliers</span></a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="<?=base_url('customers')?>">
                <i class="fas fa-fw fa-list-ul"></i>
                <span class="my-nav">Customers</span></a>
        </li>
      <li class="nav-item">
        <a class="nav-link" href="<?=base_url('invoices')?>">
          <i class="fas fa-fw fa-list-ul"></i>
          <span class="my-nav">Tax Invoices</span></a>
      </li>
        <li class="nav-item">
            <a class="nav-link" href="<?=base_url('proforma')?>">
                <i class="fas fa-fw fa-list-ul"></i>
                <span class="my-nav">Proforma Invoices</span></a>
        </li>

		<li class="nav-item">
			<a class="nav-link" href="<?=base_url('receipts')?>">
				<i class="fas fa-fw fa-list-ul"></i>
				<span class="my-nav">Receipts</span></a>
		</li>
        <li class="nav-item">
            <a class="nav-link" href="<?=base_url('reports')?>">
                <i class="fas fa-fw fa-list-ul"></i>
                <span class="my-nav">Reports </span></a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="<?=base_url('suppliers/cheque_vouchers')?>">
                <i class="fas fa-fw fa-list-ul"></i>
                <span class="my-nav">Cheque Vouchers</span></a>
        </li>

		<?php if (!strcmp(\Config\Services::session()->get('accessLevel'),'ADMINISTRATOR')): ?>
		<li class="nav-item">
			<a class="nav-link" href="<?=base_url('users')?>">
				<i class="fas fa-fw fa-list-ul"></i>
				<span class="my-nav">User Management</span></a>
		</li>
		<?php endif; ?>

		<li class="nav-item">
			<a class="nav-link" href="<?=base_url('auth/logout')?>">
				<i class="fas fa-fw fa-list-ul"></i>
				<span class="my-nav">Logout</span></a>
		</li>


      <!-- Divider -->
      <hr class="sidebar-divider d-none d-md-block">


    </ul>
    <!-- End of Sidebar -->

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

      <!-- Main Content -->
      <div id="content">

        <!-- Topbar -->
        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

          <!-- Sidebar Toggle (Topbar) -->
          <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
            <i class="fa fa-bars"></i>
          </button>

          <!-- Topbar Navbar -->
          <ul class="navbar-nav ml-auto">

            <!-- Nav Item - Search Dropdown (Visible Only XS) -->
            <li class="nav-item dropdown no-arrow d-sm-none">
              <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-search fa-fw"></i>
              </a>
              

            <div class="topbar-divider d-none d-sm-block"></div>

            <!-- Nav Item - User Information -->
            <li class="nav-item dropdown no-arrow">
              <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?php echo \Config\Services::session()->get('fullName');?></span>
                <img class="img-profile rounded-circle" src="<?=base_url('assets/img/male-user.png')?>">
              </a>
              <!-- Dropdown - User Information -->
              <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                
                <div class="dropdown-divider"></div>
                  <a class="dropdown-item" href="<?=base_url('users/changePassword')?>">
                      <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                      Change Password
                  </a>
                <a class="dropdown-item" href="<?=base_url('auth/logout')?>">
                  <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                  Logout
                </a>
              </div>
            </li>

          </ul>

        </nav>
        <!-- End of Topbar -->

        <!-- Begin Page Content -->
        <div class="container-fluid">

          <!-- Page Heading -->
          <h1 class="h3 mb-4 text-gray-800"><?=$title?></h1>
          <?php $this->rendersection('content')?>

        </div>
        <!-- /.container-fluid -->

      </div>
      <!-- End of Main Content -->

      <!-- Footer -->
      <footer class="sticky-footer bg-white">
        <div class="container my-auto">
          <div class="copyright text-center my-auto">
			  <span>Copyright &copy; JAPAN AUTO CARE 2020 || Powered by <a href="http://www.alcore-tech.com/" target="_blank">Alcore-tech</a></span>
          </div>
        </div>
      </footer>
      <!-- End of Footer -->

    </div>
    <!-- End of Content Wrapper -->

  </div>
  <!-- End of Page Wrapper -->

  <!-- Scroll to Top Button-->
  <a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
  </a>

  <!-- Bootstrap core JavaScript-->
  <script src="<?=base_url('assets/vendor/jquery/jquery.min.js')?>"></script>
  <script src="<?=base_url('assets/vendor/bootstrap/js/bootstrap.bundle.min.js')?>"></script>

  <!-- Core plugin JavaScript-->
  <script src="<?=base_url('assets/vendor/jquery-easing/jquery.easing.min.js')?>"></script>

  <!-- Custom scripts for all pages-->
  <script src="<?=base_url('assets/js/sb-admin-2.min.js')?>"></script>

  <script src="<?=base_url('assets/vendor/datatables/jquery.dataTables.min.js')?>"></script>
  <script src="<?=base_url('assets/vendor/datatables/dataTables.bootstrap4.min.js')?>"></script>

   <!-- Page level custom scripts -->
   <script src="<?=base_url('assets/js/demo/datatables-demo.js')?>"></script>

   <script>
   $('#datatable').dataTable({
     "bPaginate":false,
     "paging":false
   })
   </script>

</body>

</html>
