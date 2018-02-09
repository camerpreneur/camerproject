<?php

/**
 *  CamerProject Plugin 
 * 
 * @package ElggGroup
 */
require_once __DIR__ . '/lib/functions.php';
require_once __DIR__ . '/lib/events.php';
require_once __DIR__ . '/lib/hooks.php';

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
   
   // activity project
   elgg_unextend_view('groups/tool_latest', 'groups/profile/activity_module');
   elgg_extend_view('groups/tool_latest', 'groups/profile/activity_module');
   
   elgg_unregister_plugin_hook_handler('register', 'menu:owner_block', 'groups_activity_owner_block_menu');
   elgg_register_plugin_hook_handler('register', 'menu:owner_block', 'camerproject_activity_owner_block_menu');
   
   // project members tabs
   elgg_unregister_plugin_hook_handler('register', 'menu:groups_members', 'groups_members_menu_setup');
   elgg_register_plugin_hook_handler('register', 'menu:groups_members', 'camerproject_members_menu_setup');
   
   // register actions
   $actions_bases = __DIR__.'/actions/needproject/';
    
   elgg_register_action('needproject/edit', "$actions_bases/save.php");
   elgg_register_action('needproject/delete', "$actions_bases/delete.php");
   
   // register page handlers
   //elgg_register_page_handler('needproject', '\camerpreneur\camerproject\Router::needproject'); //Done in camerproject_page_handler
   
   // au subgroups 

//   remove_group_tool_option('subgroups', elgg_echo('au_subgroups:group:enable'));
//   remove_group_tool_option('subgroups_members_create', elgg_echo('au_subgroups:group:memberspermissions'));
//   elgg_unextend_view('forms/groups/edit', 'forms/au_subgroups/edit');  
//   elgg_unextend_view('groups/tool_latest', 'au_subgroups/group_module');
//   elgg_extend_view('groups/tool_latest', 'au_subgroups/group_module');
//   
  
  

   //elgg_unregister_page_handler('au_subgroups', __NAMESPACE__ . '\\au_subgroups_pagehandler');
   //elgg_register_page_handler('au_subgroups', __NAMESPACE__ . '\\camer_au_subgroups_pagehandler');

   remove_group_tool_option('subgroups', elgg_echo('au_subgroups:group:enable'));
   remove_group_tool_option('subgroups_members_create', elgg_echo('au_subgroups:group:memberspermissions'));
   elgg_unextend_view('forms/groups/edit', 'forms/au_subgroups/edit');  
   elgg_unextend_view('groups/tool_latest', 'au_subgroups/group_module');
   elgg_extend_view('groups/tool_latest', 'au_subgroups/group_module');

   
   elgg_unregister_event_handler('pagesetup', 'system', 'pagesetup');
   elgg_register_event_handler('pagesetup', 'system', 'camerprojectpagesetup');

   elgg_unregister_page_handler('au_subgroups', 'au_subgroups_pagehandler');
   elgg_register_page_handler('au_subgroups', 'camer_au_subgroups_pagehandler'); //ToDoIt
   
   elgg_unregister_plugin_hook_handler('route', 'groups', 'groups_router', 400);
   elgg_register_plugin_hook_handler('route', 'groups', 'camer_groups_router', 400); //ToDoIt
   elgg_unextend_view('groups/edit', 'au_subgroups/group/transfer');
   
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
    elgg_unregister_plugin_hook_handler('page_owner', 'system', 'default_page_owner_handler');
    elgg_register_plugin_hook_handler('page_owner', 'system', 'camer_default_page_owner_handler');
    
  // files of project 
    elgg_unextend_view('groups/tool_latest', 'file/group_module');
    elgg_extend_view('groups/tool_latest', 'file/group_module');

    elgg_unregister_page_handler('file', 'file_page_handler');
    elgg_register_page_handler('file', 'camerproject_file_page_handler');
    elgg_unregister_plugin_hook_handler('register', 'menu:owner_block', 'file_owner_block_menu');
    elgg_register_plugin_hook_handler('register', 'menu:owner_block', 'camerproject_file_owner_block_menu');
  
  // discussions of project
    elgg_unregister_page_handler('discussion', 'discussion_page_handler');
    elgg_register_page_handler('discussion', 'camerproject_discussion_page_handler');
    elgg_unregister_plugin_hook_handler('register', 'menu:owner_block', 'discussion_owner_block_menu');
    elgg_register_plugin_hook_handler('register', 'menu:owner_block', 'camerproject_discussion_owner_block_menu');
    
  // bookmarks of project
    elgg_unregister_page_handler('bookmarks', 'bookmarks_page_handler');
    elgg_register_page_handler('bookmarks', 'camerproject_bookmarks_page_handler');
    elgg_unregister_plugin_hook_handler('register', 'menu:owner_block', 'bookmarks_owner_block_menu');
    elgg_register_plugin_hook_handler('register', 'menu:owner_block', 'camerproject_bookmarks_owner_block_menu');
 
} 


