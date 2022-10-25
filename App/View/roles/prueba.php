<?php
// dd((array)$permisosRol);

foreach ($permissions as $p) {
    d($p->per_name);
    try {
        d(in_array($p->per_name, array_column((array)$permisosRol, 'per_name')));
    } catch (Throwable $e) {
        echo $e->getMessage();
        exit;
    }
}

// foreach ($permisosRol as $pp) {
//     d($pp->per_name);
// }
