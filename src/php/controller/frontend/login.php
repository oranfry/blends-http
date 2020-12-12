<?php
$message = null;

if (AUTH_TOKEN) {
    error_log('Unexpectedly, token is already present');
    die();
}

if (@$_COOKIE['token'] && Blends::verify_token($_COOKIE['token'])) {
    $blends = @BlendsConfig::get($_COOKIE['token'])->blends;

    if (!$blends || !count($blends)) {
        error_response('No blends set up');
    }

    $blend = array_keys($blends)[0];

    header("Location: /blend/{$blend}");
    die('Redirecting...');
}

return [];
