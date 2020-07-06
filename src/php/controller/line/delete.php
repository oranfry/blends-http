<?php
$linetype = Linetype::load(LINETYPE_NAME);

return [
    'data' => $linetype->delete(AUTH_TOKEN, get_query_filters()),
];
