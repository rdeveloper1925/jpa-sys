<?php $this->extend('layouts/app'); ?>
<?php $this->section ('content') ?>
<div class="row">
    <div class="col-md-12 container">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Invoice Info</h6>
            </div>
            <div class="card-body">
                <form action="<?=base_url('invoices/save_edits')?>" method="post">
                    <div class="row">
                        <div class="col-12">
                            <h5>Customer Information</h5>
                            <hr style="height: 5px;margin: 1px;color: black"/>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="">Customer Name:</label>
                            <input type="text" class="form-control" name="customerName" value="<?=$invoice->customerName?>" />
                            <input type="hidden" name="custId" value="<?=$invoice->customerId?>" />
                            <input type="hidden" name="customerId2" id="customerIdd"/>
                        </div>
                        <div class="form-group col-4">
                            <label for="">Contact Person: </label>
                            <input type="text" class="form-control" name="contactPerson" value="<?=$invoice->contactPerson?>" required/>
                        </div>
                        <div class="form-group col-4">
                            <label for="">Address: </label>
                            <input type="text" value="<?=$invoice->address?>" class="form-control" name="address" required/>
                        </div>
                        <div class="form-group col-4">
                            <label for="">Area Country: </label>
                            <input type="text" class="form-control" value="<?=$invoice->areaCountry?>" name="areaCountry" required/>
                        </div>
                        <div class="form-group col-4">
                            <label for="">Phone: </label>
                            <input type="text" class="form-control" value="<?=$invoice->phone?>" name="phone" required/>
                        </div>
                        <div class="form-group col-4">
                            <label for="">Email: </label>
                            <input type="text" class="form-control" value="<?=$invoice->email?>" name="email" required>
                        </div>
                        <div class="form-group col-4">
                            <label for="">Customer Tin No: </label>
                            <input type="tel" pattern='[0-9]*' class="form-control" value="<?=$invoice->tinNo?>" name="tinNo" required>
                        </div>
                        <!-- this is the breaking point -->
                        <div class="col-12">
                            <br>
                            <br>
                            <h5>Invoice Details</h5>
                            <hr style="height: 9px;margin: 1px;color: red"/>
                        </div>


                        <div class="form-group col-4">
                            <label for="">Invoice No.</label>
                            <input type="text" class="form-control" value="<?=$invoice_no?>" disabled >
                        </div>
                        <hr>
                        <div class="form-group col-4">
                            <label for="">Date: </label>
                            <input type="date" class="form-control" name="date" value="<?=date('Y-m-d',strtotime($invoice->date))?>" required>
                        </div>
                        <div class="form-group col-4">
                            <label for="">Proforma Number:</label>
                            <select name="proformaId" class="form-control">
                                <option value="<?=$invoice->proformaId?>" selected><?=$invoice->proformaId?></option>
                                <?php foreach ($proformae as $s): ?>
                                    <option value="<?=$s->invoiceId?>"><?=$s->customerName.' = '.$s->invoiceId?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group col-4">
                            <label for="">Proforma number: </label>
                            <input type="number" class="form-control" name="proformaId" value="<?=$invoice->proformaId?>" />
                        </div>
                        <div class="form-group col-4">
                            <label for="">Currency: </label>
                            <input type="text" class="form-control" name="currency" value="<?=$invoice->currency?>" required>
                        </div>

                        <div class="form-group col-4">
                            <label for="">Mode of Payment: </label>
                            <input type="text" class="form-control" name="modeOfPayment" value="<?=$invoice->modeOfPayment?>" required>
                        </div>
                        <div class="form-group col-4">
                            <label for="">LPO Number: </label>
                            <input type="text" pattern="[0-9]*" class="form-control" name="lpo" value="<?=$invoice->lpoNo?>" required>
                        </div>
                        <div class="form-group col-4">
                            <label for="">Car Registration No: </label>
                            <input type="text" class="form-control" name="carRegNo" value="<?=$invoice->carRegNo?>" required>
                        </div>
                        <div class="form-group col-4">
                            <label for="">Car Type: </label>
                            <input type="text" class="form-control" name="carType" value="<?=$invoice->carType?>" required>
                            <input type="hidden" name="proformaId" value="<?=$invoice->proformaId?>"/>
                            <input type="hidden" name="invoiceId" value="<?=$invoice_no?>"/>
                        </div>
                        <div class="form-group col-4">
                            <label for="">Mileage: </label>
                            <input type="number" pattern="[0-9]*" class="form-control" name="mileage" value="<?=$invoice->mileage?>" required>
                        </div>
                        <div class="form-group col-4">
                            <label for="narration">Narration: </label>
                            <input list="narrations" name="narration" value="<?=$invoice->narration?>" id="narration" class="form-control"/>
                            <datalist id="narrations">
                                <option>Service of Vehicle</option>
                                <option>Repair of Vehicle</option>
                                <option>Service + Repair</option>
                                <option>Supply of Parts</option>
                            </datalist>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-success">Save Edits <i class="fa fa-forward" aria-hidden="true"></i></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        function fetchCustomers() {
            var data={id:$('#customerSelect').val()};
            $.post("<?=base_url('invoices/fetch_customer')?>", data,
                function (data, textStatus, jqXHR) {
                    if(data.success){
                        $("#contactPerson").val(data.customer.contactPerson);
                        $("#address").val(data.customer.address);
                        $("#areaCountry").val(data.customer.areaCountry);
                        $("#phone").val(data.customer.phone);
                        $("#email").val(data.customer.email);
                        $("#tinNo").val(data.customer.tinNo);
                        $("#otherContactDetails").val(data.customer.otherContactDetails);
                        $("#customerIdd").val(data.customer.id);
                    }
                },
                "JSON"
            );
        }
    </script>
</div>
<?php $this->endsection();?>
