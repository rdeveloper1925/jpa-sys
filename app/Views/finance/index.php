<?php $this->extend('layouts/app') ?>
<?php $this->section('content'); ?>
<div class="row">
    <div class="col-md-12 col-sm-12">
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-create"><i class="fa fa-plus" aria-hidden="true"></i>
            Create New Entry
        </button>
    </div>
    <?php if(\Config\Services::session()->has('success')): ?>
        <div class="alert alert-success alert-dismissible fade show col-12" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <strong>Success! </strong> <?=\Config\Services::session()->get('success')?>
        </div>
    <?php endif; ?>
    <div class="col-12 mt-3">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Issued Proforma Invoices</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm" id="datatable" width="100%" cellspacing="0">
                        <thead>
                        <tr>
                            <th>Customer Name</th>
                            <th>Proforma No</th>
                            <th>Tax Invoice No</th>
                            <th>LPO No</th>
                            <th>Confirmed</th>
                            <th>Total Payable</th>
                            <th>Cleared</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th>Customer Name</th>
                            <th>Proforma No</th>
                            <th>Tax Invoice No</th>
                            <th>LPO No</th>
                            <th>Confirmed</th>
                            <th>Total Payable</th>
                            <th>Cleared</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                        </tfoot>
                        <tbody>
                        <?php if(!empty($finances)): ?>
                        <?php foreach ($finances as $f) : ?>
                            <tr>
                                <td><?=$f->customerName?></td>
                                <td><?=$f->proformaNo?></td>
                                <td><?=$f->taxInvoiceNo?></td>
                                <td><?=$f->lpoNo?></td>
                                <td><?=$f->confirmed? "CONFIRMED":"NOT CONFIRMED"?></td>
                                <td><?=$f->totalPayable?></td>
                                <td><?=$f->cleared?"CLEARED":"NOT CLEARED"?></td>
                                <td><?=date('d-M-y',strtotime($f->date))?></td>
                                <td>
                                    <button onclick="fetchEntry()" type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-view"><i class="fa fa-eye" aria-hidden="true"></i>View </button>
                                    <a href="#" onclick="del(<?=$f->id?>)" class="btn btn-sm btn-danger"><i class="fa fa-trash" aria-hidden="true"></i>Delete </a>
                                    <a href="<?=base_url('proforma/invoice_items_edit/'.$f->id)?>" class="btn btn-warning">Edit items</a>
                                    <a href="<?=base_url('proforma/custdetails_edit/'.$f->id)?>" class="btn btn-warning">Edit details</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function del(id) {
        if(confirm("Are you sure you want to delete this invoice?")){
            window.location.href="<?=base_url('proforma/delete_invoice')?>/"+id;
        }
    }
</script>

<!-- Button trigger modal -->


