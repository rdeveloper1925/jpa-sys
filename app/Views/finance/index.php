<?php $this->extend('layouts/app') ?>
<?php $this->section('content'); ?>
<div class="row">
    <div class="col-md-4 col-sm-4">
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-create"><i class="fa fa-plus" aria-hidden="true"></i>
            Create New Entry
        </button>
    </div>
    <div class="col-4 float-right">
        <a href="<?=base_url('finance/report_select')?>" class="btn btn-success" >
            <i class="fa fa-clipboard-list"></i> View Financial Reports
        </a>
    </div>
    <div class="col-4 float-right">
        <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#exampleModal123">
            <i class="fa fa-random"></i> Match Proforma to Tax invoice
        </button>
    </div>
    <!-- Button trigger modal -->


    <!-- Modal -->
    <div class="modal fade" id="exampleModal123" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="post" action="<?=base_url('finance/view_match')?>">
                        <div class="form-group">
                            <label>Proforma Invoice Number</label>
                            <input type="number" name="proformaId" class="form-control" required/>
                        </div>
                        <div class="form-group">
                            <label>Tax Invoice Number</label>
                            <input type="number" class="form-control" name="invoiceId" required/>
                        </div>
                        <input type="submit" class="btn btn-success" value="View matching"/>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php if(\Config\Services::session()->has('success')): ?>
        <div class="alert alert-success alert-dismissible fade show col-12" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <strong>Success! </strong> <?=\Config\Services::session()->get('success')?>
        </div>
    <?php endif; ?>
    <?php if(\Config\Services::session()->has('fail')): ?>
        <div class="alert alert-danger alert-dismissible fade show col-12" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <strong>Error! </strong> <?=\Config\Services::session()->get('fail')?>
        </div>
    <?php endif; ?>
    <div id="alerter" class="alert alert-success alert-dismissible fade show" style="display: none">
        <strong>Success!</strong> The accounting entry has been deleted successfull.
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
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
                                    <button onclick="fetchEntry(<?=$f->id?>)" type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#modal-view"><i class="fa fa-eye" aria-hidden="true"></i>View </button>
                                    <button onclick="fetchEntry_edit(<?=$f->id?>)" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#modal-edit"><i class="fa fa-pen-nib" aria-hidden="true"></i>Edit </button>
                                    <a href="#" onclick="del(<?=$f->id?>)" class="btn btn-sm btn-danger"><i class="fa fa-trash" aria-hidden="true"></i>Delete </a>
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
                            <label for="">Car Registration No:</label>
                            <input type="text" class="form-control" name="carRegNo" required/>
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

<!--View finanacial model -->
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
                            <input type="text" class="form-control" id="lpoNo2" disabled name="lpoNo" />
                        </div>
                        <div class="col-4 form-group">
                            <label for="">Date.</label>
                            <input type="text" class="form-control" id="date2" disabled name="date" />
                        </div>
                        <div class="col-4 form-group">
                            <label for="">Order Confirmed:</label>
                            <input type="text" disabled class="form-control" id="confirmed2"/>
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
                            <label for="">Car Registration No:</label>
                            <input type="text" class="form-control" id="carRegNo2" disabled/>
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

<!-- Edit financial model -->
<div class="modal fade" id="modal-edit" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg " role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Edit Financial entry</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="<?=base_url('finance/update')?>" method="post">
                    <div class="row">
                        <div class="col-4 form-group">
                            <label for="">Customer Name.</label>
                            <input type="text" class="form-control" id="customerName3" name="customerName" />
                            <input type="hidden" id="customerId3" name="customerId"/>
                            <input type="hidden" id="entryId" name="entryId" />
                        </div>
                        <div class="form-group col-4">
                            <label for="">Contact Person: </label>
                            <input type="text" class="form-control" id="contactPerson3" name="contactPerson" required/>
                        </div>
                        <div class="form-group col-4">
                            <label for="">Address: </label>
                            <input type="text" class="form-control" id="address3" name="address" required/>
                        </div>
                        <div class="form-group col-4">
                            <label for="">Area Country: </label>
                            <input type="text" class="form-control" id="areaCountry3"  name="areaCountry" required/>
                        </div>
                        <div class="form-group col-4">
                            <label for="">Phone: </label>
                            <input type="text" class="form-control" id="phone3"  name="phone" required/>
                        </div>
                        <div class="form-group col-4">
                            <label for="">Email: </label>
                            <input type="text" class="form-control" id="email3" name="email" required>
                        </div>
                        <div class="form-group col-4">
                            <label for="">Customer Tin No: </label>
                            <input type="tel" pattern='[0-9]*' class="form-control" id="tinNo3"  name="tinNo" required>
                        </div>
                        <div class="col-4 form-group">
                            <label for="">Proforma No.</label>
                            <input type="text" class="form-control" id="proformaNo3" name="proformaNo" />
                        </div>
                        <div class="col-4 form-group">
                            <label for="">Tax Invoice No.</label>
                            <input type="text" class="form-control" id="taxInvoiceNo3" name="taxInvoiceNo" />
                        </div>
                        <div class="col-4 form-group">
                            <label for="">lpo No.</label>
                            <input type="text" class="form-control" id="lpoNo3" name="lpoNo" />
                        </div>
                        <div class="col-4 form-group">
                            <label for="">Date.</label>
                            <input type="text" class="form-control" id="date3"  required/>
                        </div>
                        <div class="col-4 form-group">
                            <label for="">Order Confirmed:</label>
                            <br><input type="radio" name="confirmed" id="confirmed11" value="1"/> Confirmed<br>
                            <input type="radio" name="confirmed" id='confirmed00' value="0"/> Not Confirmed
                        </div>
                        <div class="col-4 form-group">
                            <label for="">Witholding tax:</label>
                            <input type="number" class="form-control" id="withholdingTax3" name="withholdingTax" required/>
                        </div>
                        <div class="col-4 form-group">
                            <label for="">VAT 18%:</label>
                            <input type="number" class="form-control" id="vat3" name="vat" required/>
                        </div>
                        <div class="col-4 form-group">
                            <label for="">Total Payable:</label>
                            <input type="number" class="form-control" id='totalPayable3' name="totalPayable" required/>
                        </div>
                        <div class="col-4 form-group">
                            <label for="">Car Registration No:</label>
                            <input type="text" class="form-control" id="carRegNo3" name="carRegNo" required/>
                        </div>
                        <div class="col-4 form-group">
                            <label for="">Cleared:</label>
                            <br><input type="radio" id="cleared11" name="cleared" value="1"/> Cleared<br>
                            <input type="radio" id="cleared00" name="cleared" value="0"/> Not Cleared
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

