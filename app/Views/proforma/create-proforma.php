<?php $this->extend('layouts/app'); ?>
<?php $this->section ('content') ?>
<div class="row">
    <div class="col-md-12 container">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Proforma Info</h6>
            </div>
            <div class="card-body">
                <form action="<?=base_url('proforma/save')?>" method="post">
                    <div class="row">
                        <div class="col-12">
                            <h5>Customer Information</h5>
                            <hr style="height: 5px;margin: 1px;color: black"/>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="">Customer Name:</label>
                            <select class="form-control" name="customerId" id="customerSelect" onchange="fetchCustomers()">
                                <?php foreach($customers as $c): ?>
                                    <option value="<?=$c->id?>"><?=$c->customerName?></option>
                                <?php endforeach; ?>
                            </select>
                            <input type="hidden" name="customerId2" id="customerIdd"/>
                        </div>
                        <div class="form-group col-4">
                            <label for="">Contact Person: </label>
                            <input type="text" class="form-control" id="contactPerson" required/>
                        </div>
                        <div class="form-group col-4">
                            <label for="">Address: </label>
                            <input type="text" class="form-control" id="address" required/>
                        </div>
                        <div class="form-group col-4">
                            <label for="">Area Country: </label>
                            <input type="text" class="form-control" id="areaCountry" required/>
                        </div>
                        <div class="form-group col-4">
                            <label for="">Phone: </label>
                            <input type="text" class="form-control" id="phone" required/>
                        </div>
                        <div class="form-group col-4">
                            <label for="">Email: </label>
                            <input type="text" class="form-control" id="email" required>
                        </div>
                        <div class="form-group col-4">
                            <label for="">Customer Tin No: </label>
                            <input type="number" class="form-control" id="tinNo" required>
                        </div>
                        <!-- this is the breaking point -->
                        <div class="col-12">
                            <br>
                            <br>
                            <h5>Proforma Details</h5>
                            <hr style="height: 9px;margin: 1px;color: red"/>
                        </div>


                        <div class="form-group col-4">
                            <label for="">Proforma No.</label>
                            <input type="text" class="form-control" value="Auto Generated" disabled >
                        </div>
                        <hr>
                        <div class="form-group col-4">
                            <label for="">Date: </label>
                            <input type="date" class="form-control" name="date" required>
                        </div>
                        <div class="form-group col-4">
                            <label for="">Currency: </label>
                            <input type="text" class="form-control" name="currency" required>
                        </div>

                        <div class="form-group col-4">
                            <label for="">Mode of Payment: </label>
                            <input type="text" class="form-control" name="modeOfPayment" required>
                        </div>
                        <div class="form-group col-4">
                            <label for="">LPO Number: </label>
                            <input type="text" class="form-control" name="lpo" required>
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
                        <div class="form-group col-4">
                            <label for="narration">Narration: </label>
                            <input list="narrations" name="narration" id="narration" class="form-control"/>
                            <datalist id="narrations">
                                <option>Service of Vehicle</option>
                                <option>Repair of Vehicle</option>
                                <option>Service + Repair</option>
                                <option>Supply of Parts</option>
                            </datalist>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-success">Proceed to Proforma Items <i class="fa fa-forward" aria-hidden="true"></i></button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
    <script>
        function fetchCustomers() {
            var data={id:$('#customerSelect').val()};
            $.post("<?=base_url('proforma/fetch_customer')?>", data,
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
