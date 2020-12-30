<?php
use contextvariableset\Daterange;
use contextvariableset\Value;
use contextvariableset\Filter;
use contextvariableset\Hidden;
use contextvariableset\Showas;

$blends = [];
$blend_lookup = [];

foreach (array_keys(BlendsConfig::get(AUTH_TOKEN)->blends) as $name) {
    $_blend = Blend::load(AUTH_TOKEN, $name);
    $blends[] = $_blend;
    $blend_lookup[$name] = $_blend;
}

unset($_blend);

$blend = @$blend_lookup[BLEND_NAME];
$showass = ['list'];

if (!$blend) {
    header("Location: /");
}

$fields = $blend->fields;

$types = [];

foreach ($blend->linetypes as $linetype) {
    if (@$blend->hide_types[$linetype]) {
        $types[] = $blend->hide_types[$linetype];
    } elseif (!@$blend->hide_types || $index = array_search($linetype, $blend->hide_types) === false || is_string($index)) {
        $types[] = $linetype;
    }
}

$linetypes = array_map(function($v){
    return Linetype::load(AUTH_TOKEN, $v);
}, $blend->linetypes);

if (!count(filter_objects($fields, 'name', 'is', 'type'))) {
    array_unshift($fields, (object) [
        'name' => 'type',
        'type' => 'text',
        'filteroptions' => $blend->linetypes,
    ]);
}

$generic = (object) [];
$generic_builder = [];

foreach ($fields as $field) {
    $generic_builder[$field->name] = [];

    if ($field->type == 'date') {
        $daterange = new Daterange('daterange');
        $showass[] = 'calendar';
        ContextVariableSet::put('daterange', $daterange);
    } else {
        $cvs = new Value(BLEND_NAME . "_{$field->name}");
        $cvs->label = $field->name;

        if (property_exists($field, 'filteroptions')) {
            if (is_array($field->filteroptions)) {
                $cvs->options = $field->filteroptions;
            } elseif (is_callable($field->filteroptions)) {
                $cvs->options = ($field->filteroptions)(AUTH_TOKEN);
            } else {
                error_response('filteroptions should be an array or a closure');
            }
        }

        ContextVariableSet::put($field->name, $cvs);
    }
}

apply_filters();

$filters = array_merge(@$blend->filters ?? [], get_current_filters($fields));

if (is_string(@$blend->cum)) {
    $cum = false;

    foreach ($filters as $filter) {
        if ($filter->field == $blend->cum) {
            $cum = true;
        }
    }
} else {
    $cum = @$blend->cum;
}

foreach ($fields as $field) {
    if (@$field->summary_if) {
        $field_summary = $field->summary;
        $field->summary = false;

        foreach ($filters as $filter) {
            if ($filter->field == $field->summary_if) {
                $field->summary = $field_summary;
            }
        }
    }
}

$records = $blend->search(AUTH_TOKEN, $filters);

if ($records === false) {
    doover();
}

foreach ($records as $record) {
    foreach ($fields as $field) {
        if (!in_array($record->{$field->name}, $generic_builder[$field->name])) {
            $generic_builder[$field->name][] = $record->{$field->name};
        }
    }
}

foreach ($filters as $filter) {
    if (
        @$filter->cmp == 'like'
        &&
        strpos($filter->value, '%') === false
    ) {
        $fields = filter_objects($fields, 'name', 'not', $filter->field);
    }
}

foreach ($generic_builder as $field => $values) {
    if (count($values) == 1) {
        $generic->{$field} = $values[0];
    }
}

if (count($showass) > 1) {
    $showas = new Showas(BLEND_NAME . "_showas");
    $showas->options = $showass;
    ContextVariableSet::put('showas', $showas);
    define('SHOWAS', $showas->value ?: @$showass[0] ?: 'list');
    $showas->value = SHOWAS;
} else {
    define('SHOWAS', @$showass[0] ?: 'list');
}

$prepop = [];

foreach ($filters as $filter) {
    if (property_exists($filter, 'value') && !is_array($filter->value)) {
        if ($filter->cmp == '=') {
            $prepop[$filter->field] = $filter->value;
        } elseif ($filter->cmp == 'like') {
            $prepop[$filter->field] = str_replace('%', '', $filter->value);
        }
    }
}

return [
    'records' => $records,
    'blend_lookup' => $blend_lookup,
    'linetypes' => $linetypes,
    'fields' => $fields,
    'types' => $types,
    'generic' => $generic,
    'prepop' => $prepop,
    'title' => BLEND_NAME . (@$daterange ? ' &bull; ' . $daterange->getTitle() : ''),
];
