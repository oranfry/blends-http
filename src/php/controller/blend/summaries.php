<?php
$blend = Blend::load(BLEND_NAME);

return [
    'data' => $blend->summaries(get_query_filters()),
];
