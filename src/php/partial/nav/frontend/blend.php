<?php use contextvariableset\Hidden; ?>
<?php use contextvariableset\Repeater; ?>
<?php use contextvariableset\Showas; ?>
<?php use contextvariableset\Filter; ?>
<?php if (defined('BLEND_NAME')): ?>
    <?php
        $query = implode('&', array_map(function($v, $k) { return "{$k}={$v}"; }, $_GET, array_keys($_GET)));
        $query = $query ? '?' . $query : '';
    ?>
    <div class="navset">
        <div class="nav-title">Blend</div>
        <div class="inline-rel">
            <div class="inline-modal listable">
                <div class="inline-dropdown">
                    <?php foreach ($blend_lookup as $name => $blend) : ?>
                        <a href="/blend/<?= $blend->name ?><?= $query ?>" <?= BLEND_NAME == $name ? 'class="current"' : '' ?>><?= $blend->name ?></a>
                    <?php endforeach ?>
                </div>
            </div>
            <span class="inline-modal-trigger"><?= BLEND_NAME ?></span>
        </div>
    </div>

    <?php $cvss = ContextVariableSet::getAll(); ?>
    <?php if (count($cvss)) : ?>
        <?php foreach ($cvss as $name => $cvs): ?>
            <?php if ($cvs instanceof Hidden || $cvs instanceof Repeater) : ?><?php continue; ?><?php endif ?>
            <?php $cvs->display(); ?>
        <?php endforeach ?>
    <?php endif ?>
<?php endif ?>
<?php $blend = $blend_lookup[BLEND_NAME]; ?>
<?php $repeater = ContextVariableSet::get('repeater'); ?>
<?php $adhocfilters = ContextVariableSet::get('adhocfilters'); ?>
<div class="navset">
    <div class="only-super1200 nav-title">Bulk</div>
    <i class="icon icon--gray icon--edit modal-trigger" data-for="bulk-edit-modal"></i>
    <i class="icon icon--gray icon--times trigger-bulk-delete-lines" data-blend="<?= BLEND_NAME ?>"></i>
    <?php if ($repeater->period && count($types) == 1): ?><i class="icon icon--gray icon--plus modal-trigger" data-for="bulk-add-modal_<?= $types[0] ?>"></i><?php endif ?>
    <?php if (@$blend->printable): ?><i class="icon icon--gray icon--printer trigger-bulk-print-lines" data-blend="<?= BLEND_NAME ?>"></i><?php endif ?>
    <?php if ($repeater->period && count($types) > 1): ?>
        <div class="inline-rel">
            <div class="inline-modal">
                <nav>
                    <?php foreach ($types as $_type): ?><a href="#"><i class="icon icon--gray icon--<?= Linetype::load(AUTH_TOKEN, $_type)->icon ?> modal-trigger" data-for="bulk-add-modal_<?= $_type ?>"></i></a><?php endforeach ?>
                </nav>
            </div>
            <i class="inline-modal-trigger icon icon--gray icon--plus"></i>
        </div>
    <?php endif ?>
</div>
<div class="navset">
    <div class="nav-title">Filters</div>
    <div class="inline-rel">
        <div class="inline-modal">
            <div id="filter-form" class="nav-dropdown nav-dropdown--spacey">
                <select>
                    <option></option>
                    <?php foreach ($fields as $field) : ?>
                        <option><?= $field->name ?></option>
                    <?php endforeach ?>
                </select>
                <div class="standard-filter-value">
                    <input type="text" style="width: 15em">
                </div>
                <div class="repeater-filter-value" style="display: none">
                </div>
                <button class="button" type="button">Add Filter</button>
                <div class="only-sub1200"><?php require search_plugins('src/php/partial/adhocfilterlist.php'); ?></div>
            </div>
        </div>
        <div class="inline-modal-trigger drnav <?= $adhocfilters->value ? 'current' : '' ?>"><i class="icon icon--gray icon--funnel"></i></div>
    </div>

    <?php $repeater->display(); ?>

    <div class="only-super1200">
        <?php require search_plugins('src/php/partial/adhocfilterlist.php'); ?>
        <?php if ($repeater->period): ?>
            <a class="filter cv-manip" data-manips="<?= $repeater->prefix ?>__period=">
                <i class="icon icon--repeat icon--gray icon--small"></i>
                <span><?= $repeater->render() ?></span>
                <i class="icon icon--times icon--gray icon--small filter__close-icon"></i>
            </a>
        <?php endif ?>
    </div>
</div>
