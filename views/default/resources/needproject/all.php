<?php

// breadcrumb
elgg_push_breadcrumb(elgg_echo('camerproject:breadcrumb:needproject:all'));

// title button
elgg_register_title_button(null, 'add', 'object', Needproject::SUBTYPE);

// build page elements
$title = elgg_echo('camerproject:needproject:all');

$body = elgg_list_entities([
	'type' => 'object',
	'subtype' => Needproject::SUBTYPE,
	'no_results' => elgg_echo('notfound'),
]);

// build page
$page = elgg_view_layout('content', [
	'title' => $title,
	'content' => $body,
	'filter' => false, // for now
]);

// draw page
echo elgg_view_page($title, $page);
