<!DOCTYPE html>
<html>
<head>

	<style>
		html,body{font-family: 'Baloo Bhaina 2', cursive;}
		#invoice{max-width:800px;margin:0 auto}
		#billship,#company,#items{width:100%;border-collapse:collapse}
		#company td,#billship td,#items td,#items th{padding:15px}
		#company,#billship{margin-bottom:10px}
		#company img{max-width:300px;height:auto}
		#bigi{font-size:22px;color:#ad132f;text-align: center;}
		#billship{background:white;color:black}
		#billship td{width:33%}
		#items th{text-align:left;border-top:2px solid #f6a5a5;border-bottom:2px solid #f6a5a5}
		#items td{border-bottom:1px solid #f6a5a5}.idesc{color:black}
		#itemz tr td{border: 1px solid #f6a5a5}
		.ttl{font-weight:700}
		.right{text-align:right}#notes{margin-top:30px;font-size:.95em}
		.title-info{
			color: #000000;
			font-size: 15px
		}
		hr.d{
			padding:0px;
			margin:1px;
			color: black;
		}
	</style>
	<link href="https://fonts.googleapis.com/css2?family=Baloo+Bhaina+2&display=swap" rel="stylesheet">
	<!--<link href="<?=base_url('assets/css/sb-admin-2.min.css')?>" rel="stylesheet">-->
</head>
<body>
<div id='invoice'>
<table style="width: 100%;" >
        <tr>
			<td rowspan='2' valign="top" style="text-align: left;width: 150px">
				<img width="300px" src='<?=base_url("assets/img/logo.png")?>'/>
			</td>
            <td colspan="2" style="text-align: left">
                <h2 style="color: black;text-decoration: underline solid black;color: black;font-size: 50px;text-align: left"><strong>JAPAN AUTO CARE LTD</strong></h2>
            </td>
        </tr>
		<tr style="margin-bottom:2px;padding-bottom:2px">
			<td valign="top" style="width:60%;text-align: left;">
				<div style="color: black" class="head-data">Plot No. 1105, Musajjalumbwa Road</div>
				<div style="color: black" class="head-data">P. O. BOX 33246</div>
				<div style="color: black" class="head-data">Kampala Uganda</div>
				<div style="color: black" class="head-data">Tel: 0392-940769</div>
				<div style="color: black" class="head-data">Email: jacltdgarage@gmail.com</div>
			</td>
			<td valign="top" style="width:35%; text-align: left">
				<div class="head-data">RECEIPT NO.: <b><span style="color: black;"><?=$receiptId?></span></b></div>
				<div class="head-data">TIN NO.:<b><b><span style="color: black;">1000076645</span></b></div>
				<div class="head-data">VAT.: <b><span style="color: black;padding-left:12px;"> 46371 - T</span></b></div>
				<div class=" head-data" style="margin-top: 9px">DATE: <b><span style="color: black;padding-left:12px;"><?=date('d/M/Y',strtotime($receipt->date))?></span></div>
			</td>
		</tr>
		<tr style="padding: 0px;margin: 0px">
			<td colspan="3" style="text-align: center">
				<div style="color: #000000;font-size: 15px;text-align: center;">Specialists in Engine Overhauling, Panel Beating, Spraying, Engine Tuning,Services for both Diesel and Petrol Cars.</div>
			</td>
		</tr>
	</table>
	<hr class="d" >
	<div id='bigi'>TAX RECEIPT</div>
	<table id='billship'>
		<tr>
			<td>
				RECEIPT NO.: <b><?=$receiptId?></b><br>
				REF NO.: <b><?=$receipt->refNo?></b><br>
				DESCRIPTION: <b><?=$receipt->description?></b><br>
                NARRATION: <b><?=$receipt->narration?></b><br>
			</td>
			<td>
				CURRENCY: <b><?=$receipt->currency?></b><br>
				REF DATE: <b><?=date('d/M/Y',strtotime($receipt->refDate))?></b><br>
                ACCOUNT NAME: <b><?=$receipt->accountName?></b><br>
                CURRENCY: <b><?=$receipt->currency?></b><br>
			</td>
		</tr>
	</table>
	<hr style="color:black;">
	<table id="itemz" width="100%">
        <tr width="100%">
            <td style="color: black; text-align: left;"><strong>Serial No.</strong></td>
            <td style="color: black; text-align: left;width: 25%"><strong>Description</strong></td>
            <td style="color: black; text-align: left;width: 15%"><strong>Quantity</strong></td>
            <td style="color: black; text-align: left;width: 15%"><strong>Rate</strong></td>
            <td style="color: black; text-align: left;"><strong>Total</strong></td>
        </tr>
		<tbody>
        <?php $grandTotal=0;$grandestTotal=0;?>
        <?php foreach ($items as $key=>$item) :?>
            <tr style="color: #000000">
                <td><?=$key+1?></td>
                <td><?=$item['inventoryItem']?></td>
                <td><?=$item['quantity']?> <?php echo ' '.$item['units']?></td>
                <td style="text-align: right"><?=number_format($item['unitCost'])?></td>
                <td style="text-align: right"><?=number_format($item['quantity']*$item['unitCost'])?></td>
                <?php $grandTotal+=$item['quantity']*$item['unitCost']; ?>
            </tr>
        <?php endforeach; ?>
        <tr>
            <td class='ttl' COLSPAN="4" style="text-align: right;color: black;"><strong> TOTAL: </strong></td>
            <td style="text-align: right;color: black;"><strong><?=$receipt->currency?>. <?=number_format($grandTotal,2,'.',',')?></strong></td>
        </tr>
        <tr>
            <td class='ttl' COLSPAN="4" style="text-align: right;color: #000000;"><strong> AMOUNT PAID: </strong></td>
            <td style="text-align: right;color: #000000;"><strong><?=$receipt->currency?>. <?=number_format($amount,2,'.',',')?></strong></td>
        </tr>
        <tr>
            <td class='ttl' COLSPAN="4" style="text-align: right;color: #7a4652;"><strong> BALANCE: </strong></td>
            <?php $bal=$grandTotal-$amount; ?>
            <td style="text-align: right;color: #7a4652;"><strong><?=$receipt->currency?>. <?=number_format($bal,2,'.',',')?></strong></td>
        </tr>
			<tr>
				<td colspan="5">
                    <?php
                    $f=new NumberFormatter("en",NumberFormatter::SPELLOUT);
                    echo "AMOUNT PAID:  <strong>".$f->format($amount )." shillings only</strong>";?>
                    <?php if ($bal>0): ?>
                    <br><?php
                    $f=new NumberFormatter("en",NumberFormatter::SPELLOUT);
                    echo "BALANCE:  <strong>".$f->format($bal )." shillings only</strong>";?>
                    <?php endif; ?>
				</td>
			</tr>
		</tbody>
	</table>
	<hr>
	<table style="width: 100%">
		<tr>
			<td valign="top" style="padding-bottom: 30px;">Prepared By</td>
			<td valign="top" style="padding-bottom: 30px;text-align: right">Received By</td>
		</tr>
		<tr style="margin-top: 15px; padding-top: 30px">
			<td>_______________________</td>
			<td style="text-align: right">_______________________</td>
		</tr>
		<tr>
			<td valign="bottom" style="text-align: right;"><?=$receipt->receivedBy?></td>
		</tr>
	</table>
	<!--<div id='notes'>
		note<br>
		note<br>
		note<br>
	</div>-->
</div>
</body>
</html>
