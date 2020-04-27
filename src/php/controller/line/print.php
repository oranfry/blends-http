<?php
$linetype = Linetype::load(LINETYPE_NAME);

kayoh_log('blends-http linetype print');

return [
    'data' => $linetype->print(get_query_filters()),
];
