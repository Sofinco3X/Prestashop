<?php
/**
* Sofinco PrestaShop Module
*
* Feel free to contact Verifone at support@paybox.com for any
* question.
*
* LICENSE: This source file is subject to the version 3.0 of the Open
* Software License (OSL-3.0) that is available through the world-wide-web
* at the following URI: http://opensource.org/licenses/OSL-3.0. If
* you did not receive a copy of the OSL-3.0 license and are unable
* to obtain it through the web, please send a note to
* support@e-transactions.fr so we can mail you a copy immediately.
*
*  @category  Module / payments_gateways
*  @version   2.1.0
*  @author    BM Services <contact@bm-services.com>
*  @copyright 2012-2016 Sofinco
*  @license   http://opensource.org/licenses/OSL-3.0
*  @link      http://www.e-transactions.fr/
*/

if (!defined('_PS_VERSION_')) {
    exit;
}
 
function upgrade_module_2_1_0($object)
{
    if (version_compare(_PS_VERSION_, '1.5', '>=')) {
        $sql = array();
        $sql[] = 'ALTER TABLE `'._DB_PREFIX_.'sofinco_order` ADD `initial_amount` int(10) unsigned NOT NULL';

        foreach ($sql as $query) {
            if (!Db::getInstance()->Execute($query)) {
                return false;
            }
        }
    }

    return $object->registerHook('actionObjectOrderUpdateAfter');
}