<!-- Modal -->
<div class="modal fade" id="modal-create" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg " role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Create New Financial entry</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="<?=base_url('finance/save')?>" method="post">
                    <div class="row">
                        <div class="col-4 form-group">
                            <label>Customer</label>
                            <select id="customerSelect" onchange="fetchCustomers()" class="form-control">
                                <?php foreach($customers as $c): ?>
                                <option value="<?=$c->id?>"><?=$c->customerName?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-4 form-group">
                            <label for="">Customer Name.</label>
                            <input type="text" class="form-control" id="customerName" name="customerName" />
                            <input type="hidden" id="customerId" name="customerId"/>
                        </div>
                        <div class="form-group col-4">
                            <label for="">Contact Person: </label>
                            <input type="text" class="form-control" id="contactPerson" name="contactPerson" required/>
                        </div>
                        <div class="form-group col-4">
                            <label for="">Address: </label>
                            <input type="text" class="form-control" id="address" name="address" required/>
                        </div>
                        <div class="form-group col-4">
                            <label for="">Area Country: </label>
                            <input type="text" class="form-control" id="areaCountry"  name="areaCountry" required/>
                        </div>
                        <div class="form-group col-4">
                            <label for="">Phone: </label>
                            <input type="text" class="form-control" id="phone"  name="phone" required/>
                        </div>
                        <div class="form-group col-4">
                            <label for="">Email: </label>
                            <input type="text" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="form-group col-4">
                            <label for="">Customer Tin No: </label>
                            <input type="tel" pattern='[0-9]*' class="form-control" id="tinNo"  name="tinNo" required>
                        </div>
                        <div class="col-4 form-group">
                            <label for="">Proforma No.</label>
                            <input type="text" class="form-control" name="proformaNo" />
                        </div>
                        <div class="col-4 form-group">
                            <label for="">Tax Invoice No.</label>
                            <input type="text" class="form-control" name="taxInvoiceNo" />
                        </div>
                        <div class="col-4 form-group">
                            <label for="">lpo No.</label>
                            <input type="text" class="form-control" name="lpoNo" />
                        </div>
                        <div class="col-4 form-group">
                            <label for="">Proforma No.</label>
                            <input type="date" class="form-control" name="date" required/>
                        </div>
                        <div class="col-4 form-group">
                            <label for="">Order Confirmed:</label>
                            <br><input type="radio" name="confirmed" value="1"/> Confirmed<br>
                            <input type="radio" name="confirmed" value="0"/> Not Confirmed
                        </div>
                        <div class="col-4 form-group">
                            <label for="">Witholding tax:</label>
                            <input type="number" class="form-control" name="withholdingTax" required/>
                        </div>
                        <div class="col-4 form-group">
                            <label for="">VAT 18%:</label>
                            <input type="number" class="form-control" name="vat" required/>
                        </div>
                        <div class="col-4 form-group">
                            <label for="">Total Payable:</label>
                            <input type="number" class="form-control" name="totalPayable" required/>
                        </div>
                        <div class="col-4 form-group">
                            <label for="">Cleared:</label>
                            <br><input type="radio" name="cleared" value="1"/> Cleared<br>
                            <input type="radio" name="cleared" value="0"/> Not Cleared
                        </div>
                    </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save changes</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-view" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg " role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">View New Financial entry</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="#" method="post">
                    <div class="row">
                        <div class="col-4 form-group">
                            <label>Customer</label>
                        </div>
                        <div class="col-4 form-group">
                            <label for="">Customer Name.</label>
                            <input type="text" class="form-control" id="customerName2" disabled name="customerName" />
                            <input type="hidden" id="customerId2" name="customerId"/>
                        </div>
                        <div class="form-group col-4">
                            <label for="">Contact Person: </label>
                            <input type="text" class="form-control" id="contactPerson2" disabled name="contactPerson" required/>
                        </div>
                        <div class="form-group col-4">
                            <label for="">Address: </label>
                            <input type="text" class="form-control" id="address2" disabled name="address" required/>
                        </div>
                        <div class="form-group col-4">
                            <label for="">Area Country: </label>
                            <input type="text" class="form-control" id="areaCountry2" disabled  name="areaCountry" required/>
                        </div>
                        <div class="form-group col-4">
                            <label for="">Phone: </label>
                            <input type="text" class="form-control" id="phone2" disabled  name="phone" required/>
                        </div>
                        <div class="form-group col-4">
                            <label for="">Email: </label>
                            <input type="text" class="form-control" id="email2" disabled name="email" required>
                        </div>
                        <div class="form-group col-4">
                            <label for="">Customer Tin No: </label>
                            <input type="tel" pattern='[0-9]*' class="form-control" disabled id="tinNo2"  name="tinNo" required>
                        </div>
                        <div class="col-4 form-group">
                            <label for="">Proforma No.</label>
                            <input type="text" class="form-control" id="proformaNo2" disabled name="proformaNo" />
                        </div>
                        <div class="col-4 form-group">
                            <label for="">Tax Invoice No.</label>
                            <input type="text" class="form-control" id="taxInvoiceNo2" disabled name="taxInvoiceNo" />
                        </div>
                        <div class="col-4 form-group">
                            <label for="">lpo No.</label>
                            <input type="text" class="form-control" name="lpoNo2" disabled name="lpoNo" />
                        </div>
                        <div class="col-4 form-group">
                            <label for="">Proforma No.</label>
                            <input type="date" class="form-control" id="date2" disabled name="date" />
                        </div>
                        <div class="col-4 form-group">
                            <label for="">Order Confirmed:</label>
                            <input type="text" disabled class="form-control" id="confirmed"/>
                        </div>
                        <div class="col-4 form-group">
                            <label for="">Witholding tax:</label>
                            <input type="number" class="form-control" disabled id="withholdingTax2" name="withholdingTax" disabled/>
                        </div>
                        <div class="col-4 form-group">
                            <label for="">VAT 18%:</label>
                            <input type="number" class="form-control" id="vat2" name="vat2"  disabled/>
                        </div>
                        <div class="col-4 form-group">
                            <label for="">Total Payable:</label>
                            <input type="number" class="form-control" id="totalPayable2" disabled/>
                        </div>
                        <div class="col-4 form-group">
                            <label for="">Cleared:</label>
                            <input type="text" id="cleared2" class="form-control" disabled/>
                        </div>
                    </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </form>
            </div>
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
                    $("#customerName").val(data.customer.customerName);
                    $("#address").val(data.customer.address);
                    $("#areaCountry").val(data.customer.areaCountry);
                    $("#phone").val(data.customer.phone);
                    $("#email").val(data.customer.email);
                    $("#tinNo").val(data.customer.tinNo);
                    $("#customerId").val(data.customer.id);
                    //$("#customerIdd").val(data.customer.id);
                }
            },
            "JSON"
        );
    }
    function fetchEntry(id){
        $.post("<?=base_url('finance/fetchEntry')?>", {id:id},
            function (data, textStatus, jqXHR) {
                console.log(data.entry);
                if(data.success){
                    $("#contactPerson2").val(data.entry.contactPerson);
                    $("#customerName2").val(data.entry.customerName);
                    $("#address2").val(data.entry.address);
                    $("#areaCountry2").val(data.entry.areaCountry);
                    $("#phone2").val(data.entry.phone);
                    $("#email2").val(data.entry.email);
                    $("#tinNo2").val(data.entry.tinNo);
                    $("#customerId2").val(data.entry.customerId);
                    //$("#customerIdd").val(data.customer.id);
                }
            },
            "JSON"
        );
    }
</script>
<?php $this->endsection(); ?>