<!-- Report Selector modal -->
<div class="modal fade" id="reportSelector" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Select Report</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" action="<?=base_url('finance/view_report')?>">
                    <div class="form-group">
                        <label>Report type:</label>
                        <select class="form-control select2-dropdown" name="reportType">
                            <option value="confirmed">Confirmed Jobs</option>
                            <option value="unconfirmed">UnConfirmed Jobs</option>
                            <option value="cleared">Cleared Balances</option>
                            <option value="uncleared">Uncleared Balances</option>
                            <option value="confirmedUncleared">Confirmed but Balance Not yet cleared</option>
                            <option value="confirmedCleared">Confirmed and Balance Cleared </option>
                        </select>
                    </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Save changes</button>
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
                    $("#proformaNo2").val(data.entry.proformaNo);
                    $("#taxInvoiceNo2").val(data.entry.taxInvoiceNo);
                    $("#date2").val(data.entry.date);
                    $("#totalPayable2").val(data.entry.totalPayable);
                    $("#carRegNo2").val(data.entry.carRegNo);
                    $("#vat2").val(data.entry.vat);
                    $("#withholdingTax2").val(data.entry.withholdingTax);
                    $("#lpoNo2").val(data.entry.lpoNo);
                    if(data.entry.cleared==1){
                        $("#cleared2").val("Cleared");
                    }else{
                        $("#cleared2").val("Not Cleared");
                    }
                    if(data.entry.confirmed==1){
                        $("#confirmed2").val("Confirmed");
                    }else{
                        $("#confirmed2").val("Not Confirmed");
                    }
                    //$("#customerIdd").val(data.customer.id);
                }
            },
            "JSON"
        );
    }
    function fetchEntry_edit(id){
        $.post("<?=base_url('finance/fetchEntry')?>", {id:id},
            function (data, textStatus, jqXHR) {
                console.log(data.entry);
                if(data.success){
                    $("#contactPerson3").val(data.entry.contactPerson);
                    $("#customerName3").val(data.entry.customerName);
                    $("#address3").val(data.entry.address);
                    $("#areaCountry3").val(data.entry.areaCountry);
                    $("#phone3").val(data.entry.phone);
                    $("#email3").val(data.entry.email);
                    $("#tinNo3").val(data.entry.tinNo);
                    $("#customerId3").val(data.entry.customerId);
                    $("#proformaNo3").val(data.entry.proformaNo);
                    $("#taxInvoiceNo3").val(data.entry.taxInvoiceNo);
                    $("#totalPayable3").val(data.entry.totalPayable);
                    $("#date3").val(data.entry.date);
                    $("#vat3").val(data.entry.vat);
                    $("#entryId").val(data.entry.id);
                    $("#carRegNo3").val(data.entry.carRegNo);
                    $("#withholdingTax3").val(data.entry.withholdingTax);
                    $("#lpoNo3").val(data.entry.lpoNo);
                    if(data.entry.cleared==1){
                        $("#cleared11").attr("checked","checked");
                    }else{
                        $("#cleared00").attr("checked","checked");
                    }
                    if(data.entry.confirmed==1){
                        $("#confirmed11").attr("checked",'checked');
                    }else{
                        $("#confirmed00").attr("checked",'checked');
                    }
                    //$("#customerIdd").val(data.customer.id);
                }
            },
            "JSON"
        );
    }
    function del(id){
        var confirmation=confirm("Are you sure you want to delete this entry?");
        if(confirmation){
            //alert('deleting');
            $.get("<?=base_url('finance/delete')?>"+"/"+id,
            function (data){
                console.log(data);
                if(data.success){
                    $("#alerter").css('display','block');
                }
            },"JSON");

        }else{
            return;
        }
    }
</script>
<?php $this->endsection(); ?>
