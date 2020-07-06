<?php
$linetype = Linetype::load(LINETYPE_NAME);

return [
    'data' => $linetype->print(AUTH_TOKEN, get_query_filters()),
];
