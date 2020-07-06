<?php
$blend = Blend::load(BLEND_NAME);

return [
    'data' => $blend->print(AUTH_TOKEN, get_query_filters()),
];

