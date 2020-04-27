<?php
$blend = Blend::load(BLEND_NAME);

return [
    'data' => $blend->search(get_query_filters()),
];
