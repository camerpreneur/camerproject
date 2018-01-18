<?php

$guid = (int) elgg_extract('guid', $vars);
elgg_entity_gatekeeper($guid, 'object', Needproject::SUBTYPE);

/* @var $entity Needproject */
$entity = get_entity($guid);

// breadcrumb
elgg_push_breadcrumb(elgg_echo('camerproject:breadcrumb:needproject:all'), 'needproject/all');
elgg_push_breadcrumb($entity->getDisplayName());

//if (camerproject_is_staff()) {
	elgg_register_menu_item('title', [
		'name' => 'camerproject',
		'text' => elgg_echo('camerproject:add'),
		'href' => elgg_http_add_url_query_elements('camerproject/add', [
			'needproject' => [$entity->guid],
		]),
		'link_class' => 'elgg-button elgg-button-action',
	]);
//}

// build page elements
$title = $entity->getDisplayName();

$body = elgg_view_entity($entity);

$sidebar = elgg_view('camerproject/needproject/sidebar', ['entity' => $entity]);

// build page
$page = elgg_view_layout('content', [
	'title' => $title,
	'content' => $body,
	'sidebar' => $sidebar,
	'filter' => false,
]);

// draw page
echo elgg_view_page($title, $page);


