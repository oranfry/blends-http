<?php for ($j = 0; $j < 7; $j++) : ?>
    <?php $date = date_shift($from, '+' . ($i + $j - $backtrack) . ' day'); ?>
    <?php if (strcmp($date, $from) < 0 || strcmp($date, $to) > 0): ?>
        <div class="cell eventcell void">&nbsp;</div>
        <?php continue; ?>
    <?php endif ?>
    <div class="cell eventcell">
        <?php for (; $c < count($records); $c++): ?>
            <?php unset($record); ?>
            <?php $record = $records[$c]; ?>
            <?php if ($record->date != $date): ?>
                <?php break; ?>
            <?php endif ?>
            <a class="col" href="<?= editlink($record->id, $record->type) ?>" style="white-space: nowrap;" title="<?php record_title($record, $fields) ?>"><?php $iconfield = @filter_objects($fields, 'type', 'is', 'icon')[0]; ?>
                <?php $firstfield = @filter_objects($fields, 'type', 'notin', ['date', 'icon'])[0]; ?>
                <?php if ($iconfield): ?><i class="icon icon--gray icon--<?= $record->{$iconfield->name} ?>" alt=""></i><?php endif ?>
                <?php if ($firstfield): ?><span><?= $record->{$firstfield->name} ?></span><?php endif ?></a>
        <?php endfor ?>
    </div>
<?php endfor ?>
