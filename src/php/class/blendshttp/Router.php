<?php
namespace blendshttp;

class Router extends \Router
{
    protected static $routes = [
        'DELETE /([a-z]+)' => ['LINETYPE_NAME', 'PAGE' => 'line/delete'],
        'DELETE /blend/([a-z]+)/delete' => ['BLEND_NAME', 'PAGE' => 'blend/delete'],
        'GET /([a-z]+)/([A-Z0-9]+)' => ['LINETYPE_NAME', 'LINE_ID', 'PAGE' => 'line/index'],
        'GET /([a-z]+)/([A-Z0-9]+)/html' => ['LINETYPE_NAME', 'LINE_ID', 'PAGE' => 'line/html'],
        'GET /([a-z]+)/([A-Z0-9]+)/pdf' => ['LINETYPE_NAME', 'LINE_ID', 'PAGE' => 'line/pdf'],
        'GET /([a-z]+)/info' => ['LINETYPE_NAME', 'PAGE' => 'line/info'],
        'GET /([a-z]+)/suggested' => ['LINETYPE_NAME', 'PAGE' => 'line/suggested'],
        'GET /blend/([a-z]+)/info' => ['BLEND_NAME', 'PAGE' => 'blend/info'],
        'GET /blend/([a-z]+)/print' => ['BLEND_NAME', 'PAGE' => 'blend/print'],
        'GET /blend/([a-z]+)/search' => ['BLEND_NAME', 'PAGE' => 'blend/index'],
        'GET /blend/([a-z]+)/summary' => ['BLEND_NAME', 'PAGE' => 'blend/summary'],
        'GET /blend/([a-z]+)/update' => ['BLEND_NAME', 'PAGE' => 'blend/update'],
        'GET /blend/list' => ['BLEND_NAME', 'PAGE' => 'blend/list'],
        'GET /download/(.*)' => ['FILE', 'PAGE' => 'download'],
        'GET /file/(.*)' => ['FILE', 'PAGE' => 'file'],
        'GET /tablelink/([a-z]+)/info' => ['TABLELINK_NAME', 'PAGE' => 'tablelink/info'],
        'GET /touch' => ['PAGE' => 'touch'],
        'POST /auth/login' => ['PAGE' => 'login', 'NOAUTH' => true],
        'POST /auth/logout' => ['PAGE' => 'logout'],
        'POST /([a-z]+)' => ['LINETYPE_NAME', 'PAGE' => 'line/save'],
        'POST /([a-z]+)/([A-Z0-9]+)/unlink/([a-z]+_[a-z]+)' => ['LINETYPE_NAME', 'LINE_ID', 'PARNT', 'PAGE' => 'line/unlink'],
        'POST /([a-z]+)/print' => ['LINETYPE_NAME', 'PAGE' => 'line/print'],
    ];
}
