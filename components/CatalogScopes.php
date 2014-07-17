<?php
/** Created by griga at 07.11.13 | 15:55.
 * 
 */

class CatalogScopes {

    const WHOLESALE = 1;
    const RETAIL = 2;
    const ALL = 0;

    public static function getList(){
        return array(
            self::WHOLESALE => t('Опт'),
            self::RETAIL => t('Розница'),
            self::ALL => t('Опт и розница'),
        );
    }

    public static function getScopeName($type){
        $scopes = self::getList();
        return isset($scopes[$type]) ? $scopes[$type] : null;
    }
} 