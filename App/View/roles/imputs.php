<div class="col-md-6">
    <label for="rol_name" class="form-label">Rol</label>
    <input name="rol_name" type="text" class="form-control <?= isset($err->rol_name) ? 'is-invalid' : '' ?>" id="rol_name" value="<?= isset($data->rol_name) ? $data->rol_name : '' ?>">
    <?php if (isset($err->rol_name)) : ?>
        <div class="invalid-feedback">
            <?= $err->rol_name ?>
        </div>
    <?php endif; ?>
</div>

<input type="hidden" name="id" value="<?= isset($data->id) ? $data->id : '' ?> ">