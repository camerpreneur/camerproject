<?php
/**
 * Description of Needproject
 *
 * @author Kana
 */

class Needproject extends ElggObject {
       
    const SUBTYPE = 'needproject';
    
/**
* {@inheritDoc}
* @see ElggObject::initializeAttributes()
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
 * @see ElggObject::canComment()
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
        
        return elgg_normalize_url("needproject/view/{$this->guid}/{$friendly_title}");
}

}

