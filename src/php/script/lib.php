<?php

function get_query_filters()
{
    $filters = [];

    foreach (explode('&', $_SERVER['QUERY_STRING']) as $v) {
        $r = preg_split('/(\*=|>=|<=|~|=|<|>)/', urldecode($v), -1, PREG_SPLIT_DELIM_CAPTURE);

        if (count($r) == 3) {
            $filters[] = (object) [
                'field' => $r[0],
                'cmp' => $r[1],
                'value' => $r[2],
            ];
        }
    }

    return $filters;
}