<?php
$blend = Blend::load(BLEND_NAME);
$data = json_decode(file_get_contents('php://input'));

return [
    'data' => $blend->update(get_query_filters(), $data),
];
