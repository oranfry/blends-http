<?php
$blend = Blend::load(BLEND_NAME);

return [
    'data' => $blend->delete(get_query_filters()),
];
