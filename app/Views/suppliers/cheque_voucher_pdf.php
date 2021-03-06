<!DOCTYPE html>
<html>
<head>

    <style>
        html,body{font-family: 'Baloo Bhaina 2', cursive;}
        #invoice{max-width:100%;margin-top:0px;margin-left:3px;margin-right:3px}
        #billship,#company,#items{width:100%;border-collapse:collapse}
        #company td,#items td,#items th{padding:15px}
        #company,#billship{margin-bottom:10px}
        #company img{max-width:300px;height:auto}
        #bigi{font-size:22px;color:black;text-align: center;font-size:25px}
        #billship{background:white;color:black;}
        #billship td{width:33%}
        #items th{text-align:left;border-top:2px solid #f6a5a5;border-bottom:2px solid #f6a5a5}
        #items td{border-bottom:1px solid #f6a5a5}.idesc{color:black}
        #itemz tr td{border: 1px solid black}
        .ttl{font-weight:700}
        .head-data{font-size:18px;text-align:left;}
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
    <table style="width: 100%;padding-top:0px;margin-top:0px;" >
        <tr>
            <td colspan="3" style="text-align: center">
                <h2 style="color: black;text-decoration: underline solid black;color: black;font-size: 53px;text-align: center"><strong>JAPAN AUTO CARE LTD</strong></h2>
            </td>
        </tr>
        <tr style="margin-bottom:2px;padding-bottom:2px">
            <td rowspan='1' valign="top" style="text-align: left;width: 150px">
                <img width="200px" src='<?=base_url("assets/img/logo.png")?>'/>
            </td>
            <td valign="top" style="width:60%;text-align: center;">
                <div style="color: black" class="head-data"><b>Plot No. 1105, Musajjalumbwa Road</b></div>
                <div style="color: black" class="head-data"><b>P. O. BOX 33246</b></div>
                <div style="color: black" class="head-data"><b>Kampala Uganda</b></div>
                <div style="color: black" class="head-data"><b>Tel: 0392-940769</b></div>
                <div style="color: black" class="head-data"><b>Email: jacitdgarage@gmail.com</b></div>
            </td>
            <td valign="top" style="width:35%; text-align: left">
                    <div class="head-data">VOUCHER NO.: <b><span style="color: black;"><?=$voucher->id?></span></b></div>

                <div class="head-data">TIN NO.:<b><b><span style="color: black;">1000076645</span></b></div>
                <div class="head-data">VAT.: <b><span style="color: black;padding-left:12px;"> 46371 - T</span></b></div>
                <div class=" head-data" style="margin-top: 9px">DATE: <b><span style="color: black;padding-left:12px;"><?=date('d/M/Y',strtotime($voucher->date))?></span></div>
            </td>
        </tr>
        <tr style="padding: 0px;margin: 0px">
            <td colspan="3" style="text-align: center">
                <div style="color: #000000;font-size: 15px;text-align: center;">Specialists in Engine Overhauling, Panel Beating, Spraying, Engine Tuning,Services for both Diesel and Petrol Cars.</div>
            </td>
        </tr>
    </table>
    <hr class="d">
    <hr class="d">
    <div id='bigi' style="text-decoration:underline;"><?=$ttl?></div>
    <table id='billship' style="padding-bottom:2px">
        <tr>
            <td>
                CUSTOMER NAME: <b><?=$voucher->name?></b><br>
                COMPANY NAME: <b><?=$voucher->supplier?></b><br>
                ADDRESS: <b><?=$voucher->address?></b><br>
                CHEQUE NO: <b><?=$voucher->chequeNo?></b><br>
                <br>
            </td>
        </tr>
        <tr>

        </tr>
    </table>
    <hr style="color:black">
    <table id="itemz" width="100%" style="width: 100%">
        <tr width="100%">
            <td style="color: black; text-align: center;width:8%"><strong>Sn.</strong></td>
            <td style="color: black; text-align: center;"><strong>Particulars</strong></td>
            <td style="color: black; text-align: center;"><strong>Code</strong></td>
            <td style="color: black; text-align: center;"><strong>Amount</strong></td>
        </tr>
        <tbody>
        <?php $grandTotal=0;$grandestTotal=0;?>
        <?php foreach ($items as $key=>$item) :?>
            <tr style="color: #000000;width=100%">
                <td><?=$key+1?></td>
                <td><?=$item['particulars']?></td>
                <td><?=$item['code']?> </td>
                <td style="text-align: right"><?=number_format($item['amount'])?></td>
                <?php $grandTotal+=$item['amount']; ?>
            </tr>
        <?php endforeach; ?>
        <tr>
            <td class='ttl b-off' COLSPAN="3" style="text-align: right;color: black;"><strong> TOTAL: </strong></td>
            <td style="padding-left:4px;text-align: right;color: black;"><strong> <?=number_format($grandTotal,2,'.',',')?></strong></td>
        </tr><!--
        <tr>
            <td class='ttl' colspan="4" style="text-align: right"><strong>ADD: VAT 18%: </strong></td>
            <?php $vat=0.18*$grandTotal; $grandestTotal+=$grandTotal+$vat; ?>
            <td  style="text-align: right"><strong><?=number_format($vat,2,'.',',')?></strong></td>
        </tr>
        <tr>
            <td class='ttl' colspan="3" style="text-align: right;color: black;"><strong>GRAND TOTAL: </strong></td>
            <td style="text-align: right;color: black;padding-right: 0px;"><strong> <?=number_format($grandestTotal,2,'.',',')?></strong></td>
        </tr>-->
        <tr>
            <td colspan="4" style="text-align: right">
                <?php
                $f=new NumberFormatter("en",NumberFormatter::SPELLOUT);
                echo "<strong>".$f->format($grandTotal )." shillings only</strong>";?></td>
        </tr>
        </tbody>
    </table>
    <hr style="color:black;">
    <table style="width: 40%">
        <tr>
            <td valign="top" style="padding-bottom: 30px;">Prepared By:</td>
            <td valign="top" style="padding-bottom: 30px;">Passed By:</td>
            <td valign="top" style="padding-bottom: 30px;">Authorized By:</td>
            <td valign="top" style="padding-bottom: 30px;">Received/Posted/Delivered By:</td>
        </tr>
        <tr style="margin-top: 15px; padding-top: 30px">
            <td>_______________________<br><?=$voucher->maker?></td>
            <td>_______________________<br><?=$voucher->passer?></td>
            <td>_______________________<br><?=$voucher->authorizer?></td>
            <td>_______________________<br><?=$voucher->receiver?></td>
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
