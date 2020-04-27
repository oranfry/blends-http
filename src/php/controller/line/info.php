<?php
$linetype = Linetype::load(LINETYPE_NAME);
$parents = Linetype::find_parent_linetypes(LINETYPE_NAME);
$parenttypes = [];

foreach ($parents as $parent) {
    $parenttypes[] = preg_replace('/.*\\\\/', '', get_class($parent));
}

$linetype->parenttypes = $parenttypes;

return [
    'data' => $linetype,
];
