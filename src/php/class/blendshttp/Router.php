<?php
namespace blendshttp;

class Router extends \Router
{
    protected static $routes = [
        /***************************************
         *                AUTH                 *
         ***************************************/

        // login
        'GET /' => ['PAGE' => 'frontend/login', 'AUTHSCHEME' => 'none'],
        'POST /' => ['PAGE' => 'frontend/login', 'AUTHSCHEME' => 'none'],
        'POST /api/auth/login' => ['PAGE' => 'api/login', 'AUTHSCHEME' => 'none', 'LAYOUT' => 'json'],

        // logout
        'POST /api/auth/logout' => ['PAGE' => 'api/logout', 'AUTHSCHEME' => 'header', 'LAYOUT' => 'json'],

        /***************************************
         *                LINE                 *
         ***************************************/

        // save
        'POST /api/([a-z]+)' => ['LINETYPE_NAME', 'PAGE' => 'api/line/save', 'LAYOUT' => 'json', 'AUTHSCHEME' => 'header'],
        'POST /api/([a-z]+)/add' => ['LINETYPE_NAME', 'PAGE' => 'api/line/add', 'LAYOUT' => 'json', 'AUTHSCHEME' => 'header'],
        'CLI save \S+ \S+ \S+' => ['PAGE' => 'cli/save', 'USERNAME', 'PASSWORD', 'LINETYPE'],

        // read
        'GET /([a-z]+)/([A-Z0-9]+)' => ['LINETYPE_NAME', 'LINE_ID', 'PAGE' => 'frontend/line'],
        'GET /api/([a-z]+)/([A-Z0-9]+)' => ['LINETYPE_NAME', 'LINE_ID', 'PAGE' => 'api/line/index', 'LAYOUT' => 'json', 'AUTHSCHEME' => 'header'],
        'GET /api/([a-z]+)/([A-Z0-9]+)/html' => ['LINETYPE_NAME', 'LINE_ID', 'PAGE' => 'api/line/html', 'AUTHSCHEME' => 'header'],
        'GET /api/([a-z]+)/([A-Z0-9]+)/pdf' => ['LINETYPE_NAME', 'LINE_ID', 'PAGE' => 'api/line/pdf', 'AUTHSCHEME' => 'header'],

        // delete
        'DELETE /api/([a-z]+)' => ['LINETYPE_NAME', 'PAGE' => 'api/line/delete', 'LAYOUT' => 'json', 'AUTHSCHEME' => 'header'],

        // unlink
        'POST /api/([a-z]+)/([A-Z0-9]+)/unlink/([a-z]+_[a-z]+)' => ['LINETYPE_NAME', 'LINE_ID', 'PARNT', 'PAGE' => 'api/line/unlink', 'LAYOUT' => 'json', 'AUTHSCHEME' => 'header'],

        // meta
        'GET /api/([a-z]+)/info' => ['LINETYPE_NAME', 'PAGE' => 'api/line/info', 'LAYOUT' => 'json', 'AUTHSCHEME' => 'header'],
        'GET /api/([a-z]+)/suggested' => ['LINETYPE_NAME', 'PAGE' => 'api/line/suggested', 'LAYOUT' => 'json', 'AUTHSCHEME' => 'header'],

        // print
        'POST /([a-z]+)/([A-Z0-9]+)/print' => ['LINETYPE_NAME', 'LINE_ID', 'PAGE' => 'frontend/line/print'],
        'POST /api/([a-z]+)/print' => ['LINETYPE_NAME', 'PAGE' => 'api/line/print', 'LAYOUT' => 'json', 'AUTHSCHEME' => 'header'],

        /***************************************
         *                BLEND                *
         ***************************************/

        // read
        'GET /blend/([a-z]+)' => ['BLEND_NAME', 'PAGE' => 'frontend/blend'],
        'GET /api/blend/([a-z]+)/search' => ['BLEND_NAME', 'PAGE' => 'api/blend/index', 'LAYOUT' => 'json', 'AUTHSCHEME' => 'header'],
        'GET /api/blend/list' => ['PAGE' => 'api/blend/list', 'LAYOUT' => 'json', 'AUTHSCHEME' => 'header'],
        'GET /api/blend/([a-z]+)/summary' => ['BLEND_NAME', 'PAGE' => 'api/blend/summary', 'LAYOUT' => 'json', 'AUTHSCHEME' => 'header'],

        // update
        'POST /ajax/blend/([a-z]+)/update' => ['BLEND_NAME', 'PAGE' => 'frontend/blend/update'],
        'GET /api/blend/([a-z]+)/update' => ['BLEND_NAME', 'PAGE' => 'api/blend/update', 'LAYOUT' => 'json', 'AUTHSCHEME' => 'header'],

        // delete
        'POST /ajax/blend/([a-z]+)/delete' => ['BLEND_NAME', 'PAGE' => 'frontend/blend/delete'],
        'DELETE /api/blend/([a-z]+)/delete' => ['BLEND_NAME', 'PAGE' => 'api/blend/delete', 'LAYOUT' => 'json', 'AUTHSCHEME' => 'header'],

        // meta
        'GET /api/blend/([a-z]+)/info' => ['BLEND_NAME', 'PAGE' => 'api/blend/info', 'LAYOUT' => 'json', 'AUTHSCHEME' => 'header'],

        // print
        'POST /ajax/blend/([a-z]+)/print' => ['BLEND_NAME', 'PAGE' => 'frontend/blend/print'],
        'GET /api/blend/([a-z]+)/print' => ['BLEND_NAME', 'PAGE' => 'api/blend/print', 'LAYOUT' => 'json', 'AUTHSCHEME' => 'header'],

        /***************************************
         *               FILES                 *
         ***************************************/

        'GET /api/download/(.*)' => ['FILE', 'PAGE' => 'api/download'],
        'GET /api/file/(.*)' => ['FILE', 'PAGE' => 'api/file', 'LAYOUT' => 'json', 'AUTHSCHEME' => 'header'],
        'GET /download/(.*)' => ['FILE', 'PAGE' => 'frontend/download'],

        /***************************************
         *              FRONTEND               *
         ***************************************/

        'GET /([a-z]+)' => ['LINETYPE_NAME', 'LINE_ID' =>  null, 'PAGE' => 'frontend/line'],
        'POST /change-token' => ['PAGE' => 'frontend/change-token', 'AUTHSCHEME' => 'none'],
        'POST /switch-user' => ['PAGE' => 'frontend/switch-user'],

        /***************************************
         *              API ONLY               *
         ***************************************/

        'GET /api/touch' => ['PAGE' => 'api/touch', 'LAYOUT' => 'json', 'AUTHSCHEME' => 'header'],

        /***************************************
         *              CLI ONLY               *
         ***************************************/

        'CLI collisions \S+ \S+' =>     [null, 'MAX', 'TABLE', 'PAGE' => 'cli/collisions', 'LAYOUT' => 'cli', 'AUTHSCHEME' => 'none'],
        'CLI collisions \S+' =>         [null, 'MAX', 'TABLE' => null, 'PAGE' => 'cli/collisions', 'LAYOUT' => 'cli', 'AUTHSCHEME' => 'none'],
        'CLI export \S+ \S+' =>         [null, 'USERNAME', 'PASSWORD', 'PAGE' => 'cli/export', 'LAYOUT' => 'cli', 'AUTHSCHEME' => 'onetime'],
        'CLI import \S+ \S+' =>         [null, 'USERNAME', 'PASSWORD', 'PAGE' => 'cli/import', 'LAYOUT' => 'cli', 'AUTHSCHEME' => 'onetime'],
        'CLI expunge-tokens \S+ \S+' => [null, 'USERNAME', 'PASSWORD', 'PAGE' => 'cli/expunge-tokens', 'LAYOUT' => 'cli', 'AUTHSCHEME' => 'onetime'],
        'CLI reset-schema \S+ \S+' =>   [null, 'USERNAME', 'PASSWORD', 'PAGE' => 'cli/reset-schema', 'LAYOUT' => 'cli', 'AUTHSCHEME' => 'onetime'],
        'CLI h2n \S+ \S+' =>            [null, 'TABLE', 'H', 'PAGE' => 'cli/h2n', 'AUTHSCHEME' => 'none', 'LAYOUT' => 'cli'],
        'CLI n2h \S+ \S+' =>            [null, 'TABLE', 'N', 'PAGE' => 'cli/n2h', 'AUTHSCHEME' => 'none', 'LAYOUT' => 'cli'],
   ];
}
