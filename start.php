<?php

/**
 *  CamerProject Plugin 
 * 
 * @package ElggGroup
 */
require_once __DIR__ . '/lib/functions.php';

//elgg_register_plugin_hook_handler('route:rewrite', 'groups', 'camerproject_rewrite_handler');

elgg_register_event_handler('init', 'system', 'camerproject_init');
elgg_register_event_handler('init', 'system', 'camerproject_fields_setup', 10000);


function camerproject_init(){
    
   elgg_register_entity_type('group', Camerproject::SUBTYPE);
   elgg_register_entity_type('object', Needproject::SUBTYPE);   
   // plugin hook
   elgg_register_plugin_hook_handler('access:collections:write', 'all', '\camerpreneur\camerproject\Access::accessArray', 999); 
   // Register a page handler, so we can have nice URLs
   elgg_unregister_page_handler('groups','groups_page_handler');
   elgg_register_page_handler('camerproject', 'camerproject_page_handler');
    
   // Set up the menu
    $item = new ElggMenuItem('groups', elgg_echo('groups'), 'camerproject/all'); 
    elgg_register_menu_item('site', $item);
    
   // Register URL handlers for groups
   elgg_unregister_plugin_hook_handler('entity:url', 'group', 'groups_set_url');
   elgg_register_plugin_hook_handler('entity:url', 'group', 'camerprojects_set_url');

   // prepare profile buttons to be registered in the title menu
   elgg_unregister_plugin_hook_handler('profile_buttons', 'group', 'groups_prepare_profile_buttons'); 
   elgg_register_plugin_hook_handler('profile_buttons', 'group', 'camerproject_prepare_profile_buttons');
   
   //
   elgg_unregister_event_handler('pagesetup', 'system', 'groups_setup_sidebar_menus');
   elgg_register_event_handler('pagesetup', 'system', 'camerproject_setup_sidebar_menus');
   
   
   // project members tabs
   elgg_unregister_plugin_hook_handler('register', 'menu:groups_members', 'groups_members_menu_setup');
   elgg_register_plugin_hook_handler('register', 'menu:groups_members', 'camerproject_members_menu_setup');
   
   // register actions
   $actions_bases = __DIR__.'/actions/needproject/';
    
   elgg_register_action('needproject/edit', "$actions_bases/save.php");
   elgg_register_action('needproject/delete', "$actions_bases/delete.php");
   
    // register page handlers
    elgg_register_page_handler('needproject', '\camerpreneur\camerproject\Router::needproject');
   
   
   // Help core resolve page owner guids from group routes
   // Registered with an earlier priority to be called before default_page_owner_handler()    
    elgg_unregister_plugin_hook_handler('page_owner', 'system', 'groups_default_page_owner_handler', 400);
    elgg_register_plugin_hook_handler('page_owner', 'system', 'camerproject_default_page_owner_handler', 400);
    
  // notification
    elgg_unregister_page_handler('notifications', 'notifications_page_handler');
    elgg_register_page_handler('notifications', 'notifications_camerproject_page_handler');
    
    elgg_unregister_event_handler('pagesetup', 'system', 'notifications_plugin_pagesetup');
    elgg_register_event_handler('pagesetup', 'system', 'notifications_camerproject_plugin_pagesetup');
    
  // pages of project
    elgg_unextend_view('groups/tool_latest', 'pages/group_module');
    elgg_extend_view('groups/tool_latest', 'pages/group_module');
    
    elgg_unregister_page_handler('pages', 'pages_page_handler');
    elgg_register_page_handler('pages', 'camerproject_pages_page_handler');
    elgg_unregister_plugin_hook_handler('register', 'menu:owner_block', 'pages_owner_block_menu');
    elgg_register_plugin_hook_handler('register', 'menu:owner_block', 'camerproject_pages_owner_block_menu');      
}   


/**
 * Add a menu item to the user ownerblock
 */
function camerproject_pages_owner_block_menu($hook, $type, $return, $params) {
	if (elgg_instanceof($params['entity'], 'user')) {
		$url = "pages/owner/{$params['entity']->username}";
		$item = new ElggMenuItem('pages', elgg_echo('pages'), $url);
		$return[] = $item;
	} else {
		if ($params['entity']->pages_enable != "no") {
			$url = "pages/project/{$params['entity']->guid}/all";
			$item = new ElggMenuItem('pages', elgg_echo('pages:group'), $url);
			$return[] = $item;
		}
	}

	return $return;
}


