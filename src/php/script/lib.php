<?php
use contextvariableset\Daterange;
use contextvariableset\Hidden;
use contextvariableset\Repeater;
use contextvariableset\Value;
use contextvariableset\Filter;

if (!preg_match('/^[a-z]*$/', @$_GET['context'])) {
    error_response("Invalid context");
}

define('MAX_COLUMN_WIDTH', 25);

function get_current_filters($fields)
{
    $filters = [];

    $daterange = new Daterange('daterange');
    $repeater = new Repeater(BLEND_NAME . "_repeater");
    $datefield = null;

    foreach ($fields as $field) {
        if (!@$field->main) {
            continue;
        }

        if ($field->type == 'date') {
            $datefield = $field;

            if ($daterange->from) {
                $filters[] = (object)[
                    'field' => 'date',
                    'value' => $daterange->from,
                    'cmp' => '>=',
                ];
            }

            if ($daterange->to) {
                $filters[] = (object)[
                    'field' => 'date',
                    'value' => $daterange->to,
                    'cmp' => '<=',
                ];
            }
        } else {
            $csv = new Value(BLEND_NAME . "_{$field->name}");
            if ($csv->value) {
                $filters[] = (object) [
                    'field' => $field->name,
                    'value' => $csv->value,
                    'cmp' => '=',
                ];
            }
        }
    }

    if ($datefield && $repeater->period) {
        $filters = array_merge($filters, get_repeater_filters($repeater, $datefield->name));
    }

    $filters = array_merge($filters, get_adhoc_filters());

    return $filters;
}

function get_past_filters($fields)
{
    $filters = [];
    $daterange = new Daterange('daterange');
    $repeater = new Repeater(BLEND_NAME . "_repeater");
    $datefield = null;

    foreach ($fields as $field) {
        if ($field->type == 'date') {
            $datefield = $field;

            if ($daterange->from) {
                $filters[] = (object)[
                    'field' => 'date',
                    'value' => $daterange->from,
                    'cmp' => '<',
                ];
            }
        } else {
            $csv = new Value(BLEND_NAME . "_{$field->name}");
            if ($csv->value) {
                $filters[] = (object) [
                    'field' => $field->name,
                    'value' => $csv->value,
                    'cmp' => '=',
                ];
            }
        }
    }

    if ($datefield && $repeater->period) {
        $filters = array_merge($filters, get_repeater_filters($repeater, $datefield->name));
    }

    return array_merge($filters, get_adhoc_filters());
}

function get_adhoc_filters()
{
    $filters = [];
    $byfield = [];
    $adhocfilters = ContextVariableSet::get("adhocfilters");

    if ($adhocfilters->value) {
        foreach (explode(',', $adhocfilters->value) as $filterid) {
            $filter = ContextVariableSet::get($filterid);
            $field = $filter->field;

            if (!@$byfield[$field]) {
                $byfield[$field] = [];
            }

            $byfield[$field][] = $filter;
        }
    }

    foreach ($byfield as $field => $_filters) {
        $bycmp = [];

        foreach ($_filters as $filter) {
            $cmp = $filter->cmp;

            if (!@$bycmp[$cmp]) {
                $bycmp[$cmp] = [];
            }

            $bycmp[$cmp][] = $filter;
        }

        if (count(@$bycmp['='] ?: []) > 1) {
            $filters[] = (object) [
                'field' => $field,
                'value' => array_map(function($e){ return $e->value; }, $bycmp['=']),
                'cmp' => '=',
            ];

            unset($bycmp['=']);
        }

        foreach ($bycmp as $cmp => $_filters) {
            foreach ($_filters as $filter) {
                $filters[] = (object) [
                    'field' => $filter->field,
                    'value' => $filter->value,
                    'cmp' => $filter->cmp,
                ];
            }
        }
    }

    return $filters;
}

function get_repeater_filters($repeater, $datefield_name)
{
     return [(object) [
        'cmp' => '*=',
        'field' => $datefield_name,
        'value' => $repeater->render(),
    ]];
}

function computed_field_value($record, $expression)
{
    extract((array)$record);

    return eval("return {$expression};");
}

function apply_filters()
{
    $adhocfilters = new Hidden(BLEND_NAME . "_filters");
    ContextVariableSet::put('adhocfilters', $adhocfilters);

    if ($adhocfilters->value) {
        foreach (explode(',', $adhocfilters->value) as $filterid) {
            ContextVariableSet::put($filterid, new Filter($filterid));
        }
    }

    $repeater = new Repeater(BLEND_NAME . "_repeater");
    ContextVariableSet::put('repeater', $repeater);
}

