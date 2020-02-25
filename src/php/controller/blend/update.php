<?php

return [
    'data' => Blend::update(BLEND_NAME, get_query_filters(), json_decode(file_get_contents('php://input'))),
];