/**
 * Dispatcher for pages.
 * URLs take the form of
 *  All pages:        pages/all
 *  User's pages:     pages/owner/<username>
 *  Friends' pages:   pages/friends/<username>
 *  View page:        pages/view/<guid>/<title>
 *  New page:         pages/add/<guid> (container: user, group, parent)
 *  Edit page:        pages/edit/<guid>
 *  History of page:  pages/history/<guid>
 *  Revision of page: pages/revision/<id>
 *  Group pages:      pages/group/<guid>/all
 *
 * Title is ignored
 *
 * @param array $page
 * @return bool
 */
function camerproject_pages_page_handler($page) {

	elgg_load_library('elgg:pages');
	
	if (!isset($page[0])) {
		$page[0] = 'all';
	}

	elgg_push_breadcrumb(elgg_echo('pages'), 'pages/all');

	$page_type = $page[0];
	switch ($page_type) {
		case 'owner':
			echo elgg_view_resource('pages/owner');
			break;
		case 'friends':
			echo elgg_view_resource('pages/friends');
			break;
		case 'view':
			echo elgg_view_resource('pages/view', [
				'guid' => $page[1],
			]);
			break;
		case 'add':
			echo elgg_view_resource('pages/new', [
				'guid' => $page[1],
			]);
			break;
		case 'edit':
			echo elgg_view_resource('pages/edit', [
				'guid' => $page[1],
			]);
			break;
		case 'group':
			echo elgg_view_resource('pages/owner');
			break;
		case 'history':
			echo elgg_view_resource('pages/history', [
				'guid' => $page[1],
			]);
			break;
		case 'revision':
			echo elgg_view_resource('pages/revision', [
				'id' => $page[1],
			]);
			break;
		case 'all':
			$dir = __DIR__ . "/views/" . elgg_get_viewtype();
			if (_elgg_view_may_be_altered('resources/pages/world', "$dir/resources/pages/world.php")) {
				elgg_deprecated_notice('The view "resources/pages/world" is deprecated. Use "resources/pages/all".', 2.3);
				echo elgg_view_resource('pages/world', ['__shown_notice' => true]);
			} else {
				echo elgg_view_resource('pages/all');
			}
			break;
		default:
			return false;
	}
	return true;
}

/**
 * Notification settings sidebar menu
 *
 */
function notifications_camerproject_plugin_pagesetup() {
	if (elgg_in_context("settings") && elgg_get_logged_in_user_guid()) {

		$user = elgg_get_page_owner_entity();
		if (!$user) {
			$user = elgg_get_logged_in_user_entity();
		}

		$params = array(
			'name' => '2_a_user_notify',
			'text' => elgg_echo('notifications:subscriptions:changesettings'),
			'href' => "notifications/personal/{$user->username}",
			'section' => "notifications",
		);
		elgg_register_menu_item('page', $params);
		
		if (elgg_is_active_plugin('groups')) {
			$params = array(
				'name' => '2_group_notify',
				'text' => elgg_echo('notifications:subscriptions:changesettings:groups'),
				'href' => "notifications/project/{$user->username}",
				'section' => "notifications",
			);
			elgg_register_menu_item('page', $params);
		}
	}
}


/**
 * Route page requests
 *
 * @param array $page Array of url parameters
 * @return bool
 */
function notifications_camerproject_page_handler($page) {

	elgg_gatekeeper();

	// Set the context to settings
	elgg_set_context('settings');

	$current_user = elgg_get_logged_in_user_entity();

	// default to personal notifications
	if (!isset($page[0])) {
		$page[0] = 'personal';
	}
	if (!isset($page[1])) {
		forward("notifications/{$page[0]}/{$current_user->username}");
	}

	$vars['username'] = $page[1];

	// note: $user passed in
	switch ($page[0]) {
		case 'project':
			echo elgg_view_resource('notifications/groups', $vars);
			break;
		case 'personal':
			echo elgg_view_resource('notifications/index', $vars);
			break;
		default:
			return false;
	}
	return true;
}


function camerproject_rewrite_handler($hook, $type, $value, $params) {
    
    $value['identifier'] = 'camerproject';
    return $value;

}


/**
 * Setup group members tabs
 *
 * @param string         $hook   "register"
 * @param string         $type   "menu:groups_members"
 * @param ElggMenuItem[] $menu   Menu items
 * @param array          $params Hook params
 *
 * @return void|ElggMenuItem[]
 */
