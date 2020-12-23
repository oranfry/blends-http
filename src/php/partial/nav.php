<?php use contextvariableset\Hidden; ?>
<?php use contextvariableset\Repeater; ?>
<?php use contextvariableset\Showas; ?>
<?php if (defined('BLEND_NAME')): ?>
    <?php
        $query = implode('&', array_map(function($v, $k) { return "{$k}={$v}"; }, $_GET, array_keys($_GET)));
        $query = $query ? '?' . $query : '';
    ?>
    <div class="navset">
        <div class="nav-title">Blends</div>
        <div class="nav-modal listable">
            <div class="nav-dropdown">
                <?php foreach ($blend_lookup as $name => $blend) : ?>
                    <a href="/blend/<?= $blend->name ?><?= $query ?>" <?= BLEND_NAME == $name ? 'class="current"' : '' ?>><?= $blend->name ?></a>
                <?php endforeach ?>
            </div>
        </div>
        <span class="nav-modal-trigger only-sub1200"><?= BLEND_NAME ?></span>
    </div>

    <?php $cvss = ContextVariableSet::getAll(); ?>
    <?php if (count($cvss)) : ?>
        <?php foreach ($cvss as $name => $cvs): ?>
            <?php if ($cvs instanceof Hidden || $cvs instanceof Repeater) : ?><?php continue; ?><?php endif ?>
            <div class="nav-title"><?= $name ?></div>
            <?php $cvs->display(); ?>
        <?php endforeach ?>
    <?php endif ?>
<?php endif ?>
<?php @include search_plugins('src/php/partial/nav/' . PAGE . '.php'); ?>
<?php if (defined('ROOT_USERNAME') && AUTH_TOKEN && Blends::token_username(AUTH_TOKEN) == ROOT_USERNAME): ?>
    <form action="/switch-user" method="post" class="only-super1200">
        <div class="navset only-super1200">
            <div class="nav-title">Switch User</div>
            <input type="text" name="username" value="<?= ROOT_USERNAME ?>" style="width: 100%; padding: 0.5em">
        </div>
    </form>
<?php endif ?>
