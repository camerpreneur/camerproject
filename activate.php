<?php
/**
 * This file is called during the activation of the plugin
 */
$subtypes = [
	'camerproject' => 'Camerproject',
	'needproject' => 'Needproject',
];
foreach ($subtypes as $subtype => $class) {
    
    if (!update_subtype('object', $subtype, $class)) {
        add_subtype('object', $subtype, $class);
    }
}