function camer_au_subgroups_pagehandler($page) {
	
	// dirty check to avoid duplicate page handlers
	// since this should only be called from the route, groups hook
	if (strpos(current_page_url(), elgg_get_site_url() . 'needproject') === 0) {
		return false;
	}
	
	switch ($page[0]) {
		case 'add':
			set_input('au_subgroup', true);
			set_input('au_subgroup_parent_guid', $page[1]);
			elgg_set_page_owner_guid($page[1]);
			echo elgg_view_resource('needproject/add');
			return true;
			break;
		
		case 'list':
			elgg_set_page_owner_guid($page[1]);
			echo elgg_view('resources/au_subgroups/list');
                       return true;
			break;
		
		case 'delete':
			elgg_set_page_owner_guid($page[1]);
			echo elgg_view('resources/au_subgroups/delete');
                       return true;
			break;
		
		case 'openclosed':
			set_input('filter', $page[1]);
			echo elgg_view('resources/au_subgroups/openclosed');
			return true;
			break;
	}
	
	return false;
}


/**
 * Add owner block link
 */
function camerproject_activity_owner_block_menu($hook, $type, $return, $params) {
	if (elgg_instanceof($params['entity'], 'group')) {
		if ($params['entity']->activity_enable != "no") {
			$url = "camerproject/activity/{$params['entity']->guid}";
			$item = new ElggMenuItem('activity', elgg_echo('groups:activity'), $url);
			$return[] = $item;
		}
	}

	return $return;
}
 
/**
 * Add a menu item to an ownerblock
 * 
 * @param string $hook
 * @param string $type
 * @param array  $return
 * @param array  $params
 */
function camerproject_bookmarks_owner_block_menu($hook, $type, $return, $params) {
	if (elgg_instanceof($params['entity'], 'user')) {
		$url = "bookmarks/owner/{$params['entity']->username}";
		$item = new ElggMenuItem('bookmarks', elgg_echo('bookmarks'), $url);
		$return[] = $item;
	} else {
		if ($params['entity']->bookmarks_enable != 'no') {
			$url = "bookmarks/project/{$params['entity']->guid}/all";
			$item = new ElggMenuItem('bookmarks', elgg_echo('bookmarks:group'), $url);
			$return[] = $item;
		}
	}

	return $return;
}


/**
 * Dispatcher for bookmarks.
 *
 * URLs take the form of
 *  All bookmarks:        bookmarks/all
 *  User's bookmarks:     bookmarks/owner/<username>
 *  Friends' bookmarks:   bookmarks/friends/<username>
 *  View bookmark:        bookmarks/view/<guid>/<title>
 *  New bookmark:         bookmarks/add/<guid> (container: user, group, parent)
 *  Edit bookmark:        bookmarks/edit/<guid>
 *  Group bookmarks:      bookmarks/group/<guid>/all
 *  Bookmarklet:          bookmarks/bookmarklet/<guid> (user)
 *
 * Title is ignored
 *
 * @param array $page
 * @return bool
 */
function camerproject_bookmarks_page_handler($page) {

	elgg_load_library('elgg:bookmarks');

	if (!isset($page[0])) {
		$page[0] = 'all';
	}

	elgg_push_breadcrumb(elgg_echo('bookmarks'), 'bookmarks/all');

	switch ($page[0]) {
		case "all":
			echo elgg_view_resource('bookmarks/all');
			break;

		case "owner":
			echo elgg_view_resource('bookmarks/owner');
			break;

		case "friends":
			echo elgg_view_resource('bookmarks/friends');
			break;

		case "view":
			echo elgg_view_resource('bookmarks/view', [
				'guid' => $page[1],
			]);
			break;

		case "add":
			echo elgg_view_resource('bookmarks/add');
			break;

		case "edit":
			echo elgg_view_resource('bookmarks/edit', [
				'guid' => $page[1],
			]);
			break;

		case 'project':
			echo elgg_view_resource('bookmarks/owner');
			break;

		case "bookmarklet":
			echo elgg_view_resource('bookmarks/bookmarklet', [
				'container_guid' => $page[1],
			]);
			break;

		default:
			return false;
	}

	elgg_pop_context();
	return true;
}

