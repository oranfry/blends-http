<?php
$blends = [];

foreach (array_keys(Config::get()->blends) as $name) {
    $blends[] = Blend::load($name);
}

return [
    'data' => $blends,
];
