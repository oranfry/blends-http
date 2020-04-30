<?php
define('LAYOUT', 'pdf');

$linetype = Linetype::load(LINETYPE_NAME);
$line = @$linetype->find_lines([(object)['field' => 'id', 'value' => LINE_ID]])[0];

if (!$line) {
    error_response('No such line', 400);
}

$linetype->load_children($line);
$filedata = $linetype->aspdf($line);

return [
    'filedata' => $filedata,
    'filename' => LINE_ID . '.pdf',
];