/**
 * Add owner block link for groups
 *
 * @param string         $hook   'register'
 * @param string         $type   'menu:owner_block'
 * @param ElggMenuItem[] $return
 * @param array          $params
 * @return ElggMenuItem[] $return
 */
function camerproject_discussion_owner_block_menu($hook, $type, $return, $params) {
	if (elgg_instanceof($params['entity'], 'group')) {
		if ($params['entity']->forum_enable != "no") {
			$url = "discussion/project/{$params['entity']->guid}";
			$item = new ElggMenuItem('discussion', elgg_echo('discussion:group'), $url);
			$return[] = $item;
		}
	}

	return $return;
}

/**
 * Discussion page handler
 *
 * URLs take the form of
 *  All topics in site:    discussion/all
 *  List topics in forum:  discussion/owner/<guid>
 *  View discussion topic: discussion/view/<guid>
 *  Add discussion topic:  discussion/add/<guid>
 *  Edit discussion topic: discussion/edit/<guid>
 *
 * @param array $page Array of url segments for routing
 * @return bool
 */
function camerproject_discussion_page_handler($page) {

	if (!isset($page[0])) {
		$page[0] = 'all';
	}

	elgg_push_breadcrumb(elgg_echo('discussion'), 'discussion/all');

	switch ($page[0]) {
		case 'all':
			echo elgg_view_resource('discussion/all');
			break;
		case 'owner':
			echo elgg_view_resource('discussion/owner', [
				'guid' => elgg_extract(1, $page),
			]);
			break;
		case 'project':
			echo elgg_view_resource('discussion/group', [
				'guid' => elgg_extract(1, $page),
			]);
			break;
		case 'add':
			echo elgg_view_resource('discussion/add', [
				'guid' => elgg_extract(1, $page),
			]);
			break;
		case 'reply':
			switch (elgg_extract(1, $page)) {
				case 'edit':
					echo elgg_view_resource('discussion/reply/edit', [
						'guid' => elgg_extract(2, $page),
					]);
					break;
				case 'view':
					discussion_redirect_to_reply(elgg_extract(2, $page), elgg_extract(3, $page));
					break;
				default:
					return false;
			}
			break;
		case 'edit':
			echo elgg_view_resource('discussion/edit', [
				'guid' => elgg_extract(1, $page),
			]);
			break;
		case 'view':
			echo elgg_view_resource('discussion/view', [
				'guid' => elgg_extract(1, $page),
			]);
			break;
		default:
			return false;
	}
	return true;
}


/**
 * Add a menu item to the user ownerblock
 */
function camerproject_file_owner_block_menu($hook, $type, $return, $params) {
	if (elgg_instanceof($params['entity'], 'user')) {
		$url = "file/owner/{$params['entity']->username}";
		$item = new ElggMenuItem('file', elgg_echo('file'), $url);
		$return[] = $item;
	} else {
		if ($params['entity']->file_enable != "no") {
			$url = "file/project/{$params['entity']->guid}/all";
			$item = new ElggMenuItem('file', elgg_echo('file:group'), $url);
			$return[] = $item;
		}
	}

	return $return;
}


/**
 * Dispatches file pages.
 * URLs take the form of
 *  All files:       file/all
 *  User's files:    file/owner/<username>
 *  Friends' files:  file/friends/<username>
 *  View file:       file/view/<guid>/<title>
 *  New file:        file/add/<guid>
 *  Edit file:       file/edit/<guid>
 *  Group files:     file/group/<guid>/all
 *  Download:        file/download/<guid>
 *
 * Title is ignored
 *
 * @param array $page
 * @return bool
 */
