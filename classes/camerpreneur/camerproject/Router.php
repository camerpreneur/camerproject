<?php

namespace Camerpreneur\camerproject;

/**
 * Description of Router
 *
 * @author Kana
 */
class Router {
    
/**
 * Handle /needproject URLs
 *
 * @param array $page URL segments
 *
 * @return bool
 */
public static function needproject($page) {

        $vars = [];

        switch (elgg_extract(0, $page)) {
                case 'all':

                        echo elgg_view_resource('needproject/all');
                        return true;

                        break;
                case 'add':

                        echo elgg_view_resource('needproject/add');
                        return true;

                        break;
                case 'view':

                        $vars['guid'] = (int) elgg_extract(1, $page);

                        echo elgg_view_resource('needproject/view', $vars);
                        return true;

                        break;
                case 'edit':

                        $vars['guid'] = (int) elgg_extract(1, $page);

                        echo elgg_view_resource('needproject/edit', $vars);
                        return true;

                        break;

                default:

                        forward('needproject/all');
                        break;
        }

        return false;
}


/**
 * Project page handler
 *
 * URLs take the form of
 *  All groups:           camerproject/all
 *  User's owned groups:  groups/owner/<username>
 *  User's member groups: groups/member/<username>
 *  Group profile:        groups/profile/<guid>/<title>
 *  New group:            groups/add/<guid>
 *  Edit group:           groups/edit/<guid>
 *  Group invitations:    groups/invitations/<username>
 *  Invite to group:      groups/invite/<guid>
 *  Membership requests:  groups/requests/<guid>
 *  Group activity:       groups/activity/<guid>
 *  Group members:        groups/members/<guid>
 *
 * @param array $page Array of url segments for routing
 * @return bool
 */
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
			if (elgg_view_exists("resources/groups/members/{$vars['sort']}")) {
				echo elgg_view_resource("groups/members/{$vars['sort']}", $vars);
			} else {
				echo elgg_view_resource('groups/members', $vars);
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

    
}
