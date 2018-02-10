<?php
 /**
  * function return the the state of the project are
  * @return array
  */
function project_get_progress(){
    
    $codes = [
        'elab_concept',
        'prototype',
        'test',
        'pre_client',
        'pre_result_financiere',
        'develop',
        'develop_inter',
    ];
    
    $progress = [];
    
    foreach ($codes as $code){
        $progress[$code] = elgg_echo("camerproject:projectstatus:title:$code");      
    }
    return $progress;
}

/**
 * function define the currency of project
 * 
 */

function project_get_currency(){
    
    $codes = [
        'usd',
        'euro',
        'franc',
        'niara',
        'rand',
        'dinar',
        'peso',
        'birr',
        'cedi',    
    ];
    
    $currency = [];
    
    foreach ( $codes as $code){
        $currency[$code] = elgg_echo("camerproject:projectcurrency:name:$code");
    }
    uksort($currency, 'strcasecmp');
    
    return $currency;
}

function project_get_sectorindustry(){
    
    $codes = [
        'agri',
        'auto',
        'bank',
        'biolo',
        'afric',
        'it',    
    ];
    
    $sector = [];
    
    foreach ( $codes as $code){
        $sector[$code] = elgg_echo("camerproject:projectindustrysector:name:$code");
    }
    uksort($sector, 'strcasecmp');
    
    return $sector;
}


/**
 * function define industry sector 
 * 
 */

function project_get_industry(){
    
    $codes = [
        'agri',
        'auto',
        'bank',
        'biolo',
        'afric',
        'it',       
    ];
    
    $$industry = [];
    
    foreach ($codes as $code){
        $industry[$code] = elgg_echo("camerproject:projectindustrysector:name:$code");
    }
    
    uksort($industry, 'strcasecmp');
    
    return $industry;
}

/**
 *  function define skills of need 
 */

function needproject_get_skills(){
    
    $codes = [
        'archi',
        'art',
        'busintil',
        'com',
    ];
    
    $skills = [];
    
    foreach ( $codes as $code ){
        $skills[$code] = elgg_echo("camerproject:needprojectskill:$code");
    }
    
    uksort($skills, 'strcasecmp');
    
    return $skills;
}

function needproject_get_ability(){
    
    $codes = [
        'actanddyn',
        'analandcrit',
        'atease',
    ];
            
    $ability = [];
    
    foreach ($codes as $code ){
      
        $ability[$code] = elgg_echo("camerproject:needprojectability:$code");
    }
    
    uksort($ability, 'strcasecmp');
    
    return $ability;
}

/**
 * Prepare the form vars for add/edit a Needproject
 *
 * @param Needproject $entity (optional) the entity to edit
 *
 * @return array
 */
function camerproject_prepare_needproject_vars(Needproject $entity = null) {
	
	// defaults
	$result = [
		'title' => '',
		'description' => '',
        'skills' => '',
        'ability' => '',
        'statusneed' => '',
		'access_id' => get_default_access(null, [
			'entity_type' => 'object',
			'entity_subtype' => Needproject::SUBTYPE,
			'container_guid' => elgg_get_site_entity()->guid,
		]),
	];
	
        $sticky_vars = elgg_get_sticky_values('needproject/edit');
	
        if (!empty($sticky_vars)) {
            
            foreach ($sticky_vars as $name => $value) {
                    $result[$name] = $value;
            }		
            elgg_clear_sticky_form('needproject/edit');
	}
	
	return $result;
}


/**
 * Determines if a need could potentially be moved
 * To a parent group
 * Makes sure permissions are in order, and that the subgroup isn't already a parent
 * of the parent or anything weird like that
 * 
 * @param type $user ElggUser
 * @param type $needproject_guid
 * @param type $parentgroup_guid
 */
function can_move_needproject($subgroup, $parent, $user = NULL) {
	if (!elgg_instanceof($user, 'user')) {
		$user = elgg_get_logged_in_user_entity();
	}

	if (!$user) {
		return false;
	}

	// make sure they're really groups
	if (!elgg_instanceof($subgroup, 'object') || !elgg_instanceof($parent, 'group')) {
		return false;
	}

	// make sure we can edit them
	if (!$subgroup->canEdit($user->guid) || !$parent->canEdit($user->guid)) {
		return false;
	}

	// make sure we can edit all the way up, and we're not trying to move a group into itself
	if (!can_edit_recursive($subgroup) || $subgroup->guid == $parent->guid) {
		return false;
	}

	// make sure we're not moving a group into it's existing parent
	$current_parent = get_parent_group($subgroup);
	if ($current_parent && $current_parent->guid == $parent->guid) {
		return false;
	}

	// also make sure the potential parent isn't a subgroup of the subgroup
	$children = get_all_children_guids($subgroup);
	if (in_array($parent->guid, $children)) {
		return false;
	}

	return true;
}

/**
 * Determines if a group is a needproject of another group
 * 
 * @param type $group
 * return ElggGroup | false
 */
function get_parent_group($group) {
	if (!elgg_instanceof($group, 'group')) {
		return false;
	}

	$parent = elgg_get_entities_from_relationship(array(
		'types' => array('group'),
		'limit' => 1,
		'relationship' => Camerproject::AFFECTED_NEEDPROJECT,
		'relationship_guid' => $group->guid,
	));

	if (is_array($parent) && isset($parent[0])) {
		return $parent[0];
	}

	return false;
}



function breadcrumb_override($params) {
	switch ($params['segments'][0]) {
		case 'profile':
			$group = get_entity($params['segments'][1]);
			if (!$group) {
				return;
			}

			$breadcrumbs[] = array('title' => elgg_echo('groups'), 'link' => elgg_get_site_url() . 'groups/all');
			$parentcrumbs = parent_breadcrumbs($group, false);

			foreach ($parentcrumbs as $parentcrumb) {
				$breadcrumbs[] = $parentcrumb;
			}

			$breadcrumbs[] = array(
				'title' => $group->name,
				'link' => NULL
			);

			set_input('au_subgroups_breadcrumbs', $breadcrumbs);
			break;

		case 'edit':
			$group = get_entity($params['segments'][1]);
			if (!$group) {
				return;
			}

			$breadcrumbs[] = array('title' => elgg_echo('groups'), 'link' => elgg_get_site_url() . 'camerproject/all');
			$parentcrumbs = parent_breadcrumbs($group, false);

			foreach ($parentcrumbs as $parentcrumb) {
				$breadcrumbs[] = $parentcrumb;
			}
			$breadcrumbs[] = array('title' => $group->name, 'link' => $group->getURL());
			$breadcrumbs[] = array('title' => elgg_echo('groups:edit'), 'link' => NULL);

			set_input('au_subgroups_breadcrumbs', $breadcrumbs);
			break;
	}
}