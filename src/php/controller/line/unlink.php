<?php
$linetype = Linetype::load(LINETYPE_NAME);

@list($line) = $linetype->find_lines([(object)['field' => 'id', 'cmp' => '=', 'value' => LINE_ID]]);

if (!$line) {
    return [
        'data' => (object) [],
    ];
}

return [
    'data' => $linetype->unlink($line, PARNT),
];