function camerproject_members_menu_setup($hook, $type, $menu, $params) {

	$entity = elgg_extract('entity', $params);
	if (empty($entity) || !($entity instanceof ElggGroup)) {
		return;
	}

	$menu[] = ElggMenuItem::factory([
		'name' => 'alpha',
		'text' => elgg_echo('sort:alpha'),
		'href' => "camerproject/members/{$entity->getGUID()}",
		'priority' => 100
	]);

	$menu[] = ElggMenuItem::factory([
		'name' => 'newest',
		'text' => elgg_echo('sort:newest'),
		'href' => "camerproject/members/{$entity->getGUID()}/newest",
		'priority' => 200
	]);

	return $menu;
}



function camerproject_fields_setup(){
    
    $profile_defaults = [
        'description' => 'longtext',
        'progress' => 'text',
        'industry' => 'tags',
        'activity' => 'text',
        'markettype' => 'text',
        'typemark' => 'text',
        'offertype' => 'text',
        'turnover' => 'text',
        'currency' => 'text',
        'location' => 'location',
        'projectwebsite' => 'url',
        'projectpitch' => 'url',
	];
    
    $profile_defaults = elgg_trigger_plugin_hook('profile:fields', 'group', NULL, $profile_defaults);

    elgg_set_config('group', $profile_defaults);
    
    // register any tag metadata names
    foreach ($profile_defaults as $name => $type) {
            if ($type == 'tags') {
                elgg_register_tag_metadata_name($name);

                // only shows up in search but why not just set this in en.php as doing it here
                // means you cannot override it in a plugin
                add_translation(get_current_language(), array("tag_names:$name" => elgg_echo("camerproject:$name")));
            }
    }
    
}


function camerproject_page_handler($page) {

	if (!isset($page[0])) {
		$page[0] = 'all';
	}

	elgg_push_breadcrumb(elgg_echo('camerproject'), "camerproject/all");

	$vars = [];
	switch ($page[0]) {
		case 'add':
		case 'all':
		case 'owner':
		case 'search':
			echo elgg_view_resource("camerproject/{$page[0]}");
			break;
		case 'invitations':
		case 'member':
			echo elgg_view_resource("camerproject/{$page[0]}", [
				'username' => $page[1],
			]);
			break;
		case 'members':
			$vars['sort'] = elgg_extract('2', $page, 'alpha');
			$vars['guid'] = elgg_extract('1', $page);
			if (elgg_view_exists("resources/camerproject/members/{$vars['sort']}")) {
				echo elgg_view_resource("camerproject/members/{$vars['sort']}", $vars);
			} else {
				echo elgg_view_resource('camerproject/members', $vars);
			}
			break;
		case 'profile':
			// Page owner and context need to be set before elgg_view() is
			// called so they'll be available in the [pagesetup, system] event
			// that is used for registering items for the sidebar menu.
			// @see groups_setup_sidebar_menus()
			elgg_push_context('group_profile');
			elgg_set_page_owner_guid($page[1]);
		case 'activity':
		case 'edit':
		case 'invite':
		case 'requests':
			echo elgg_view_resource("camerproject/{$page[0]}", [
				'guid' => $page[1],
			]);
			break;
		default:
			return false;
	}
	return true;
}
        
/**
 * Populates the ->getUrl() method for group objects
 *
 * @param string $hook
 * @param string $type
 * @param string $url
 * @param array  $params
 * @return string
 */
function camerprojects_set_url($hook, $type, $url, $params) {
	$entity = $params['entity'];
	$title = elgg_get_friendly_title($entity->name);
	return "camerproject/profile/{$entity->guid}/$title";
}         

/**
 * Returns menu items to be registered in the title menu of the group profile
 *
 * @param string         $hook   "profile_buttons"
 * @param string         $type   "group"
 * @param ElggMenuItem[] $items  Buttons
 * @param array          $params Hook params
 * @return ElggMenuItem[]
 */
