<div class="col-md-6">
    <label for="per_name" class="form-label">Permiso(nombre de la ruta)</label>
    <input name="per_name" type="text" class="form-control <?= isset($err->per_name) ? 'is-invalid' : '' ?>" id="per_name" value="<?= isset($data->per_name) ? $data->per_name : '' ?>">
    <?php if (isset($err->per_name)) : ?>
        <div class="invalid-feedback">
            <?= $err->per_name ?>
        </div>
    <?php endif; ?>
</div>
<div class="col-md-6">
    <label for="description" class="form-label">Detalle</label>
    <input name="description" type="text" class="form-control <?= isset($err->description) ? 'is-invalid' : '' ?>" id="description" value="<?= isset($data->description) ? $data->description : '' ?>">
    <?php if (isset($err->description)) : ?>
        <div class="invalid-feedback">
            <?= $err->description ?>
        </div>
    <?php endif; ?>
</div>


<input type="hidden" name="id" value="<?= isset($data->id) ? $data->id : '' ?> ">