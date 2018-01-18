<?php

/**
 * Description of Camerproject
 *
 * @author Kana
 */

class Camerproject  extends ElggGroup{
   
    const SUBTYPE = 'camerproject';
    const AFFECTED_NEEDPROJECT = 'need_affected';
     
   
/**
* {@inheritDoc}
* @see ElggGroup::initializeAttributes()
*/
  protected function initializeAttributes() {
        parent::initializeAttributes();
        $site = elgg_get_site_entity();
        $this->attributes['subtype'] = self::SUBTYPE;
        $this->attributes['owner_guid'] = $site->guid;
        $this->attributes['container_guid'] = $site->guid;
    }
    
/**
 * {@inheritDoc}
 * @see ElggGroup::canComment()
 */
public function canComment($user_guid = 0, $default = null) {
        return false;
}

/**
 * {@inheritDoc}
 * @see ElggEntity::getURL()
 */
public function getURL() {
    
        $friendly_title = elgg_get_friendly_title($this->getDisplayName());
        
        return elgg_normalize_url("groups/profile/{$this->guid}/{$friendly_title}");
    
    
} 

/*
 *  forward url to add new need of project
 */
public function forUrl(){
    return elgg_normalize_url("needproject/add");
}

/**
 * Set the needproject affected by this camerproject
 *
 * @param int[] $needproject
 *
 * return bool
 */
 public function setNeedproject($needproject) {

        if (!is_array($needproject)) {
                return false;
        }
               
        $existing_needproject = $this->getNeedproject([
                'limit' => false,
                'callback' => function($row) {
                        return (int) $row->guid;
                },
        ]);

        $result = true;

        // remove needproject
        $remove_needproject = array_diff($existing_needproject, $needproject);
        foreach ($remove_needproject as $needproject_guid) {
                $result &= $this->removeRelationship($needproject_guid, self::AFFECTED_NEEDPROJECT);
        }

        // add new needproject
        $add_needproject = array_diff($needproject, $existing_needproject);
        foreach ($add_needproject as $needproject_guid) {
                $result &= $this->addRelationship($needproject_guid, self::AFFECTED_NEEDPROJECT);
        }

        return $result;
    }

/**
 * Return affected needproject
 *
 * @param array $options additional options for elgg_get_entities_from_relationship
 *
 * @see elgg_get_entities_from_relationship()
 *
 * @return bool|int|Needproject]
 */
    public function getNeedproject($options = []) {

        $defaults = [
                'type' => 'object',
                'subtype' => Needproject::SUBTYPE,
                'relationship' => self::AFFECTED_NEEDPROJECT,
        ];

        $options = array_merge($options, $defaults);

        return $this->getEntitiesFromRelationship($options);
    }
	
}