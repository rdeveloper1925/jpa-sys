<?php $this->extend('layouts/app'); ?>
<?php $this->section ('content') ?>
<div class="row">
    <div class="col-md-12 container">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Completion certificate info</h6>
            </div>
            <div class="card-body">
                <form action="#" method="post">
                    <div class="row">
                        <div class="col-12">
                            <h5>Customer Information</h5>
                            <hr style="height: 5px;margin: 1px;color: black"/>
                        </div>
                        <div class="form-group col-4">
                            <label for="">Customer Name: </label>
                            <input type="text" class="form-control"  disabled value="<?=$certificate->customerName?>" name="customerName" id="customerName"  />
                        </div>
                        <div class="form-group col-4">
                            <label for="">Contact Person: </label>
                            <input type="text" class="form-control"  disabled value="<?=$certificate->contactPerson?>" name="contactPerson" id="contactPerson"  />
                        </div>
                        <div class="form-group col-4">
                            <label for="">Address: </label>
                            <input type="text" class="form-control"  disabled value="<?=$certificate->address?>" name="address" id="address"  />
                        </div>
                        <div class="form-group col-4">
                            <label for="">Area Country: </label>
                            <input type="text" class="form-control"  disabled value="<?=$certificate->country?>" name="country" id="areaCountry"  />
                        </div>
                        <div class="form-group col-4">
                            <label for="">Phone: </label>
                            <input type="text" class="form-control"  disabled value="<?=$certificate->phone?>" name="phone" id="phone"  />
                        </div>
                        <div class="form-group col-4">
                            <label for="">Email: </label>
                            <input type="text" class="form-control"  disabled value="<?=$certificate->email?>" name="email" id="email"  >
                        </div>
                        <div class="form-group col-4">
                            <label for="">Customer Tin No: </label>
                            <input type="tel" pattern='[0-9]*'  disabled value="<?=$certificate->tinNo?>" name="tinNo" class="form-control" id="tinNo"  >
                        </div>
                        <!-- this is the breaking point -->
                        <div class="col-12">
                            <h5>Certificate Details</h5>
                            <hr style="height: 9px;margin: 1px;color: red"/>
                        </div>
                        <div class="form-group col-4">
                            <label for="">Proforma/Tax Invoice no.</label>
                            <input type="text" class="form-control" disabled value="<?=$certificate->invoiceNo?>" name="invoiceNo" />
                        </div>
                        <hr>
                        <div class="form-group col-4">
                            <label for="">Date of Completion: </label>
                            <input type="text" class="form-control"  disabled value="<?=date('d-m-Y',strtotime($certificate->dateCompleted))?>" name="dateCompleted"  >
                        </div>
                        <div class="form-group col-4">
                            <label for="">Car Chasis No: </label>
                            <input type="text" class="form-control"  disabled value="<?=$certificate->carChasisNo?>" name="carChasisNo"  >
                        </div>
                        <div class="form-group col-4">
                            <label for="">Car Registration No: </label>
                            <input type="text" class="form-control"  disabled value="<?=$certificate->carRegNo?>" name="carRegNo"  >
                        </div>
                        <div class="form-group col-4">
                            <label for="">Car Type: </label>
                            <input type="text" class="form-control"  disabled value="<?=$certificate->carType?>" name="carType"  >
                        </div>
                        <div class="form-group col-4">
                            <label for="">Mileage: </label>
                            <input type="number" class="form-control"  disabled value="<?=$certificate->mileage?>" name="mileage"  >
                        </div>
                        <!-- this is the breaking point -->
                        <div class="col-12">
                            <br>
                            <h5>Engineer & Driver Details</h5>
                            <hr style="height: 9px;margin: 1px;color: red"/>
                        </div>
                        <div class="form-group col-4">
                            <label for="">Workshop Engineer: </label>
                            <input type="text" class="form-control"  disabled value="<?=$certificate->engineerName?>" name="engineerName"  >
                        </div>
                        <div class="form-group col-4">
                            <label for="">Transport Officer: </label>
                            <input type="text" class="form-control"  disabled value="<?=$certificate->transportOfficer?>" name="transportOfficer"  >
                        </div>
                        <div class="form-group col-4">
                            <label for="">Driver Name: </label>
                            <input type="text" class="form-control"  disabled value="<?=$certificate->driverName?>" name="driverName"  >
                        </div>
                        <div class="col-12">
                            <br>
                            <h5>Repairs Done</h5>
                            <hr style="height: 9px;margin: 1px;color: red"/>
                        </div>
                        <div class="form-group col-6">
                            <label for="">Description of Repairs</label>
                            <textarea class="form-control"  disabled value="<?=$certificate->repairsDone?>" name="repairsDone" ><?=$certificate->repairsDone?></textarea>
                        </div>
                        <div class="form-group col-6">
                            <label for="">Comments</label>
                            <textarea class="form-control"  disabled value="<?=$certificate->comments?>" name="comments"  ><?=$certificate->comments?></textarea>
                        </div>
                        <div class="col-12">
                            <a href="<?=base_url('certificates/print/'.$certificate->id)?>>" class="btn btn-success">View Certificate <i class="fa fa-forward" aria-hidden="true"></i></a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $this->endsection();?>
