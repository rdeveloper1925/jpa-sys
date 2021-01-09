<?php $this->extend('layouts/app'); ?>
<?php $this->section ('content') ?>
<div class="row">
    <div class="col-md-12 container">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Completion certificate info</h6>
            </div>
            <div class="card-body">
                <form action="<?=base_url('certificates/save')?>" method="post">
                    <div class="row">
                        <div class="col-12">
                            <h5>Customer Information</h5>
                            <hr style="height: 5px;margin: 1px;color: black"/>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="">Find Customer:</label>
                            <select class="form-control" name="customerId" id="customerSelect" onchange="fetchCustomers()">
                                <?php foreach($customers as $c): ?>
                                    <option value="<?=$c->id?>"><?=$c->customerName?></option>
                                <?php endforeach; ?>
                            </select>
                            <input type="hidden" name="customerId2" id="customerIdd"/>
                        </div>
                        <div class="form-group col-4">
                            <label for="">Customer Name: </label>
                            <input type="text" class="form-control" name="customerName" id="customerName" required/>
                        </div>
                        <div class="form-group col-4">
                            <label for="">Contact Person: </label>
                            <input type="text" class="form-control" name="contactPerson" id="contactPerson" required/>
                        </div>
                        <div class="form-group col-4">
                            <label for="">Address: </label>
                            <input type="text" class="form-control" name="address" id="address" required/>
                        </div>
                        <div class="form-group col-4">
                            <label for="">Area Country: </label>
                            <input type="text" class="form-control" name="areaCountry" id="areaCountry" required/>
                        </div>
                        <div class="form-group col-4">
                            <label for="">Phone: </label>
                            <input type="text" class="form-control" name="phone" id="phone" required/>
                        </div>
                        <div class="form-group col-4">
                            <label for="">Email: </label>
                            <input type="text" class="form-control" name="email" id="email" required>
                        </div>
                        <div class="form-group col-4">
                            <label for="">Customer Tin No: </label>
                            <input type="tel" pattern='[0-9]*' name="tinNo" class="form-control" id="tinNo" required>
                        </div>
                        <!-- this is the breaking point -->
                        <div class="col-12">
                            <h5>Certificate Details</h5>
                            <hr style="height: 9px;margin: 1px;color: red"/>
                        </div>
                        <div class="form-group col-4">
                            <label for="">Proforma/Tax Invoice no.</label>
                            <input type="text" class="form-control"name="invoiceNo" value="" required >
                        </div>
                        <hr>
                        <div class="form-group col-4">
                            <label for="">Date of Completion: </label>
                            <input type="date" class="form-control" name="dateCompleted" required>
                        </div>
                        <div class="form-group col-4">
                            <label for="">Car Chasis No: </label>
                            <input type="text" class="form-control" name="carChasisNo" required>
                        </div>
                        <div class="form-group col-4">
                            <label for="">Car Registration No: </label>
                            <input type="text" class="form-control" name="carRegNo" required>
                        </div>
                        <div class="form-group col-4">
                            <label for="">Car Type: </label>
                            <input type="text" class="form-control" name="carType" required>
                        </div>
                        <div class="form-group col-4">
                            <label for="">Mileage: </label>
                            <input type="number" class="form-control" name="mileage" required>
                        </div>
                        <!-- this is the breaking point -->
                        <div class="col-12">
                            <br>
                            <h5>Engineer & Driver Details</h5>
                            <hr style="height: 9px;margin: 1px;color: red"/>
                        </div>
                        <div class="form-group col-4">
                            <label for="">Workshop Engineer: </label>
                            <input type="text" class="form-control" name="engineerName" required>
                        </div>
                        <div class="form-group col-4">
                            <label for="">Transport Officer: </label>
                            <input type="text" class="form-control" name="transportOfficer" required>
                        </div>
                        <div class="form-group col-4">
                            <label for="">Driver Name: </label>
                            <input type="text" class="form-control" name="driverName" required>
                        </div>
                        <div class="col-12">
                            <br>
                            <h5>Repairs Done</h5>
                            <hr style="height: 9px;margin: 1px;color: red"/>
                        </div>
                        <div class="form-group col-6">
                            <label for="">Description of Repairs</label>
                            <textarea class="form-control" name="repairsDone" required></textarea>
                        </div>
                        <div class="form-group col-6">
                            <label for="">Comments</label>
                            <textarea class="form-control" name="comments" required></textarea>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-success">Proceed <i class="fa fa-forward" aria-hidden="true"></i></button>
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
                        $("#customerName").val(data.customer.customerName);
                    }
                },
                "JSON"
            );
        }
    </script>
</div>
<?php $this->endsection();?>
