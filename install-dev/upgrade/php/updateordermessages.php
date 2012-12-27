<?php
/*
* 2007-2012 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2012 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

function updateordermessages()
{
	if ($messages = Db::getInstance()->executeS('SELECT id_message, message FROM '._DB_PREFIX_.'message'))
	{
        if(is_array($messages))
            foreach($messages as $message)
            {
                $sql = 'UPDATE '._DB_PREFIX_.'message SET message = \''.pSQL(html_entity_decode($message['message'], ENT_COMPAT, 'UTF-8')).'\' WHERE id_message = '.(int)$message['id_message'];
                Db::getInstance()->execute($sql);
            }
	}
    
	if ($messages = Db::getInstance()->executeS('SELECT id_customer_message, message FROM '._DB_PREFIX_.'customer_message'))
	{
        if(is_array($messages))
            foreach($messages as $message)
            {
                $sql = 'UPDATE '._DB_PREFIX_.'customer_message SET message = \''.pSQL(html_entity_decode(str_replace('&amp;', '&', $message['message']), ENT_COMPAT, 'UTF-8')).'\' WHERE id_customer_message = '.(int)$message['id_customer_message'];
                Db::getInstance()->execute($sql);
            }
	}    
}