<?php

/**
 *  delete needproject
 */

$guid = (int) get_input('guid');
$entity = get_entity($guid);

if(!$entity instanceof Needproject){
    
    return elgg_error_response(elgg_echo("error"));
}

