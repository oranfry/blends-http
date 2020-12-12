<?php use contextvariableset\Daterange; ?>
<!DOCTYPE html>
<html lang="en-NZ">
    <head>
        <meta name="viewport" content="width=320, initial-scale=1, user-scalable=no">
        <link rel="stylesheet" type="text/css" href="/css/styles.<?= latest('css') ?>.css">
        <meta charset="utf-8"/>
        <title><?= BlendsConfig::get(AUTH_TOKEN)->instance_name ?: 'Blends' ?></title>
        <style>
            .appcolor-bg,
            .button.button--main,
            nav a.current,
            td.today,
            tr.today td,
            .periodchoice.periodchoice--current,
            .nav-dropdown a.current,
            .drnav.current,
            .cv-manip.current {
                background-color: #<?= HIGHLIGHT ?>;
            }

            .button.button--main {
                border: 1px solid #<?= adjustBrightness(HIGHLIGHT, -60) ?>
            }
        </style>
</head>
<body class="wsidebar">
    <?php require APP_HOME . '/src/php/partial/nav.php'; ?>
    <div class="wrapper">
        <?php if (@$GLOBALS['title']): ?>
            <h3><?= $GLOBALS['title'] ?></h3>
        <?php endif ?>
        <?php require APP_HOME . '/src/php/partial/content/' . PAGE . '.php'; ?>
    </div>

    <?php
        foreach (ContextVariableSet::getAll() as $active) {
            $active->enddisplay();
        }
    ?>

    <?php if (AUTH_TOKEN): ?>
        <?php $daterange = new Daterange('daterange'); ?>
        <?php $username = Blends::token_username(AUTH_TOKEN); ?>
        <?php $user = Blends::token_user(AUTH_TOKEN); ?>

        <script>
            window.orig_token = '<?= AUTH_TOKEN ?>';
            window.repeater = <?= $repeater->period ? "'" . $repeater->render() . "'" : 'null' ?>;
            window.range_from = <?= $daterange->from ? "'" . $daterange->from . "'" : 'null' ?>;
            window.range_to = <?= $daterange->to ? "'" . $daterange->to . "'" : 'null' ?>;
            window.username = '<?= $username; ?>';
            window.user = <?= $user ? "'{$user}'" : 'null'; ?>;
            <?php foreach (PAGE_PARAMS as $key => $value): ?>
                window.<?= "{$key} = '{$value}'"; ?>;
            <?php endforeach ?>
            <?php if (@BLEND_NAME): ?>
                window.current_filter = '<?= implode('&', array_map(function($f){
                    return $f->field . $f->cmp . (is_array($f->value) ? implode(',' ,$f->value) : $f->value);
                }, get_current_filters(Blend::load(AUTH_TOKEN, BLEND_NAME)->fields))); ?>';
            <?php endif ?>
            <?php if (BACK): ?><?= "var back = '" . BACK . "'"; ?>;<?php endif ?>
        </script>
    <?php endif ?>

    <script type="text/javascript" src="/js/app.<?= latest('js') ?>.js"></script>
    <?php @include APP_HOME . '/src/php/partial/js/' . PAGE . '.php'; ?>
</body>
</html>
