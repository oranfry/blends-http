<?php
$linetype = Linetype::load(LINETYPE_NAME);
$lines = json_decode(file_get_contents('php://input'));

return [
    'data' => $linetype->unlink($lines),
];
