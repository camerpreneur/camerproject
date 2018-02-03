<?php

gatekeeper();

$guid = (int) elgg_extract('guid', $vars);

elgg_entity_gatekeeper($guid, 'object', Needproject::SUBTYPE);

/* @var $entity Needproject */
$entity = get_entity($guid);

if (!$entity->canEdit()) {
	register_error(elgg_echo('limited_access'));
	forward(REFERER);
}

// breadcrumb
elgg_push_breadcrumb(elgg_echo('camerproject:breadcrumb:needproject:all'), 'needproject/all');
elgg_push_breadcrumb($entity->getDisplayName(), $entity->getURL());
elgg_push_breadcrumb(elgg_echo('edit'));

// build page elements
$title = elgg_echo('camerproject:needproject:edit', [$entity->getDisplayName()]);
$body_vars = camerproject_prepare_needproject_vars();
$body = elgg_view_form('needproject/edit', $body_vars);

// build page
$page = elgg_view_layout('content', [
	'title' => $title,
	'content' => $body,
	'filter' => false,
]);

// draw page
echo elgg_view_page($title, $page);