function camerproject_file_page_handler($page) {

	if (!isset($page[0])) {
		$page[0] = 'all';
	}

	$page_type = $page[0];
	switch ($page_type) {
		case 'owner':
			file_register_toggle();
			echo elgg_view_resource('file/owner');
			break;
		case 'friends':
			file_register_toggle();
			echo elgg_view_resource('file/friends');
			break;
		case 'view':
			echo elgg_view_resource('file/view', [
				'guid' => $page[1],
			]);
			break;
		case 'add':
			echo elgg_view_resource('file/upload');
			break;
		case 'edit':
			echo elgg_view_resource('file/edit', [
				'guid' => $page[1],
			]);
			break;
		case 'search':
			file_register_toggle();
			echo elgg_view_resource('file/search');
			break;
		case 'project':
			file_register_toggle();
			echo elgg_view_resource('file/owner');
			break;
		case 'all':
			file_register_toggle();
			$dir = __DIR__ . "/views/" . elgg_get_viewtype();
			if (_elgg_view_may_be_altered('resources/file/world', "$dir/resources/file/world.php")) {
				elgg_deprecated_notice('The view "resources/file/world" is deprecated. Use "resources/file/all".', 2.3);
				echo elgg_view_resource('file/world', ['__shown_notice' => true]);
			} else {
				echo elgg_view_resource('file/all');
			}
			break;
		case 'download':
			elgg_deprecated_notice('/file/download page handler has been deprecated and will be removed. Use elgg_get_download_url() to build download URLs', '2.2');
			$dir = __DIR__ . "/views/" . elgg_get_viewtype();
			if (_elgg_view_may_be_altered('resources/file/download', "$dir/resources/file/download.php")) {
				// For BC with 2.0 if a plugin is suspected of using this view we need to use it.
				echo elgg_view_resource('file/download', [
					'guid' => $page[1],
				]);
			} else {
				$file = get_entity($page[1]);
				if (!$file instanceof ElggFile) {
					return false;
				}
				$download_url = elgg_get_download_url($file);
				if (!$download_url) {
					return false;
				}
				forward($download_url);
			}
			break;
		default:
			return false;
	}
	return true;
}


function camer_default_page_owner_handler($hook, $entity_type, $returnvalue, $params) {
	if ($returnvalue) {
		return $returnvalue;
	}

	$ia = elgg_set_ignore_access(true);

	$username = get_input("username");
	if ($username) {
		// @todo using a username of group:<guid> is deprecated
		if (substr_count($username, 'group:')) {
			preg_match('/group\:([0-9]+)/i', $username, $matches);
			$guid = $matches[1];
			if ($entity = get_entity($guid)) {
				elgg_set_ignore_access($ia);
				return $entity->getGUID();
			}
		}

		if ($user = get_user_by_username($username)) {
			elgg_set_ignore_access($ia);
			return $user->getGUID();
		}
	}

	$owner = get_input("owner_guid");
	if ($owner) {
		if ($user = get_entity($owner)) {
			elgg_set_ignore_access($ia);
			return $user->getGUID();
		}
	}

	// ignore root and query
	$uri = current_page_url();
	$path = str_replace(elgg_get_site_url(), '', $uri);
	$path = trim($path, "/");
	if (strpos($path, "?")) {
		$path = substr($path, 0, strpos($path, "?"));
	}

	// @todo feels hacky
	$segments = explode('/', $path);
	if (isset($segments[1]) && isset($segments[2])) {
		switch ($segments[1]) {
			case 'owner':
			case 'friends':
				$user = get_user_by_username($segments[2]);
				if ($user) {
					elgg_set_ignore_access($ia);
					return $user->getGUID();
				}
				break;
			case 'view':
			case 'edit':
				$entity = get_entity($segments[2]);
				if ($entity) {
					elgg_set_ignore_access($ia);
					return $entity->getContainerGUID();
				}
				break;
			case 'add':
			case 'group':
			case 'project':
				$entity = get_entity($segments[2]);
				if ($entity) {
					elgg_set_ignore_access($ia);
					return $entity->getGUID();
				}
				break;
		}
	}

	elgg_set_ignore_access($ia);
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
		case 'project':
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
		case 'needproject':
			switch (elgg_extract(1, $page)) {
				case 'all':
					echo elgg_view_resource('needproject/all');
					break;
				case 'list':
				case 'add':   //Add URL must be camerproject/needproject/add/{ParentGroup_GUID}
					elgg_set_page_owner_guid($page[2]);
					echo elgg_view_resource("needproject/{$page[1]}");
					break;
				case 'view':
					echo elgg_view_resource('needproject/view', [
						'guid' => elgg_extract(2, $page),
					]);
					break;
				case 'edit':   //Edit URL must be camerproject/needproject/edit/{GUID}/{ParentGroup_GUID}
					elgg_set_page_owner_guid($page[3]);
					echo elgg_view_resource('needproject/edit', [
						'guid' => elgg_extract(2, $page),
					]);
					break;
				default:
					return false;
			}
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
