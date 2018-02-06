<?php


/**
 * re/routes some urls that go through the groups handler
 */
function camer_groups_router($hook, $type, $return, $params) {
	breadcrumb_override($return);

	// subgroup options
	if ($return['segments'][0] == 'needproject') {
		elgg_load_library('elgg:groups');
		$group = get_entity($return['segments'][2]);
		if (!elgg_instanceof($group, 'group') || (($group->subgroups_enable == 'no') && ($return['segments'][1] != "delete"))) {
			return $return;
		}

		elgg_set_context('groups');
		elgg_set_page_owner_guid($group->guid);

		switch ($return['segments'][1]) {
			case 'add':
				$return = array(
					'identifier' => 'needproject',
					'handler' => 'needproject',
					'segments' => array(
						'add',
						$group->guid
					)
				);
				
				return $return;
				break;

			case 'delete':
				$return = array(
					'identifier' => 'au_subgroups',
					'handler' => 'au_subgroups',
					'segments' => array(
						'delete',
						$group->guid
					)
				);
				
				return $return;
				break;

			case 'list':
				$return = array(
					'identifier' => 'au_subgroups',
					'handler' => 'au_subgroups',
					'segments' => array(
						'list',
						$group->guid
					)
				);
				
				return $return;
				break;
		}
	}

	// need to redo closed/open tabs provided by group_tools - if it's installed
	if ($return['segments'][0] == 'all' && elgg_is_active_plugin('group_tools')) {
		$filter = get_input('filter', false);

		if (empty($filter) && ($default_filter = elgg_get_plugin_setting("group_listing", "group_tools"))) {
			$filter = $default_filter;
			set_input("filter", $default_filter);
		}

		if (in_array($filter, array("open", "closed", "alpha"))) {
			$return = array(
					'identifier' => 'au_subgroups',
					'handler' => 'au_subgroups',
					'segments' => array(
						'openclosed',
						$filter
					)
				);
				
			return $return;
		}
	}
}


