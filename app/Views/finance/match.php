<?php $this->extend('layouts/app') ?>
<?php $this->section('content'); ?>
    <div class="row">
        <div class="col-6">
            <!-- Basic Card Example -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Proforma Invoice No: <?=$proforma->invoiceId?></h6>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <td><strong>Customer Name</strong></td>
                            <td><?=$proforma->customerName?></td>
                        </tr>
                        <tr>
                            <td><strong>Contact Person</strong></td>
                            <td><?=$proforma->contactPerson?></td>
                        </tr>
                        <tr>
                            <td><strong>Car Type</strong></td>
                            <td><?=$proforma->carType?></td>
                        </tr>
                        <tr>
                            <td><strong>Car Reg No.</strong></td>
                            <td><?=$proforma->carRegNo?></td>
                        </tr>
                        <tr>
                            <td><strong>LPO No</strong></td>
                            <td><?=$proforma->lpoNo?></td>
                        </tr>
                        <tr>
                            <td><strong>Mileage</strong></td>
                            <td><?=$proforma->mileage?></td>
                        </tr>
                        <tr>
                            <td><strong>Date</strong></td>
                            <td><?=date('Y-M-d', strtotime($proforma->date))?></td>
                        </tr>
                        <tr>
                            <td><strong>Total Proforma Items cost</strong></td>
                            <td><strong><?=number_format($proformaItemsCost,2)?></strong></td>
                        </tr>
                        <tr>
                            <td><strong>Withholding tax</strong></td>
                            <td><strong><?=number_format($withholdingTax,2)?></strong></td>
                        </tr>
                        <tr>
                            <td><strong>Vat</strong></td>
                            <td><strong><?=number_format($vat,2)?></strong></td>
                        </tr>
                        <tr>
                            <td><strong>Total Value</strong></td>
                            <td><strong><?=number_format($totalValue,2)?></strong></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-6">
            <!-- Basic Card Example -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Tax Invoice No: <?=$invoice->invoiceId?></h6>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <td><strong>Customer Name</strong></td>
                            <td><?=$invoice->customerName?></td>
                        </tr>
                        <tr>
                            <td><strong>Contact Person</strong></td>
                            <td><?=$invoice->contactPerson?></td>
                        </tr>
                        <tr>
                            <td><strong>Car Type</strong></td>
                            <td><?=$invoice->carType?></td>
                        </tr>
                        <tr>
                            <td><strong>Car Reg No.</strong></td>
                            <td><?=$invoice->carRegNo?></td>
                        </tr>
                        <tr>
                            <td><strong>LPO No</strong></td>
                            <td><?=$invoice->lpoNo?></td>
                        </tr>
                        <tr>
                            <td><strong>Mileage</strong></td>
                            <td><?=$invoice->mileage?></td>
                        </tr>
                        <tr>
                            <td><strong>Date</strong></td>
                            <td><?=date('Y-M-d', strtotime($invoice->date))?></td>
                        </tr>
                        <tr>
                            <td><strong>Total Tax Invoice Items cost</strong></td>
                            <td><strong><?=number_format($invoiceItemsCost,2)?></strong></td>
                        </tr>
                        <tr>
                            <td><strong>Withholding tax</strong></td>
                            <td><strong><?=number_format($withholdingTax2,2)?></strong></td>
                        </tr>
                        <tr>
                            <td><strong>Vat</strong></td>
                            <td><strong><?=number_format($vat2,2)?></strong></td>
                        </tr>
                        <tr>
                            <td><strong>Total Value</strong></td>
                            <td><strong><?=number_format($totalValue2,2)?></strong></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-12">
            <form method="post" action="<?=base_url('finance/confirm_match')?>">
                <input type="hidden" name="proformaId" value="<?=$proforma->invoiceId?>" />
                <input type="hidden" name="invoiceId" value="<?=$invoice->invoiceId?>" />
                <input type="hidden" name="withholdingTax" value="<?=$withholdingTax2?>" />
                <input type="hidden" name="vat" value="<?=$vat2?>" />
                <input type="hidden" name="totalValue" value="<?=$totalValue2?>" />
                <input type="submit" class="btn btn-success" value="Confirm matching"/>
            </form>
        </div>
    </div>
<?php $this->endSection(); ?>
