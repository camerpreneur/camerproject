<?php

$full = elgg_extract('full_view', $vars, FALSE);
$needproject = elgg_extract('entity', $vars,FALSE);

if(!$needproject){
    return;
}

$title = $needproject->title;


