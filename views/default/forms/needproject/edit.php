<?php
/**
 *  Add/Edit form of Needproject
 * 
 * @uses $vars['entity'] the needproject edit
 */

use Needproject;

/* @var $entity Needproject */

$entity = elgg_extract('entity', $vars);
$skills = needproject_get_skills();
$ability = needproject_get_ability();

if( $entity instanceof Needproject){    
    // edit    
echo elgg_view_field([
        '#type' => 'hidden',
        'name' => 'guid',
        'value' => $entity->guid,
    ]);
}

// title
echo elgg_view_field([
	'#type' => 'text',
	'#label' => elgg_echo('camerproject:needproject:title'),
	'name' => 'titleneed',
	'value' => elgg_extract('titleneed', $vars),
	'required' => true,
]);

// description

echo elgg_view_field([
    '#type' => 'plaintext',
    '#label' => elgg_echo("camerproject:needproject:description"),
    'name' => 'description',
    'value' => elgg_extract('description', $vars),
    'required' => true,
]);

// shills 

echo elgg_view_field([
    '#type' => "select",
    '#label' => elgg_echo("camerproject:needproject:skills"),
    'name' => 'skills',
    'options_values' => $skills,
    'value' => elgg_extract('skills', $vars),
    'required' => true,
]);

// years of experience

echo elgg_view_field([
    '#type' => 'select',
    '#label' => elgg_echo("camerproject:needproject:yearexper"),
    'name' => 'yearexper',
    'options' => [        
        'zeroandfive' => elgg_echo("camerproject:needprojectyear:zeroandfiveyears"),
        'fiveandten' => elgg_echo("camerproject:needprojectyear:fiveandtenyears"),
        'tenandfiften' => elgg_echo("camerproject:needprojectyear:tenandfifteenyears"),
        'abovefifteen' => elgg_echo("camerproject:needprojectyear:abovefifteenyears"),
    ],
    'value' => elgg_extract('yearexper', $vars),
    'required' => true,
]);

// ability
echo elgg_view_field([
    '#type' => 'select',
    '#label' => elgg_echo("camerproject:needproject:expectedabili"),
    'name' => 'ability',
    'options_values' => $ability,
    'value' => elgg_extract('ability', $vars),
    'required' => true,
]);

// status of need

echo elgg_view_field([
    '#type' => 'select',
    '#label' => elgg_echo("camerproject:needproject:statusneed"),
    'name' => 'statusneed',
    'options' => [
        'needfree' => elgg_echo("camerproject:needprojectstatus:free"),
        'needpaying' => elgg_echo("camerproject:needprojectstatus:paying"),
    ],
    'value' => elgg_extract('statusneed', $vars),
]);

// access
echo elgg_view_field([
	'#type' => 'access',
	'#label' => elgg_echo('access'),
	'name' => 'access_id',
	'value' => (int) elgg_extract('access_id', $vars),
	'entity_type' => 'object',
	'entity_subtype' => Needproject::SUBTYPE,
	'container_guid' => elgg_get_site_entity()->guid,
	'entity' => $entity,
]);

// footer
$footer = elgg_view_field([
	'#type' => 'submit',
	'value' => elgg_echo('save'),
]);

elgg_set_form_footer($footer);