function addlink($type, $group, $groupfield, $defaultgroup, $parent_query, $prepop = [])
{
    $url = "/{$type}";

    $query = $prepop;

    if ($groupfield) {
        $query[$groupfield] = $group ?: @$defaultgroup;
    }

    $query['back'] = base64_encode($_SERVER['REQUEST_URI']);

    $url .= '?' . http_build_query($query) . (@$parent_query ? '&' . $parent_query : '');

    return $url;
}

function editlink($id, $type)
{
    $back = base64_encode($_SERVER['REQUEST_URI']);

    return "/{$type}/{$id}?back={$back}";
}

function get_repeater_dates($repeater, $from, $to)
{
    $period = $repeater->period;

    if ($period == 'day') {
        $n = $repeater->n;
        $pegdate = $repeater->pegdate;
        $fastforward = $repeater->ff;
        $offset = '';
    } elseif ($period == 'month') {
        $day = $repeater->day;
        $round = $repeater->round;
        $fastforward = $repeater->ff;
        $offset = $repeater->offset;
    } elseif ($period == 'year') {
        $month = $repeater->month;
        $day = $repeater->day;
        $round = $repeater->round;
        $fastforward = $repeater->ff;
        $offset = $repeater->offset;
    } else {
        error_response("Invalid period");
    }

    if ($offset) {
        if (!preg_match('/^([+-][1-9][0-9]*) (day|month|year)$/', $offset, $groups)) {
            error_response('Invalid offset');
        }

        $offsetMagnitude = intval(preg_replace('/[+-]/', '', $groups[1]));
        $offsetSign = preg_match('/-/', $groups[1]) ? '-' : '+';
        $offsetSignNegated = $offsetSign == '-' ? '+' : '-';
        $offsetPeriod = $groups[2];
    }

    $start = $from;
    $end = $to;

    if ($offset && $offsetMagnitude) {
        $start = date_shift($start, "{$offsetSignNegated}{$offsetMagnitude} {$offsetPeriod}");
        $end = date_shift($end, "{$offsetSignNegated}{$offsetMagnitude} {$offsetPeriod}");
    }

    if ($fastforward) {
        $start = date_shift($start, "-6 day");
        $end = date_shift($end, "-6 day");
    }

    $dates = [];

    for ($d = $start; $d <= $end; $d = date_shift($d, '+1 day')) {
        if ($period == 'day') {
            $a = strtotime("{$d} 00:00:00 +0000") / 86400;
            $b = strtotime("{$pegdate} 00:00:00 +0000") / 86400;

            if (($a - $b) % $n == 0) {
                $dates[] = $d;
            }
        } elseif (
            preg_replace('/.*-/', '', $d) == ($round ? min($day, date('t', strtotime($d))) : $day) &&
            ($period != 'year' || preg_replace('/.*-(.*)-.*/', '$1', $d) == $month)
        ) {
            $dates[] = $d;
        }
    }

    // fastforward and offset

    for ($i = 0; $i < count($dates); $i++) {
        if ($fastforward) {
            while (date('w', strtotime($dates[$i])) != $fastforward - 1) {
                $dates[$i] = date_shift($dates[$i], "+1 day");
            }
        }

        if ($offset && $offsetMagnitude) {
            $dates[$i] = date_shift($dates[$i], "{$offsetSign}{$offsetMagnitude} {$offsetPeriod}");
        }
    }

    return $dates;
}

function doover()
{
    setcookie('token', '', time() - 3600);
    header('Location: /');
    die();
}

function get_query_filters()
{
    $filters = [];

    foreach (explode('&', $_SERVER['QUERY_STRING']) as $v) {
        $r = preg_split('/(\*=|>=|<=|~|=|<|>)/', urldecode($v), -1, PREG_SPLIT_DELIM_CAPTURE);

        if (count($r) == 3) {
            $values = explode(',', $r[2]);
            $filters[] = (object) [
                'field' => $r[0],
                'cmp' => $r[1],
                'value' => count($values) > 1 ? $values : reset($values),
            ];
        }
    }

    return $filters;
}

