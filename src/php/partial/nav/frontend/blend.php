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

    <div class="inline-rel">
        <div class="inline-modal repeater-modal">
            <div class="nav-dropdown--spacey" style="white-space: nowrap; width: 17em;">
                <div class="form-row">
                    <div class="form-row__label">Repeater</div>
                    <div class="form-row__value">
                        <select class="repeater-select cv-surrogate no-autosubmit" data-for="<?= $repeater->prefix ?>__period">
                            <option></option>
                            <?php foreach (['day', 'month', 'year'] as $period): ?>
                                <option <?= ($period == $repeater->period) ? 'selected="selected"' : '' ?> value="<?= $period ?>"><?= $period ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                    <div style="clear: both"></div>
                </div>

                <div class="form-row" data-repeaters="day">
                    <div class="form-row__label">n</div>
                    <div class="form-row__value">
                        <input class="cv-surrogate no-autosubmit" data-for="<?= $repeater->prefix ?>__n" type="number" step="1" min="1" value="<?= $repeater->n ?>" style="width: 4em">
                    </div>
                    <div style="clear: both"></div>
                </div>

                <div class="form-row" data-repeaters="day">
                    <div class="form-row__label">Peg Date</div>
                    <div class="form-row__value">
                        <input class="cv-surrogate no-autosubmit" data-for="<?= $repeater->prefix ?>__pegdate" type="text" value="<?= $repeater->pegdate ?>" style="width: 7em"><span class="button fromtoday">&bull;</span>
                    </div>
                    <div style="clear: both"></div>
                </div>

                <div class="form-row" data-repeaters="month year">
                    <div class="form-row__label">Day</div>
                    <div class="form-row__value">
                        <input class="cv-surrogate no-autosubmit" data-for="<?= $repeater->prefix ?>__day" type="text" value="<?= $repeater->day ?>" style="width: 7em">
                    </div>
                    <div style="clear: both"></div>
                </div>

                <div class="form-row" data-repeaters="year">
                    <div class="form-row__label">Month</div>
                    <div class="form-row__value">
                        <input class="cv-surrogate no-autosubmit" data-for="<?= $repeater->prefix ?>__month" type="text" value="<?= $repeater->month ?>" style="width: 7em">
                    </div>
                    <div style="clear: both"></div>
                </div>

                <div class="form-row" data-repeaters="day month year">
                    <div class="form-row__label">F/F</div>
                    <div class="form-row__value">
                         <select class="cv-surrogate no-autosubmit" data-for="<?= $repeater->prefix ?>__ff">
                            <option></option>
                            <?php foreach (['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $i => $ff): ?>
                                <option <?= ($i + 1 == $repeater->ff) ? 'selected="selected"' : '' ?> value="<?= $i + 1?>"><?= $ff ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                    <div style="clear: both"></div>
                </div>

                <div class="form-row" data-repeaters="month year">
                    <div class="form-row__label">Offset</div>
                    <div class="form-row__value">
                        <input class="cv-surrogate no-autosubmit" data-for="<?= $repeater->prefix ?>__offset" type="text" value="<?= $repeater->offset ?>" style="width: 7em">
                    </div>
                    <div style="clear: both"></div>
                </div>

                <div class="form-row" data-repeaters="month year">
                    <div class="form-row__label">Round</div>
                    <div class="form-row__value">
                         <select class="cv-surrogate no-autosubmit" data-for="<?= $repeater->prefix ?>__round">
                            <option></option>
                            <option <?= $repeater->round == 'Yes' ? 'selected': '' ?>>Yes</option>
                        </select>
                    </div>
                    <div style="clear: both"></div>
                </div>

                <div class="form-row">
                    <div class="form-row__label">&nbsp;</div>
                    <div class="form-row__value">
                        <a class="button cv-manip" data-manips="<?= $repeater->prefix ?>__period=">Clear</a>
                        <a class="button cv-manip" data-manips="">Apply</a>
                    </div>
                    <div style="clear: both"></div>
                </div>
            </div>
        </div>
        <?php if (@$datefield): ?>
            <div class="inline-modal-trigger drnav <?= $repeater->period ? 'current' : '' ?>"><i class="icon icon--gray icon--repeat"></i></div>
        <?php endif ?>
    </div>
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
