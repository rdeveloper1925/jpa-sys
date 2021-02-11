<?php $this->extend('layouts/app') ?>
<?php $this->section('content'); ?>
<div class="row">
    <?php if(\Config\Services::session()->has('fail')): ?>
        <div class="alert alert-danger alert-dismissible fade show col-12" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <strong>Error! </strong> <?=\Config\Services::session()->get('fail')?>
        </div>
    <?php endif; ?>
    <div class="col-6 justify-content-center">
        <form method="post" action="<?=base_url('finance/view_report')?>">
            <div class="form-group">
                <label>Customer:</label>
                <select class="form-control select2-dropdown" name="customer">
                    <option selected value="*">All</option>
                    <?php foreach($customers as $c): ?>
                    <option value="<?=$c->id?>"><?=$c->customerName?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>From Date:</label>
                <input type="date" class="form-control" name="from" required/>
            </div>
            <div class="form-group">
                <label>To Date:</label>
                <input type="date" class="form-control" name="to" required/>
            </div><!--
            <div class="form-group">
                <label>Report type:</label>
                <select class="form-control select2-dropdown" name="reportType">
                    <option selected value="*">All</option>
                    <option value="confirmed">Confirmed Jobs</option>
                    <option value="unconfirmed">UnConfirmed Jobs</option>
                    <option value="cleared">Cleared Balances</option>
                    <option value="uncleared">Uncleared Balances</option>
                    <option value="confirmedUncleared">Confirmed but Balance Not yet cleared</option>
                    <option value="confirmedCleared">Confirmed and Balance Cleared </option>
                </select>
            </div>-->
            <input type="submit" class="btn btn-success" value="View Report">
        </form>
    </div>
</div>
<?php $this->endsection(); ?>