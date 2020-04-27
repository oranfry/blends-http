<?php
$linetype = Linetype::load(LINETYPE_NAME);

return [
    'data' => $linetype->delete(get_query_filters()),
];
