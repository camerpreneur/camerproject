<?php
/**
 * Project entity view
 *
 * @package ElggGroups
 */

$camerproject = $vars['entity'];

$icon = elgg_view_entity_icon($camerproject, 'tiny', $vars);

$metadata = '';
if (!elgg_in_context('owner_block') && !elgg_in_context('widgets')) {
	// only show entity menu outside of widgets and owner block
	$metadata = elgg_view_menu('entity', array(
		'entity' => $camerproject,
		'handler' => 'camerproject',
		'sort_by' => 'priority',
		'class' => 'elgg-menu-hz',
	));
}


if ($vars['full_view']) {
	echo elgg_view('groups/profile/summary', $vars);
} else {
	// brief view
	$params = array(
		'entity' => $camerproject,
		'metadata' => $metadata,
		'subtitle' => $camerproject->description,
	);
	$params = $params + $vars;
	$list_body = elgg_view('group/elements/summary', $params);

	echo elgg_view_image_block($icon, $list_body, $vars);
}