function camerproject_prepare_profile_buttons($hook, $type, $items, $params) {

	$group = elgg_extract('entity', $params);
	if (!$group instanceof ElggGroup) {
		return;
	}

	$actions = [];

	if ($group->canEdit()) {
		// group owners can edit the group and invite new members
		$actions['groups:edit'] = "camerproject/edit/{$group->guid}";
		$actions['groups:invite'] = "camerproject/invite/{$group->guid}";
	}

	$user = elgg_get_logged_in_user_entity();
	if ($user && $group->isMember($user)) {
		if ($group->owner_guid != $user->guid) {
			// a member can leave a group if he/she doesn't own it
			$actions['groups:leave'] = "action/groups/leave?group_guid={$group->guid}";
		}
	} else if ($user) {
		$url = "action/groups/join?group_guid={$group->guid}";
		if ($group->isPublicMembership() || $group->canEdit()) {
			// admins can always join
			// non-admins can join if membership is public
			$actions['groups:join'] = $url;
		} else {
			// request membership
			$actions['groups:joinrequest'] = $url;
		}
	}

	foreach ($actions as $action => $url) {
		$items[] = ElggMenuItem::factory(array(
			'name' => $action,
			'href' => elgg_normalize_url($url),
			'text' => elgg_echo($action),
			'is_action' => 0 === strpos($url, 'action'),
			'link_class' => 'elgg-button elgg-button-action',
		));
	}

	return $items;
}


/**
 * Configure the groups sidebar menu. Triggered on page setup
 *
 */
function camerproject_setup_sidebar_menus() {

	// Get the page owner entity
	$page_owner = elgg_get_page_owner_entity();

	if (elgg_in_context('group_profile')) {
		if (!elgg_instanceof($page_owner, 'group')) {
			forward('', '404');
		}

		if (elgg_is_logged_in() && $page_owner->canEdit() && !$page_owner->isPublicMembership()) {
			$url = elgg_get_site_url() . "camerproject/requests/{$page_owner->getGUID()}";

			$count = elgg_get_entities_from_relationship(array(
				'type' => 'user',
				'relationship' => 'membership_request',
				'relationship_guid' => $page_owner->getGUID(),
				'inverse_relationship' => true,
				'count' => true,
			));

			if ($count) {
				$text = elgg_echo('groups:membershiprequests:pending', array($count));
			} else {
				$text = elgg_echo('groups:membershiprequests');
			}

			elgg_register_menu_item('page', array(
				'name' => 'membership_requests',
				'text' => $text,
				'href' => $url,
			));
		}
	}
	if (elgg_get_context() == 'groups' && !elgg_instanceof($page_owner, 'group')) {
		elgg_register_menu_item('page', array(
			'name' => 'camerproject:all',
			'text' => elgg_echo('camerproject:all'),
			'href' => 'camerproject/all',
		));

		$user = elgg_get_logged_in_user_entity();
		if ($user) {
			$url =  "camerproject/owner/$user->username";
			$item = new ElggMenuItem('groups:owned', elgg_echo('groups:owned'), $url);
			elgg_register_menu_item('page', $item);

			$url = "camerproject/member/$user->username";
			$item = new ElggMenuItem('groups:member', elgg_echo('groups:yours'), $url);
			elgg_register_menu_item('page', $item);

			$url = "camerproject/invitations/$user->username";
			$invitation_count = groups_get_invited_groups($user->getGUID(), false, array('count' => true));

			if ($invitation_count) {
				$text = elgg_echo('groups:invitations:pending', array($invitation_count));
			} else {
				$text = elgg_echo('groups:invitations');
			}

			$item = new ElggMenuItem('groups:user:invites', $text, $url);
			elgg_register_menu_item('page', $item);
		}
	}
}


/**
 * Helper handler to correctly resolve page owners on group routes
 *
 * @see default_page_owner_handler()
 *
 * @param string $hook   "page_owner"
 * @param string $type   "system"
 * @param int    $return Page owner guid
 * @param array  $params Hook params
 * @return int|void
 */
function camerproject_default_page_owner_handler($hook, $type, $return, $params) {

	if ($return) {
		return;
	}

	$segments = _elgg_services()->request->getUrlSegments();
	$identifier = array_shift($segments);

	if ($identifier !== 'groups') {
		return;
	}

	$page = array_shift($segments);

	switch ($page) {

		case 'add' :
			$guid = array_shift($segments);
			if (!$guid) {
				$guid = elgg_get_logged_in_user_guid();
			}
			return $guid;

		case 'edit':
		case 'profile' :
		case 'activity' :
		case 'invite' :
		case 'requests' :
		case 'members' :
		case 'profile' :
			$guid = array_shift($segments);
			if (!$guid) {
				return;
			}
			return $guid;

		case 'member' :
		case 'owner' :
		case 'invitations':
			$username = array_shift($segments);
			if ($username) {
				$user = get_user_by_username($username);
			} else {
				$user = elgg_get_logged_in_user_entity();
			}
			if (!$user) {
				return;
			}
			return $user->guid;
	}
}
