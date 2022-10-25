<?php include ext('layoutdash.head') ?>
<div class="pcoded-content">
    <!-- [ breadcrumb ] start -->
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col d-flex flex-column flex-md-row justify-content-between align-items-center">
                    <div class="page-header-title">
                        <h5 class="m-b-10">Permisos para: <?= $rol->rol_name ?></h5>
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
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5>Lista de Permisos</h5>
                </div>
                <div class="card-body">
                    <!-- <h5>Form controls</h5>
                    <hr> -->
                    <form action="<?= route('roles.permissions') ?>" method="post">
                        <div class="row">
                            <?= csrf() ?>
                            <?php foreach ($permissionsGroup as $g) : ?>
                                <div class="col-md-3">
                                    <div class="card h-80">
                                        <div class="card-header p-2">
                                            <h5 class="card-title m-0"><?= ucfirst($g[0]->title) ?></h5>
                                        </div>
                                        <div class="card-body p-2">
                                            <?php foreach ($g as $p) : ?>
                                                <div class="form-check">
                                                    <input class="form-check-input" id="<?= $p->id ?>" type="checkbox" name="<?= $p->per_name ?>" value="<?= $p->id ?>" id="<?= $p->id ?>" <?= in_array($p->per_name, array_column((array)$permisosRol, 'per_name')) ? 'checked' : '' ?>>
                                                    <label class="form-check-label" for="<?= $p->id ?>">
                                                        <?= $p->description ?>
                                                    </label>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>

                            <div class="col-12 text-center">
                                <input type="hidden" name="rol_id" value="<?= $rol->id ?>">
                                <button type="submit" class="btn btn-primary">Actualizar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- [ Main Content ] end -->
</div>
<?php include ext('layoutdash.footer') ?>