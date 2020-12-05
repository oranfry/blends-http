<?php

define('AUTH_TOKEN', @getallheaders()['X-Auth']);

function init_app()
{
    if (
        (!preg_match(',^/auth/(login|logout)$,', $_SERVER['REQUEST_URI']) || $_SERVER['REQUEST_METHOD'] != 'POST')
        &&
        !Blends::verify_token(AUTH_TOKEN)
    ) {
        error_response('Unauthorised');
    }
}

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