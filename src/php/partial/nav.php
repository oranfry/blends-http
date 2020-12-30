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
            <?php $cvs->display(); ?>
        <?php endforeach ?>
    <?php endif ?>
<?php endif ?>
