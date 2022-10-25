<div class="col-md-6">
    <label for="name" class="form-label">Nombre</label>
    <input name="name" type="text" class="form-control <?= isset($err->name) ? 'is-invalid' : '' ?>" id="name" value="<?= isset($data->name) ? $data->name : '' ?>">
    <?php if (isset($err->name)) : ?>
        <div class="invalid-feedback">
            <?= $err->name ?>
        </div>
    <?php endif; ?>
</div>
<div class="col-md-6">
    <label for="email" class="form-label">Email</label>
    <input name="email" type="email" class="form-control <?= isset($err->email) ? 'is-invalid' : '' ?>" id="email" value="<?= isset($data->email) ? $data->email : '' ?>">
    <?php if (isset($err->email)) : ?>
        <div class="invalid-feedback">
            <?= $err->email ?>
        </div>
    <?php endif; ?>
</div>
<div class="col-md-6">
    <label for="password" class="form-label">Contraseña</label>
    <input name="password" type="password" class="form-control <?= isset($err->password) ? 'is-invalid' : '' ?>" id="password" value="<?= isset($data->password) ? $data->password : '' ?>">
    <?php if (isset($err->password)) : ?>
        <div class="invalid-feedback">
            <?= $err->password ?>
        </div>
    <?php endif; ?>
</div>

<div class="col-md-6">
    <label for="rol" class="form-label">Rol</label>
    <select id="rol" name="rol_id" class="form-select <?= isset($err->rol_id) ? 'is-invalid' : '' ?>">
        <option value="">Seleccione...</option>
        <?php foreach ($roles as $rol) : ?>
            <option <?= isset($data->rol_id) && $data->rol_id == $rol->id ? 'selected' : '' ?> value="<?= $rol->id; ?>"><?= $rol->rol_name; ?></option>
        <?php endforeach; ?>
    </select>
    <?php if (isset($err->rol_id)) : ?>
        <div class="invalid-feedback">
            <?= $err->rol_id ?>
        </div>
    <?php endif; ?>
</div>

<div class="col-md-6 border mt-3">
    <h5 class="font-14 mb-2">Estado</h5>
    <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="status" id="status1" value="1" <?= isset($data->status) && $data->status == 1 ? 'checked' : '' ?>>
        <label class="form-check-label" for="status1">Activo</label>
    </div>
    <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="status" id="status2" value="0" <?= isset($data->status) && $data->status == 0 ? 'checked' : '' ?>>
        <label class="form-check-label" for="status2">Inactivo</label>
    </div>
</div>

<input type="hidden" name="id" value="<?= isset($data->id) ? $data->id : '' ?> ">