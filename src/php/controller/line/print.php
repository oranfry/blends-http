<?php
$linetype = Linetype::load(LINETYPE_NAME);

return [
    'data' => $linetype->print(get_query_filters()),
];
