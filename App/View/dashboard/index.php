<?php
// $linksCss2 = [
//     base_url . '/assets/plugins/dataTables/datatables.bootstrap5.css',
// ];

$linksScript2 = [
	base_url . '/assets/js/dashboard.js',
];
?>
<?php include ext('layoutdash.head') ?>
<div class="pcoded-content">
	<!-- [ breadcrumb ] start -->
	<div class="page-header">
		<div class="page-block">
			<div class="row align-items-center">
				<div class="col d-flex flex-column flex-md-row justify-content-between align-items-center">
					<div class="page-header-title">
						<h5 class="m-b-10">Panel de Resultados</h5>
						<input id="urlCantidades" type="hidden" data-url="<?= route('dashboard.cantidades') ?>">
						<input id="urlVentaCompra" type="hidden" data-url="<?= route('dashboard.ventacompra') ?>">
						<input id="urlProductoStock" type="hidden" data-url="<?= route('dashboard.productostock') ?>">
						<input id="urlProductoTop" type="hidden" data-url="<?= route('dashboard.topventas') ?>">
					</div>
					<!-- <div class="">
						<button type="button" class="btn btn-primary btn-sm">Primary</button>
						<button type="button" class="btn btn-success btn-sm">Success</button>
					</div> -->
				</div>
			</div>
		</div>
	</div>
	<!-- [ breadcrumb ] end -->

	<!-- [ Main Content ] start -->
	<!-- cabezera de datos -->
	<div class="row">
		<div class="col-sm-3">
			<div class="card prod-p-card bg-success text-white">
				<div class="card-body p-3">
					<div class="row align-items-center m-b-0">
						<div class="col">
							<h6 class="m-b-5 text-white">T. Usuarios</h6>
							<h3 class="m-b-0 text-white totalUsuarios">5</h3>
						</div>
						<div class="col-auto">
							<i class="bi bi-person-gear"></i>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-sm-3">
			<div class="card prod-p-card bg-primary text-white">
				<div class="card-body p-3">
					<div class="row align-items-center m-b-0">
						<div class="col">
							<h6 class="m-b-5 text-white">T. Clientes</h6>
							<h3 class="m-b-0 text-white totalClientes">15</h3>
						</div>
						<div class="col-auto">
							<i class="bi bi-people"></i>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-sm-3">
			<div class="card prod-p-card bg-danger text-white">
				<div class="card-body p-3">
					<div class="row align-items-center m-b-0">
						<div class="col">
							<h6 class="m-b-5 text-white">T. Proveedores</h6>
							<h3 class="m-b-0 text-white totalProveedores">8</h3>
						</div>
						<div class="col-auto">
							<i class="bi bi-truck"></i>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-sm-3">
			<div class="card prod-p-card bg-warning text-white">
				<div class="card-body p-3">
					<div class="row align-items-center m-b-0">
						<div class="col">
							<h6 class="m-b-5 text-white">T. Productos</h6>
							<h3 class="m-b-0 text-white totalProductos">15</h3>
						</div>
						<div class="col-auto">
							<i class="bi bi-box-seam"></i>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- grafico de compra y venta y top productos mas vendidos-->
	<div class="row">
		<div class="col-xl-6 col-md-12">
			<div class="card">
				<div class="card-header">
					<h5>Ventas y Compras <?= date("Y") ?></h5>
				</div>
				<div class="card-body">
					<!-- <div class="row pb-2">
						<div class="col-auto m-b-10">
							<h3 class="mb-1">$21,356.46</h3>
							<span>Total Sales</span>
						</div>
						<div class="col-auto m-b-10">
							<h3 class="mb-1">$1935.6</h3>
							<span>Average</span>
						</div>
					</div> -->
					<div id="graficoVentasCompras"></div>
				</div>
			</div>
		</div>
		<div class="col-xl-6 col-md-12">
			<div class="card">
				<div class="card-header">
					<h5>5 Productos más Vendidos</h5>
				</div>
				<div class="card-body">
					<div class="row ">
						<div class="col-12">
							<div id="productoMasVendidos"></div>
						</div>
						<div class="col-12">
							<!-- <div id="tablasTopVentas"></div> -->
							<div class="table-responsive">
								<table class="table table-hover table-sm table-bordered border-dark">
									<thead class="">
										<tr class="">
											<th class="text-center p-0 m-0 bg-dark text-white">Producto</th>
											<th class="text-center p-0 m-0 bg-dark text-white">Cant Ventas</th>
										</tr>
									</thead>
									<tbody id="tablasTopVentas">
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- Lista de productos con stock minimo y productos sin stock -->
		<div class="row">
			<div class="col-xl-6 col-md-12">
				<div class="card">
					<div class="card-header">
						<h5>Productos debajo del stock Mínimo</h5>
					</div>
					<div class="card-body">
						<div class="table-responsive">
							<table class="table table-striped" id="simpleDatatableStockMin"></table>
						</div>
					</div>
				</div>
			</div>
			<div class="col-xl-6 col-md-12">
				<div class="card">
					<div class="card-header">
						<h5>Productos Sin stock</h5>
					</div>
					<div class="card-body">
						<div class="table-responsive">
							<table class="table table-striped" id="simpleDatatableCeroStock"></table>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- [ Main Content ] end -->
	</div>
	<?php include ext('layoutdash.footer') ?>