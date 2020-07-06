<?php
$linetype = Linetype::load(LINETYPE_NAME);
$lines = json_decode(file_get_contents('php://input'));
$keep_filedata = @$_GET['keepfiledata'] === '1';

return [
    'data' => $linetype->save(AUTH_TOKEN, $lines, 0, null, $keep_filedata),
];
