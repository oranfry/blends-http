<?php use contextvariableset\Showas; ?>
<?php $blend = $blend_lookup[BLEND_NAME]; ?>
<?php require search_plugins('src/php/partial/showas/' . SHOWAS . '.php'); ?>

<div class="modal" id="bulk-edit-modal" style="background-color: #eee">
    <form method="post" class="bulk-edit-form">
        <?php foreach ($fields as $field): ?>
            <?php $options = @$suggested_values[$field->name]; ?>
            <?php $value = @$generic->{$field->name}; ?>
            <?php $checked = property_exists($generic, $field->name); ?>
            <?php $field_inc = search_plugins("src/php/partial/fieldtype/{$field->type}.php"); ?>

            <?php if (!file_exists($field_inc)) : ?>
                <?php continue; ?>
            <?php endif ?>

            <div class="form-row">
                <div class="form-row__label"><?= $field->name ?></div>
                <div class="form-row__value">
                    <?php if ($field->type != 'file'): ?>
                        <div style="position: absolute; left: 0;">
                            <input type="checkbox" <?= $checked ? 'checked="checked"' : '' ?> name="apply_<?= $field->name ?>">
                        </div>
                    <?php endif ?>
                    <?php $bulk = true; require $field_inc; unset($bulk); ?>
                </div>
                <div style="clear: both"></div>
            </div>
        <?php endforeach ?>

        <div class="form-row">
            <div class="form-row__label">&nbsp;</div>
            <div class="form-row__value">
                <input class="button" name="action" value="update" type="submit">
                <div class="button close-modal">cancel</div>
            </div>
            <div style="clear: both"></div>
        </div>
    </form>

    <pre id="output"></pre>
</div>

<?php foreach ($linetypes as $linetype) : ?>
    <?php if (!in_array($linetype->name, $types)) :?>
        <?php continue; ?>
    <?php endif ?>

    <div class="modal" id="bulk-add-modal_<?= $linetype->name ?>">
        <form method="post" class="bulk-add-form" data-linetype="<?= $linetype->name ?>" data-blend="<?= BLEND_NAME ?>">
            <?php foreach ($linetype->fields as $field) : ?>
                <?php if ($field->type == 'date') : ?>
                    <?php continue; ?>
                <?php endif ?>

                <?php $options = @$suggested_values[$field->name]; ?>
                <?php $value = @$generic->{$field->name}; ?>
                <?php $checked = property_exists($generic, $field->name); ?>
                <?php $field_inc = search_plugins("src/php/partial/fieldtype/{$field->type}.php"); ?>

                <?php if (!file_exists($field_inc)) : ?>
                    <?php continue; ?>
                <?php endif ?>

                <div class="form-row">
                    <div class="form-row__label"><?= $field->name ?></div>
                    <div class="form-row__value">
                        <?php require $field_inc; ?>
                    </div>
                    <div style="clear: both"></div>
                </div>
            <?php endforeach ?>

            <div class="form-row">
                <div class="form-row__label">&nbsp;</div>
                <div class="form-row__value">
                    <input class="button" name="action" value="add" type="submit">
                    <div class="button close-modal">cancel</div>
                </div>
                <div style="clear: both"></div>
            </div>
        </form>

        <pre id="output"></pre>
    </div>
<?php endforeach ?>
