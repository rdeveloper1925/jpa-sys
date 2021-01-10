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
                <div style="color: black" class="head-data"><b>Email: jacltdgarage@gmail.com</b></div>
            </td>
            <td valign="top" style="width:35%; text-align: left">
                <div class="head-data">CERTIFICATE NO.: <b><span style="color: black;"><?=$data->id?></span></b></div>
                <div class="head-data">TIN NO.:<b><b><span style="color: black;">1000076645</span></b></div>
                <div class="head-data">VAT.: <b><span style="color: black;padding-left:12px;"> 46371 - T</span></b></div>
                <div class=" head-data" style="margin-top: 9px">DATE: <b><span style="color: black;padding-left:12px;"><?=date('d/M/Y')?></span></div>
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
    <div id='bigi' style="text-decoration:underline;"><b><?=$ttl?></b></div><br>
    <table id='billship' style="padding-bottom:2px">
        <tr>
            <td colspan='2'>CUSTOMER: <b><?=$data->customerName?></b></td>
        </tr>
        <tr>
            <td>
                ADDRESS: <b><?=$data->address?></b><br>
                EMAIL: <b><?=$data->email?></b><br>
                PHONE: <b><?php if($data->phone[0]!=='+'){echo '+';}?><?=$data->phone?></b><br>
                CAR TYPE: <b><?=$data->carType?></b><br>
                CAR CHASIS NO: <b><?=$data->carChasisNo?></b><br>
                CAR REGISTRATION NO: <b><?=$data->carRegNo?><br>
            </td>
            <td>
                DATE COMPLETED: <b><?=date('d/M/Y',strtotime($data->dateCompleted))?></b><br>
                TIN NO.: <b><?=$data->tinNo?></b><br>
                CONTACT PERSON: <b><?=$data->contactPerson?></b><br>
                MILEAGE: <b><?=$data->mileage?></b><br>
                INVOICE NO: <b><?=$data->invoiceNo?></b><br>
                ENGINEER NAME: <b><?=$data->engineerName?>
            </td>
    </table>
    <hr style="color:black">
    <b>DESCRIPTION OF REPAIRS COMPLETED ON THE VEHICLE</b>
    <textarea width="100%"><?=$data->repairsDone?></textarea>
    <hr style="color:black">
    <b>I CERTIFY THAT THE VEHICLE HAS BEEN REPAIRED AS REQUESTED</b><br><br>
    WORKSHOP ENGINEER SIGNATURE: __________________________________________________<br>
    <b><?=$data->engineerName?></b><br><br>

    TRANSPORT OFFICER SIGNATURE: ____________________________________________________<br>
    <b><?=$data->transportOfficer?></b><br><br>

    DRIVER SIGNATURE: _________________________________________________________________<br>
    <b><?=$data->driverName?></b><br><br>

    Date of Completion: <b><?=date('d/M/Y',strtotime($data->dateCompleted))?></b><br><br>

    <b>Additional Comments:</b>
    <textarea width="100%"><?=$data->comments?></textarea>

</div>
</body>
</html>
