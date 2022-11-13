<?php include ext('layoutDash.menuDash') ?>

</div>
<!-- [ Main Content ] end -->



<?php foreach ($linksScript as $value) : ?>
    <script src="<?= $value ?>"></script>
<?php endforeach; ?>
<?php if (isset($linksScript2)) : ?>
    <?php foreach ($linksScript2 as $value) : ?>
        <script src="<?= $value ?>"></script>
    <?php endforeach; ?>
<?php endif; ?>
</body>

</html>