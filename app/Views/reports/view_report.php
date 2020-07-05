<?php $this->extend('layouts/app'); ?>
<?php $this->section ('content') ?>
<div class="row">
    <div class="col-md-12 col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Generated Report</h6>
            </div>
            <div class="card-body">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                    <tr>
                        <th>Invoice no</th>
                        <th>Business Partner</th>
                        <th>Date</th>
                        <th>Tax Amount (VAT)</th>
                        <th>Narration</th>
                        <th>LPO No</th>
                        <th>value</th>
                    </tr>
                    </thead>
                    <tfoot>
                    <tr>
                        <th>Invoice no</th>
                        <th>Business Partner</th>
                        <th>Date</th>
                        <th>Tax Amount (VAT)</th>
                        <th>Narration</th>
                        <th>LPO No</th>
                        <th>value</th>
                    </tr>
                    </tfoot>
                    <tbody>
                    <?php foreach ($data as $d) : ?>
                        <tr>
                            <td><?=$d['ID']?></td>
                            <td><?=$d['CUSTOMER_NAME']?></td>
                            <td><?=$d['DATE']?></td>
                            <td><?=$d['vat']?></td>
                            <td><?=$d['NARRATION']?></td>
                            <td><?=$d['LPO']?></td>
                            <td><?=$d['sum']['SM']?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <a href="<?=base_url('reports/excel/'.$from.'/'.$to)?>" class="btn btn-success">Generate Excel</a><br><BR>
                <a href="<?=base_url('reports/pdf')?>" class="btn btn-primary">Generate PDF</a><br>
            </div>
        </div>
    </div>
    <script>

    </script>
</div>
<?php $this->endsection();?>
