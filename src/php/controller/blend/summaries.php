<?php
$blend = Blend::load(BLEND_NAME);

return [
    'data' => $blend->summaries(AUTH_TOKEN, get_query_filters()),
];
