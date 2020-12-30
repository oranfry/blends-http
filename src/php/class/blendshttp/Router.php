<?php
namespace blendshttp;

class Router extends \Router
{
    protected static $routes = [
        // login
        'GET /' => ['PAGE' => 'tools/login', 'AUTHSCHEME' => 'none'],

        // line
        'GET /([a-z]+)' => ['LINETYPE_NAME', 'LINE_ID' =>  null, 'PAGE' => 'frontend/line'],
        'GET /([a-z]+)/([A-Z0-9]+)' => ['LINETYPE_NAME', 'LINE_ID', 'PAGE' => 'frontend/line'],

        // blend
        'GET /blend/([a-z]+)' => ['BLEND_NAME', 'PAGE' => 'frontend/blend'],

        // special
        'POST /switch-user' => ['PAGE' => 'frontend/switch-user'],
   ];
}
