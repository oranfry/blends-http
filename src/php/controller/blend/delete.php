<?php
$blend = Blend::load(BLEND_NAME);

return [
    'data' => $blend->delete(AUTH_TOKEN, get_query_filters()),
];
