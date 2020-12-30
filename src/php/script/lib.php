<?php
use contextvariableset\Daterange;
use contextvariableset\Hidden;
use contextvariableset\Repeater;
use contextvariableset\Value;
use contextvariableset\Filter;

function get_basic_filters($fields)
{
    $filters = [];
    $datefield = null;
    $repeater = new Repeater(BLEND_NAME . "_repeater");

    foreach ($fields as $field) {
        if ($field->type == 'date') {
            $datefield = $field;
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


function get_current_filters($fields)
{
    $filters = get_basic_filters($fields);

    $daterange = new Daterange('daterange');

    foreach ($fields as $field) {
        if ($field->type == 'date') {
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
        }
    }

    return $filters;
}

function get_past_filters($fields)
{
    $filters = get_basic_filters($fields);
    $daterange = new Daterange('daterange');

    foreach ($fields as $field) {
        if ($field->type == 'date') {
            if ($daterange->from) {
                $filters[] = (object)[
                    'field' => 'date',
                    'value' => $daterange->from,
                    'cmp' => '<',
                ];
            }
        }
    }

    return $filters;
}

function get_current_plus_past_filters($fields)
{
    $filters = get_basic_filters($fields);
    $daterange = new Daterange('daterange');

    foreach ($fields as $field) {
        if ($field->type == 'date') {
            if ($daterange->to) {
                $filters[] = (object)[
                    'field' => 'date',
                    'value' => $daterange->to,
                    'cmp' => '<=',
                ];
            }
        }
    }

    return $filters;
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
                'modified' => (object) ['def' => 'timestamp not null default current_timestamp on update current_timestamp'],
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
        }, $fields, array_keys($fields))) . ", primary key (`id`), " . implode(", ", array_filter(array_map(function($def, $fieldName){
            if (in_array($fieldName, ['id', 'created', 'modified']) || preg_match('/^[a-z]*(?:text|blob)\b/', strtolower($def->def))) {
                return;
            }

            return "index(`{$fieldName}`)";
        }, $fields, array_keys($fields)))) . ") engine=InnoDB default charset=latin1";
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
