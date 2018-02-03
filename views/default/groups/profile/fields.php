<?php
/**
 * Project profile fields
 */

$camerproject = $vars['entity'];
$owner = $camerproject->getOwnerEntity();
$profile_fields = elgg_get_config('group');

if (is_array($profile_fields) && count($profile_fields) > 0) {

	$even_odd = 'even';
	foreach ($profile_fields as $key => $valtype) {
		// do not show the name
		if ($key == 'name') {
			continue;
		}

		$value = $camerproject->$key;
		
                if (is_null($value)) {
			continue;
		}
		$options = array('value' => $camerproject->$key);
		
                if ($valtype == 'tags') {
			$options['tag_names'] = $key;
		}
                
                if($key == 'progress'){
                    $options = ["value" => elgg_echo("camerproject:projectstatus:title:$value") ];                   
                }
                
                if( $key == 'industry'){
                    
                    $options = ["value" => elgg_echo("camerproject:projectindustrysector:name:$value") ];                   
                }
                
                if($key == 'currency'){
                    
                    $options = ["value" => elgg_echo("camerproject:projectcurrency:name:$value") ];                   
                }
                
                if($key == 'turnover'){
                    
                    $options = ["value" => elgg_echo("camerproject:turnover:$value") ];                   
                }
                
                if($key == 'offertype'){
                    
                    $options = ["value" => elgg_echo("camerproject:offertype:$value") ];                   
                }
                if($key == 'typemark'){
                    
                    $options = ["value" => elgg_echo("camerproject:typermark:$value") ];                   
                }
                
                if($key == 'markettype'){
                    
                    $options = ["value" => elgg_echo("camerproject:markettype:$value") ];                   
                }
                
                if($key == 'activity'){
                    
                    $options = ["value" => elgg_echo("camerproject:activity:$value") ];                   
                }
                echo "<div class=\"{$even_odd}\">";
		echo "<b>";
		echo elgg_echo("camerproject:$key");
		echo ": </b>";
		echo elgg_view("output/$valtype", $options);
		echo "</div>";

		$even_odd = ($even_odd == 'even') ? 'odd' : 'even';
	}
        
    if($owner->guid == elgg_get_logged_in_user_guid()){          
        echo "<div class=\"elgg-module\">";
        //echo "<div class=\"elgg-head\">";
        echo elgg_view('output/url', array(
            'text' => elgg_echo('camerproject:needproject:add'),
            'href' => elgg_get_site_url() . "needproject/add",
            'link_class' => 'elgg-button elgg-button-action'
            ));
       // echo "</div>";
        echo "</div>";
        
        
    }
 
  
}
