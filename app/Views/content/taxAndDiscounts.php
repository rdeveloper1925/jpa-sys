<?php $this->extend('layouts/app');
$this->section('content') ?>
<div class="row">
	<div class="col-md-3">
	<div class="card mb-4">
		<div class="card-header">
			Apply Discount
		</div>
		<div class="card-body">
			<?php if(empty($discount)): ?>
			<form action="<?=base_url('invoices/apply_discount/'.$invoiceId)?>" method="post">
				<div class="form-group">
					<label for="">Discount (%)</label>
					<input class="form-control" step=0.01 type="number" name="discount" required max="99" min="0"/>
				</div>
				<input type="submit" class="btn btn-success" value="Apply Discount"/>
			</form>
			<?php elseif (!empty($discount)): ?>
			<h5>Applied Discount: <strong><?=$discount[0]['discount']?>%</strong></h5>
			<a href="<?=base_url('invoices/remove_discount/'.$invoiceId)?>" class="btn btn-danger">Remove Discount</a>
			<?php endif; ?>
		</div>
	</div>
	</div>
	<div class="col-md-9">
	<div class="card mb-4">
		<div class="card-header">
			Invoice Preview (No. <?=$invoiceId?>) LPO No. <?=$data->lpoNo?>
		</div>
		<div class="card-body">
			<div class="row">
				<div class="col-5">
				<table class="table table-success">
					<tr>
						<td sytle="padding-right:9px"><b>Customer Name:</b></td>
						<td><?=$data->customerName?></td>
					</tr>
					<tr>
						<td sytle="padding-right:9px"><b>Contact Person:</b></td>
						<td><?=$data->contactPerson?></td>
					</tr>
					<tr>
						<td sytle="padding-right:9px"><b>Date:</b></td>
						<td><?=date('Y-m-d',strtotime($data->date))?></td>
					</tr>
					<tr>
						<td sytle="padding-right:9px"><b>Phone:</b></td>
						<td><?=$data->phone?></td>
					</tr>
					<tr>
						<td sytle="padding-right:9px"><b>Currency:</b></td>
						<td><?=$data->currency?></td>
					</tr>
					<tr>
						<td sytle="padding-right:9px"><b>Mileage:</b></td>
						<td><?=$data->mileage?></td>
					</tr>
				</table>
				</div>
				<div class="col-7">
					<table class="table table-success">
						<tr>
							<td sytle="padding-right:9px"><b>Customer Tin No.:</b></td>
							<td><?=$data->tinNo?></td>
						</tr>
						<tr>
							<td sytle="padding-right:9px"><b>Mode Of Payment:</b></td>
							<td><?=$data->modeOfPayment?></td>
						</tr>
						<tr>
							<td sytle="padding-right:9px"><b>Address:</b></td>
							<td><?=$data->address?></td>
						</tr>
						<tr>
							<td sytle="padding-right:9px"><b>Email:</b></td>
							<td><?=$data->email?></td>
						</tr>
						<tr>
							<td ><b>Car Type:</b></td>
							<td><?=$data->carType?></td>
						</tr>
						<tr>
							<td sytle="padding-right:9px"><b>Car Registration:</b></td>
							<td><?=$data->carRegNo?></td>
						</tr>
					</table>
				</div>
				<div class="container col-md-12">
					<table class="table table-bordered">
						<thead>
							<th>Serial No.</th>
							<th>Description</th>
							<th>Quantity</th>
							<th>Unit Price</th>
							<th>Total</th>
						</thead>
						<tbody>
						<?php $grandTotal=0;$grandestTotal=0;?>
						<?php foreach ($items as $key=>$item) :?>
							<tr>
								<td><?=$key+1?></td>
								<td><?=$item['inventoryItem']?></td>
								<td><?=$item['quantity']?></td>
								<td style="text-align: right"><?=number_format($item['unitCost'])?></td>
								<td style="text-align: right"><?=number_format($item['quantity']*$item['unitCost'])?></td>
								<?php $grandTotal+=$item['quantity']*$item['unitCost']; ?>
							</tr>
						<?php endforeach; ?>
						<tr>
							<td COLSPAN="4" style="text-align: right"><strong> TOTAL: </strong></td>
							<td style="text-align: right"><strong><?=$data->currency?>. <?=number_format($grandTotal,2,'.',',')?></strong></td>
						</tr>
						<?php if(!empty($discount)): ?>
						<tr>
							<td colspan="4" style="text-align: right"><strong>LESS: DISCOUNT (<?=$discount[0]['discount']?>%): </strong></td>
							<?php $discount=($discount[0]['discount']/100)*$grandTotal; $grandTotal-=$discount;?>
							<td colspan="4" style="text-align: right"><strong>(<?=$data->currency?>. <?=number_format($discount,2,'.',',')?>)</strong></td>
						</tr>
						<?php endif; ?>
						<tr>
							<td colspan="4" style="text-align: right"><strong>ADD: VAT (18%): </strong></td>
							<?php $vat=0.18*$grandTotal; $grandestTotal=$grandTotal+$vat; ?>
							<td colspan="4" style="text-align: right"><strong><?=$data->currency?>. <?=number_format($vat,2,'.',',')?></strong></td>
						</tr>
						<tr>
							<td colspan="4" style="text-align: right"><strong>GRAND TOTAL: </td>
							<td colspan="4" style="text-align: right"><strong><?=$data->currency?>. <?=number_format( $grandestTotal,2,'.',',' )?></strong></td>
						</tr>
						<tr>
							<td colspan="5" style="text-align: right" >
                                <strong><span id="numToWordsss" ></span></strong>
                            </td>
						</tr>
                        <tr>
                            <td colspan="5">
                                <?php
                                $f = new NumberFormatter("en", NumberFormatter::SPELLOUT);
                                echo $f->format($grandestTotal).' shillings only';
                                ?>
                            </td>
                        </tr>
						</tbody>
					</table>
                    <form action="<?=base_url('invoices/generate/'.$invoiceId)?>" method="post">
                        <input type="hidden" id="words" name="words"/>
						<p>Created by: <?=$maker->maker?></p>
                        <button type="submit"  class="btn btn-md btn-success">Generate TAX INVOICE PDF</button><br>
                    </form><!--
                    <form action="<?=base_url('invoices/generate2/'.$invoiceId)?>" method="post">
                        <input type="hidden" id="words2" name="words2"/>
                        <input type="submit"  class="btn btn-md btn-success" value="Generate PROFORMA INVOICE PDF"/>
                    </form>
                       -->
				</div>
			</div>
		</div>
	</div>
</div>
</div>

<?php $this->endsection(); ?>
