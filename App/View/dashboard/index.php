<?php include ext('layoutdash.head') ?>
<div class="pcoded-content">
	<!-- [ breadcrumb ] start -->
	<div class="page-header">
		<div class="page-block">
			<div class="row align-items-center">
				<div class="col d-flex flex-column flex-md-row justify-content-between align-items-center">
					<div class="page-header-title">
						<h5 class="m-b-10">Panel de Resultados</h5>
					</div>
					<div class="">
						<button type="button" class="btn btn-primary btn-sm">Primary</button>
						<button type="button" class="btn btn-success btn-sm">Success</button>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- [ breadcrumb ] end -->

	<!-- [ Main Content ] start -->
	<div class="row">
		<div class="col-lg-3 col-md-6">
			<div class="card text-white bg-secondary mb-4">
				<div class="card-header bg-transparent border-bottom">Hola: <?= $userName ?></div>
				<div class="card-body">
					<h5 class="card-title">email: <?= session()->user()->email ?></h5>
					<p class="card-text">rol: <?= session()->user()->rol_name ?></p>
				</div>
			</div>
		</div>
	</div>
	<!-- [ Main Content ] end -->
</div>
<?php include ext('layoutdash.footer') ?>