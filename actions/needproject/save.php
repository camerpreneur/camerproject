<?php

elgg_gatekeeper();

$parent_guid = get_input('parent_guid');
$parent = get_entity($parent_guid);

//sanity check
if (!$parent) {
  register_error(elgg_echo('camerproject:error:invalid:group'));
  forward(REFERER);
}

elgg_make_sticky_form('needproject/edit');

$title = get_input('title');
$decription = get_input('description');
$skills = get_input('skills');
$years = get_input('yearexper');
$ability = get_input('ability');
$statusneed = get_input('statusneed');
 
$guid = (int) get_input('guid');

if (!empty($guid)) {
	$entity = get_entity($guid);
	if (!$entity || !$entity->canEdit()) {
		return elgg_error_response(elgg_echo('actionunauthorized'));
	}
} else {
	$entity = new Needproject();
	
	if (!$entity->save()) {
		return elgg_error_response(elgg_echo('save:fail'));
	}
}

$entity->title = $title;
$entity->description = $decription;
$entity->skills = $skills;
$entity->yearexper = $years;
$entity->ability = $ability;
$entity->statusneed = $statusneed;
$entity->access_id = (int) get_input('access_id');

if (!$entity->save()) {
    return elgg_error_response(elgg_echo('save:fail'));
}

//$camerproject->setNeedproject($entity); //WTF?

elgg_clear_sticky_form('needproject/edit');
return elgg_ok_response('', elgg_echo('save:success'), $entity->getURL());
