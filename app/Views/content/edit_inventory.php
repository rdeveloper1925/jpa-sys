<?php $this->extend('layouts/app') ?>
<?php $this->section('content')?>
    <div class="row">
        <div class="col-12">
            <a href="<?=base_url('inventory/delete_inventory/'.$item->id)?>" class="btn btn-md btn-danger">Delete Inventory Item</a>
        </div>
        <div class="col-md-8 container">
            <form action="<?=base_url('inventory/update/'.$item->id)?>" method="post">
                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="">Date In:</label>
                        <input type="text" class="form-control " value="<?=date('d-M-Y',strtotime($item->dateIn))?>" required />
                    </div>
                    <div class="form-group col-md-6">
                        <label for="">Part Name:</label>
                        <input type="text" class="form-control " value="<?=$item->partName?>" required />
                    </div>
                    <div class="form-group col-md-6">
                        <label for="">Part No:</label>
                        <input type="text" class="form-control " value="<?=$item->partNo?>" required />
                    </div>
                    <div class="form-group col-md-6">
                        <label for="">Quantity In Store:</label>
                        <input type="number" step=0.01 class="form-control " value="<?=$item->quantityInStore?>" name="quantityInStore" required />
                    </div>
                    <div class="form-group col-md-6">
                        <label for="">Balance In Store:</label>
                        <input type="number" step=0.01 class="form-control " name="balanceInStore" value="<?=$item->balanceInStore?>" required />
                    </div>
                    <div class="form-group col-md-6">
                        <label for="">Supplied By:</label>
                        <input type="text" class="form-control " value="<?=$item->suppliedBy?>" required />
                    </div>
                    <div class="col-md-12">
                        <input type="submit" value="Save" class="btn btn-success btn-md"/>
                    </div>
                </div>
            </form>
        </div>
    </div>
<?php $this->endsection(); ?>
