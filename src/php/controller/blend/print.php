<?php
$blend = Blend::load(BLEND_NAME);

return [
    'data' => $blend->print(get_query_filters()),
];

