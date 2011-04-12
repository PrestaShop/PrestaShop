<?php
/*
* 2007-2011 PrestaShop 
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
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision: 1.4 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AdminMessages extends AdminTab
{
	public function __construct()
	{
	 	global $cookie;
	 	$this->table = 'order';
	 	$this->className = 'Order';
	 	$this->view = 'noActionColumn';
		$this->colorOnBackground = true;

		$start = 0;
		$this->_defaultOrderBy = 'date_add';

		/* Manage default params values */
		if (empty($limit))
			$limit = ((!isset($cookie->{$this->table.'_pagination'})) ? $this->_pagination[0] : $limit = $cookie->{$this->table.'_pagination'});

		if (!Validate::isTableOrIdentifier($this->table))
			die (Tools::displayError('Table name is invalid:').' "'.$this->table.'"');

		if (empty($orderBy))
			$orderBy = Tools::getValue($this->table.'Orderby', $this->_defaultOrderBy);
		elseif ($orderBy == 'id_order')
			$orderBy = 'm.id_order';
		
		if (empty($orderWay))
			$orderWay = Tools::getValue($this->table.'Orderway', 'ASC');		

		$limit = (int)(Tools::getValue('pagination', $limit));
		$cookie->{$this->table.'_pagination'} = $limit;

		/* Check params validity */
		if (!Validate::isOrderBy($orderBy) OR !Validate::isOrderWay($orderWay)
			OR !is_numeric($start) OR !is_numeric($limit))
			die(Tools::displayError('get list params is not valid'));

		if ($orderBy == 'id_order')
			$orderBy = 'm.id_order';
		
		/* Determine offset from current page */
		if ((isset($_POST['submitFilter'.$this->table]) OR
		isset($_POST['submitFilter'.$this->table.'_x']) OR
		isset($_POST['submitFilter'.$this->table.'_y'])) AND
		!empty($_POST['submitFilter'.$this->table]) AND
		is_numeric($_POST['submitFilter'.$this->table]))
			$start = (int)($_POST['submitFilter'.$this->table] - 1) * $limit;

		$this->_list = Db::getInstance()->ExecuteS('
		SELECT SQL_CALC_FOUND_ROWS m.id_message, m.id_cart, m.id_employee, IF(m.id_order > 0, m.id_order, \'--\') id_order, m.message, m.private, m.date_add, CONCAT(LEFT(c.`firstname`, 1), \'. \', c.`lastname`) AS customer,
		c.id_customer, count(m.id_message) nb_messages, (SELECT message FROM '._DB_PREFIX_.'message WHERE id_order = m.id_order ORDER BY date_add DESC LIMIT 1) last_message,
		(SELECT COUNT(m2.id_message) FROM '._DB_PREFIX_.'message m2 WHERE 1 AND m2.id_customer != 0 AND m2.id_order = m.id_order AND m2.id_message NOT IN 
		(SELECT mr2.id_message FROM '._DB_PREFIX_.'message_readed mr2 WHERE mr2.id_employee = '.(int)($cookie->id_employee).') GROUP BY m2.id_order) nb_messages_not_read_by_me
		FROM '._DB_PREFIX_.'message m
		LEFT JOIN '._DB_PREFIX_.'orders o ON (o.id_order = m.id_order)
		LEFT JOIN '._DB_PREFIX_.'customer c ON (c.id_customer = m.id_customer)
		GROUP BY m.id_order
		ORDER BY '.(isset($orderBy) ? pSQL($orderBy) : 'date_add') .' '.(isset($orderWay) ? pSQL($orderWay) : 'DESC').'
		LIMIT '.(int)($start).','.(int)($limit));
		$this->_listTotal = Db::getInstance()->getValue('SELECT FOUND_ROWS()');

 		$this->fieldsDisplay = array(
		'id_order' => array('title' => $this->l('Order ID'), 'align' => 'center', 'width' => 30),
		'id_customer' => array('title' => $this->l('Customer ID'), 'align' => 'center', 'width' => 30),
		'customer' => array('title' => $this->l('Customer'), 'width' => 100, 'filter_key' => 'customer', 'tmpTableFilter' => true),
		'last_message' => array('title' => $this->l('Last message'), 'width' => 400, 'orderby' => false),
		'nb_messages_not_read_by_me' => array('title' => $this->l('Unread message(s)'), 'width' =>30, 'align' => 'center'),
		'nb_messages' => array('title' => $this->l('Number of messages'), 'width' => 30, 'align' => 'center'));

		parent::__construct();
	}

	public function display()
	{
		global $cookie, $currentIndex;

		if (isset($_GET['ajax']) && !empty($_GET['id_cart']))
		{
			ob_clean();
			
			$messages = Message::getMessagesByCartId(Tools::getValue('id_cart'), true);
			
			echo '
			<style type="text/css">
				* {
					font-size: 12px;
					font-family: Arial,Verdana,Helvetica,sans-serif;
				}
			</style>
			<p style="color: #CC0000; font-weight: bold;">'.$this->l('This customer has not finalized their order, however here are their messages:').'</p>';
			
			foreach ($messages AS $message)
			{				
				echo '
				<table cellpadding="5" border="1">
					<tr>
						<td>'.$this->l('Cart ID:').'</td>
						<td>'.(int)$message['id_cart'].'</td>
					</tr>
					<tr>
						<td>'.$this->l('Customer ID:').'</td>
						<td>'.(int)$message['id_customer'].'</td>
					</tr>
					<tr>
						<td>'.$this->l('Date:').'</td>
						<td>'.Tools::displayDate($message['date_add'], (int)$cookie->id_lang, true).'</td>
					</tr>
				</table>
				<p>'.$this->l('Message:').' '.Tools::htmlentitiesUTF8($message['message']).'</p>
				<hr size="1" noshade style="margin-bottom: 15px;" />';
			}

			die;
		}
		elseif (isset($_GET['view'.$this->table]) AND !empty($_GET['id_order']) AND $_GET['id_order'] != '--')
			Tools::redirectAdmin('index.php?tab=AdminOrders&id_order='.(int)($_GET['id_order']).'&vieworder'.'&token='.Tools::getAdminToken('AdminOrders'.(int)(Tab::getIdFromClassName('AdminOrders')).(int)($cookie->id_employee)));
		else
		{
			if (isset($_GET['id_order']) AND (empty($_GET['id_order']) OR $_GET['id_order'] == '--'))
			{
				echo '<p class="warning bold"><img src="../img/admin/warning.gif" alt="" class="middle" /> &nbsp;'.
				Tools::displayError('Cannot display this message because the customer has not finalized their order.').'</p>';
			}	
					
			foreach ($this->_list AS $k => &$item)
				if (Tools::strlen($item['last_message']) > 150 + Tools::strlen('...'))
					$this->_list[$k]['last_message'] = Tools::substr(html_entity_decode($item['last_message'], ENT_QUOTES, 'UTF-8'), 0, 150, 'UTF-8').'...';
					
			foreach ($this->_list AS $k => &$item)
				if ($item['id_order'] == '--')
					$this->_list[$k]['last_message'] .= ' <a class="iframe" onclick="$(this).parent().attr(\'onclick\', \'return false\');" href="'.$currentIndex.'&token='.Tools::getAdminToken('AdminMessages'.(int)(Tab::getIdFromClassName('AdminMessages')).(int)($cookie->id_employee)).'&ajax=1&id_cart='.(int)$this->_list[$k]['id_cart'].'" title="'.$this->l('View details').'"><img src="../img/admin/details.gif" alt="'.$this->l('View details').'" /></a>';
					
			echo '
			<link href="'._PS_CSS_DIR_.'jquery.fancybox-1.3.4.css" rel="stylesheet" type="text/css" media="screen" />
			<script type="text/javascript" src="'._PS_JS_DIR_.'jquery/jquery.fancybox-1.3.4.js"></script>
			<script type="text/javascript">
				$(document).ready(function()
				{
					$(\'a.iframe\').fancybox();
				});
			</script>';
					
			$this->displayList();
			$this->displayOptionsList();
		}
	}
}