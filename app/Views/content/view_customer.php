<?php $this->extend('layouts/app'); ?>
<?php $this->section('content'); ?>
<div class="row">
    <div class="col-md-8 container">
        <form action="<?=base_url('customers/update/'.$customer->id)?>" method="post">
            <div class="row">
                <div class="form-group col-md-6">
                    <label for="">Customer value:</label>
                    <input type="text" class="form-control " name="customerName" value="<?=$customer->customerName?>" required />
                </div>
                <div class="form-group col-md-6">
                    <label for="">Contact Person:</label>
                    <input type="text" class="form-control " name="contactPerson" value="<?=$customer->contactPerson?>" required />
                </div>
                <div class="form-group col-md-6">
                    <label for="">Address:</label>
                    <input type="text" class="form-control " name="address" value="<?=$customer->address?>" required />
                </div>
                <div class="form-group col-md-6">
                    <label for="">Phone:</label>
                    <input type="number" class="form-control " name="phone" value="<?=$customer->phone?>" required />
                </div>
                <div class="form-group col-md-6">
                    <label for="">Email:</label>
                    <input type="text" class="form-control " name="email" value="<?=$customer->email?>" required />
                </div>
                <div class="form-group col-md-6">
                    <label for="">Tin No:</label>
                    <input type="number" class="form-control " name="tinNo" value="<?=$customer->tinNo?>" required />
                </div>
                <div class="form-group col-md-6">
                    <label for="">Area Country:</label>
                    <input type="text" class="form-control " name="areaCountry" value="<?=$customer->areaCountry?>" required />
                </div>
                <div class="col-md-12">
                    <input type="submit" value="Save" class="btn btn-success btn-md"/>
                </div>
            </div>
        </form>
    </div>
</div>
<?php $this->endsection(); ?>
