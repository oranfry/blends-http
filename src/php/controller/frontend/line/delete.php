<?php
define('LAYOUT', 'json');

$linetype = Linetype::load(AUTH_TOKEN, LINETYPE_NAME);

$result = $linetype->delete(AUTH_TOKEN, [(object) [
    'field' => 'id',
    'value' => LINE_ID,
]]);
