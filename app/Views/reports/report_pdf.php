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
            <td valign="top" style="width:35%; text-align: left"><!--
                <?php if($ttl=='PROFORMA INVOICE'): ?>
                    <div class="head-data">PROFORMA NO.: <b><span style="color: black;"></span></b></div>
                <?php endif; ?>
                <?php if($ttl=='TAX INVOICE'): ?>
                    <div class="head-data">INVOICE NO.: <b><span style="color: black;"></span></b></div>
                <?php endif; ?>
                <div class="head-data">TIN NO.:<b><b><span style="color: black;">1000076645</span></b></div>
                <div class="head-data">VAT.: <b><span style="color: black;padding-left:12px;"> 46371 - T</span></b></div>
                <div class=" head-data" style="margin-top: 9px">DATE: <b><span style="color: black;padding-left:12px;"></span></div>-->
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
    <div style="text-align: center;font-size:17px"><?=$ttl2?></div>
    <table id='billship' style="padding-bottom:2px"><!--
        <tr>
            <td>
                <?php if($ttl=='TAX INVOICE'): ?>
                    LPO NO.: <b></b><br>
                <?php endif; ?>
                CUSTOMER: <b></b><br>
                ADDRESS: <b></b><br>
                EMAIL: <b></b><br>
                PHONE: <b></b><br>
                CAR TYPE: <b></b><br>
                <br>
            </td>
            <td>
                DATE: <b></b><br>
                TIN NO.: <b></b><br>

                CONTACT PERSON: <b></b><br>
                MODE OF PAYMENT: <b></b><br>
                MILEAGE: <b></b>
            </td>
        </tr>
        <tr>
            <td colspan='2'>CAR REGISTRATION NO: <b></b></td>
        </tr>-->
    </table>
    <hr style="color:black">
    <table id="itemz" width="100%" style="width: 100%">
        <tr width="100%">
            <td style="color: black; text-align: center"><strong>Invoice No.</strong></td>
            <td style="color: black; text-align: center;"><strong>Customer</strong></td>
            <td style="color: black; text-align: center;"><strong>Date</strong></td>
            <td style="color: black; text-align: center;"><strong>Value</strong></td>
        </tr>
        <tbody><?php $grand=0; ?>
            <?php foreach($data as $d): ?>
            <tr style="color: #000000;width=100%">
                <td><?=$d['ID']?></td>
                <td><?=$d['CUSTOMER_NAME']?></td>
                <td><?=date('d-M-Y',strtotime($d['DATE']))?></td>
                <td style="text-align: right"><?=$d['CURRENCY'].'. '?><?=number_format($d['sum']['SM'],2,'.',',')?></td>
                <?php $grand=$grand+$d['sum']['SM']; ?>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <hr style="color:black;">
    <table style="width: 40%">
        <tr>
            <td valign="top">INVOICE COUNT: <?=count($data)?> </td>
        </tr>
        <tr>
            <td valign="top">GRAND VALUE: <?=number_format($grand,2,'.',',')?> </td>
        </tr>
    </table><!--
    <div id='notes'>
        note<br>
        note<br>
        note<br>
    </div>-->
</div>
</body>
</html>
