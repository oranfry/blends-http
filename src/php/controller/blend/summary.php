<?php
$blend = Blend::load(BLEND_NAME);

return [
    'data' => $blend->summary(AUTH_TOKEN, get_query_filters()),
];
