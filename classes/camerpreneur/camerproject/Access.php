<?php

namespace Camerpreneur\camerproject;

/**
 * Description of Access
 *
 * @author Kana
 */
class Access {

 /**
 * Update all annotatations access_id to match the entity access
 *
 * @param string      $event  the name of the event
 * @param string      $type   the type of the event
 * @param \ElggObject $entity supplied entity
 *
 * @return void
 */
public static function updateAnnotationAccess($event, $type, $entity) {
		
        if (!($entity instanceof \Needproject)) {
                return;
        }

        $old_attributes = $entity->getOriginalAttributes();
        $old_access_id = elgg_extract('access_id', $old_attributes);
        if (!isset($old_access_id) || ($old_access_id === $entity->access_id)) {
                // nothing changed
                return;
        }

        /* @var $annotations_batch \ElggBatch */
        $annotations_batch = $entity->getAnnotations([
                'limit' => false,
                'batch' => true,
        ]);
        /* @var $annotation \ElggAnnotation */
        foreach ($annotations_batch as $annotation) {
                if ($annotation->access_id === $entity->access_id) {
                        continue;
                }

                $annotation->access_id = $entity->access_id;
                $annotation->save();
        }
}
	
/**
 * Set the options for the input/access on sectorindustry (announcement) edit form
 *
 * @param string $hook         the name of the hook
 * @param string $type         the type of the hook
 * @param array  $return_value current return value
 * @param array  $params       supplied params
 *
 * @return void|array
 */
public static function accessArray($hook, $type, $return_value, $params) {
		
    $input_params = elgg_extract('input_params', $params, []);
    if (empty($input_params)) {
            return;
    }

    $entity_subtype = elgg_extract('entity_subtype', $input_params);
    if (!in_array($entity_subtype, [\Needproject::SUBTYPE])) {
            return;
    }

    return [
            ACCESS_LOGGED_IN => elgg_echo('LOGGED_IN'),
            ACCESS_PUBLIC => elgg_echo('PUBLIC'),
            ACCESS_PRIVATE => elgg_echo("camerproject:access:project"),
    ];
    }   
}