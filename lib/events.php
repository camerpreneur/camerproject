<?php

function camerprojectpagesetup() {
	if (in_array(elgg_get_context(), array('au_subgroups', 'group_profile'))) {
		$group = elgg_get_page_owner_entity();
		$any_member = ($group->subgroups_members_create_enable != 'no');
		if (elgg_instanceof($group, 'group') && $group->subgroups_enable != 'no') {

			if (($any_member && $group->isMember()) || $group->canEdit()) {
				// register our title menu
				elgg_register_menu_item('title', array(
					'name' => 'add_subgroup',
					'href' => "camerproject/needproject/add/{$group->guid}",
					'text' => elgg_echo('au_subgroups:add:subgroup'),
					'link_class' => 'elgg-button elgg-button-action'
				));
			}
		}
	}
}

