<?php
/*
 * add needproject
 */

gatekeeper();

// breadcrumb
//elgg_push_breadcrumb(elgg_echo('camerproject:breadcrumb:needproject:all'), 'needproject/all');
elgg_push_breadcrumb(elgg_echo('add'));

// build page elements
$title = elgg_echo('camerproject:needproject:add');

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