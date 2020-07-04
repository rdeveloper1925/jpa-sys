<?php $this->extend('layouts/app'); ?>
<?php $this->section('content'); ?>
<div class="row">
	<div class="text-center">
		<div class="error mx-auto" data-text="ERROR!!">ERROR!!</div>
		<p class="lead text-gray-800 mb-5"><?=$message?></p>
	</div>
</div>
<?php $this->endsection(); ?>
