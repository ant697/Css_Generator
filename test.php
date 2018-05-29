<?php
$tab = ["a", "b", "c"];
foreach ($tab as $key => $value) {
    echo $key;
    continue;
    echo "ca marche pas";
}