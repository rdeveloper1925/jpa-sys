<?php $this->extend('layouts/app'); ?>
<?php $this->section ('content') ?>
<div class="row">
    <div class="col-md-6 container">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Generate Report</h6>
            </div>
            <div class="card-body">
               <form action="<?=base_url('reports/generate')?>" method="post">
                   <?php if(isset($error)): ?>
                   <div class="alert alert-danger alert-dismissible fade show" role="alert">
                       <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                           <span aria-hidden="true">&times;</span>
                           <span class="sr-only">Close</span>
                       </button>
                       <strong><i class="fa fa-lock" style="padding-right:13px;"></i>Error!</strong> <?=$error?>
                   </div>
                   <?php endif; ?>
                   <div class="form-group">
                       <label for="reportType">Report type:</label>
                       <select name="reportType" class="form-control" required>
                           <option>Proforma Report</option>
                           <option>Tax Invoice Report</option>
                           <option>Supplier Report</option>
                       </select>
                   </div>
                   <div class="form-group">
                       <label for="from">From Date:</label>
                       <input type="date" id="from" class="form-control" name="from" required/>
                   </div>
                   <div class="form-group">
                       <label for="to">To Date:</label>
                       <input type="date" id="to" class="form-control" name="to" required/>
                   </div>
                   <div class="form-group">
                       <label for="customer">Customer:</label>
                       <select class="form-control" name="customer">
                           <option selected>All</option>
                           <?php foreach ($customers as $c): ?>
                           <option value="<?=$c->id?>"><?=$c->customerName?></option>
                           <?php endforeach; ?>
                       </select>
                   </div>
                   <input type="submit" class="btn btn-outline-success" value="Generate"/>
               </form>
            </div>
        </div>
    </div>
    <script>

    </script>
</div>
<?php $this->endsection();?>