function build_table_definitions($token)
{
    $schemata = [];
    $definitions = [
        'record' => [
            'sequence_pointer' => "create table `sequence_pointer` (`table` varchar(255) not null, `pointer` int default '1', primary key (`table`)) engine=innodb default charset=latin1",
            'master_record_lock' => "create table `master_record_lock` (`counter` int default null) engine=innodb default charset=latin1",
        ],
        'tablelink' => [],
    ];

    $defs = [
        'text' => "varchar(255) null",
        'numberdp' => 'decimal (32, 16) null',
        'number' => 'integer null',
        'multiline' => "text",
        'date' => "date",
        'timestamp' => "timestamp",
    ];

    $examples = [
        'text' => "'hello'",
        'number' => '1.23',
        'multiline' => "'multiline\ntext'",
        'date' => "'2020-01-01'",
        'timestamp' => "'2020-01-01 01:02:03'",
    ];

    $tablelinks = [];

    foreach (array_keys(BlendsConfig::get($token)->linetypes) as $linetypeName) {
        $linetype = Linetype::load($token, $linetypeName);
        $table = $linetype->table;
        $db_table = BlendsConfig::get($token)->tables[$table];

        if (!isset($schemata[$db_table])) {
            $schemata[$db_table] = [
                'id' => (object) ['def' => 'char(10) not null', 'primary' => true],
                'user' => (object) ['def' => 'varchar(255) default null'],
                'created' => (object) ['def' => 'timestamp not null default current_timestamp'],
            ];
        }

        foreach ($linetype->unfuse_fields as $field => $details) {
            if (!is_object($details) || !property_exists($details, 'type')) {
                error_response("Unfuse field {$linetype->name}.{$field} not in the right format");
            }

           $field_full = str_replace('{t}.', '', $field);

            if (isset($schemata[$db_table][$field_full]) && $schemata[$db_table][$field_full]->def != $details->type) {
                error_response('Inconsistent types for field ' . $db_table . '.' . $field . ': ' . $schemata[$db_table][$field_full]->def . ' vs.' . $details->type);
            }

            $schemata[$db_table][$field_full] = (object) ['def' => $details->type];
        }

        foreach (@$linetype->children as $child) {
            $tablelinks[$child->parent_link] = true;
        }

        foreach (@$linetype->inlinelinks ?: [] as $child) {
            $tablelinks[$child->tablelink] = true;
        }
    }

    foreach ($schemata as $table => $fields) {
        $definitions['record'][$table] = "create table `{$table}` (" . implode(", ", array_map(function($def, $fieldName){
            return "`{$fieldName}` {$def->def}";
        }, $fields, array_keys($fields))) . ", primary key (`id`)) engine=InnoDB default charset=latin1";
    }

    foreach (array_keys($tablelinks) as $tablelinkname) {
        $tablelink = Tablelink::load($tablelinkname);
        $dbtables = [
            BlendsConfig::get($token)->tables[$tablelink->tables[0]],
            BlendsConfig::get($token)->tables[$tablelink->tables[1]],
        ];

        $unique = implode(", ", array_filter([
            in_array($tablelink->type, ['oneone', 'manyone']) ? "UNIQUE KEY `{$tablelink->ids[0]}_id` (`{$tablelink->ids[0]}_id`)" : '',
            in_array($tablelink->type, ['oneone', 'onemany']) ? "UNIQUE KEY `{$tablelink->ids[1]}_id` (`{$tablelink->ids[1]}_id`)" : '',
        ]));

        $unique .= ($unique ? ", " : '');

        $definitions['tablelink'][$tablelink->middle_table] = preg_replace('/\s+/', ' ', "create table `{$tablelink->middle_table}` (
                `{$tablelink->ids[0]}_id` char(10) not null,
                `{$tablelink->ids[1]}_id` char(10) not null,
                {$unique} key `fk_{$tablelinkname}_1` (`{$tablelink->ids[0]}_id`),
                key `fk_{$tablelinkname}_2` (`{$tablelink->ids[1]}_id`),
                constraint `fk_{$tablelinkname}_1` foreign key (`{$tablelink->ids[0]}_id`) references `{$dbtables[0]}` (`id`) on delete cascade on update restrict,
                constraint `fk_{$tablelinkname}_2` foreign key (`{$tablelink->ids[1]}_id`) references `{$dbtables[1]}` (`id`) on delete cascade on update restrict
            ) engine=innodb default charset=latin1");
    }

    return $definitions;
}

function landingpage()
{
    $blends = @BlendsConfig::get($_COOKIE['token'])->blends;

    if (!$blends || !count($blends)) {
        error_response('No blends set up');
    }

    $blend = array_keys($blends)[0];

    return "/blend/{$blend}";
}