<?php include ext('layoutdash.head') ?>
<div class="pcoded-content">
    <!-- [ breadcrumb ] start -->
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col d-flex flex-column flex-md-row justify-content-between align-items-center">
                    <div class="page-header-title">
                        <h5 class="m-b-10">Panel de Roles</h5>
                    </div>
                    <div class="">
                        <?php if (can('users.create')) : ?>
                            <a href="<?= route('roles.create') ?>" class="btn btn-outline-dark btn-sm">Crear Rol</a>
                        <?php endif;  ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- [ breadcrumb ] end -->

    <!-- [ Main Content ] start -->
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header">
                    <h5>Lista de usuarios</h5>
                </div>
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Rol</th>
                                    <th scope="col">Permisos</th>
                                    <th scope="col">Editar</th>
                                    <th scope="col">Eliminar</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($roles as $r) : ?>
                                    <tr>
                                        <th scope="row"><?= $r->id ?></th>
                                        <td><?= $r->rol_name ?></td>

                                        <?php if (can('roles.permissions')) : ?>
                                            <td><a href="<?= route('roles.permissions') . '?id=' . $r->id ?>" class="btn btn-outline-primary btn-sm"><i class="bi bi-key"></i></a></td>
                                        <?php endif;  ?>

                                        <?php if (can('roles.edit')) : ?>
                                            <td><a href="<?= route('roles.edit') . '?id=' . $r->id ?>" class="btn btn-outline-warning btn-sm"><i class="bi bi-pencil"></i></a></td>
                                        <?php endif;  ?>

                                        <?php if (can('roles.destroy')) : ?>
                                            <td><a href=<?= route('roles.destroy') . '?id=' . $r->id ?>" class="btn btn-outline-danger btn-sm"><i class="bi bi-trash3"></i></a></td>
                                        <?php endif;  ?>

                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- [ Main Content ] end -->
</div>
<?php include ext('layoutdash.footer') ?>