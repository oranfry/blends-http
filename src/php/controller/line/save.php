<?php

return [
    'data' => Linetype::save(LINETYPE_NAME, json_decode(file_get_contents('php://input')), LINE_ID),
];
