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
*  @version  Release: $Revision: 7025 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class HookCore extends ObjectModel
{
	/**
	 * @var string Hook name identifier
	 */
	public $name;

	/**
	 * @var string Hook title (displayed in BO)
	 */
	public $title;

	/**
	 * @var string Hook description
	 */
	public $description;

	/**
	 * @var bool
	 */
	public $position = false;

	/**
	 * @var bool Is this hook usable with live edit ?
	 */
	public $live_edit = false;

	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'hook',
		'primary' => 'id_hook',
		'fields' => array(
			'name' => 			array('type' => self::TYPE_STRING, 'validate' => 'isHookName', 'required' => true, 'size' => 32),
			'title' => 			array('type' => self::TYPE_STRING),
			'description' => 	array('type' => self::TYPE_HTML),
			'position' => 		array('type' => self::TYPE_BOOL),
			'live_edit' => 		array('type' => self::TYPE_BOOL),
		),
	);

	/** @var array Cache Var */
	protected static $_hook_modules_cache = null;
	protected static $_hook_modules_cache_exec = null;
	static $_hook_alias = null;

	/**
	 * Preload hook alias list
	 *
	 * @return boolean preload_needed
	 */
	public static function preloadHookAlias()
	{
		if (!is_null(self::$_hook_alias))
			return false;
		self::$_hook_alias = array();
		$hook_alias_list = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'hook_alias`');
		if ($hook_alias_list)
			foreach ($hook_alias_list as $ha)
				self::$_hook_alias[strtolower($ha['alias'])] = $ha['name'];
		return true;
	}


	/**
	 * Return retrocompatible hook name
	 *
	 * @param string $hook_name Hook name
	 * @return integer Hook ID
	 */
	public static function getRetroHookName($hook_name)
	{
		$retro_hook_name = '';
		self::preloadHookAlias();
		if (isset(self::$_hook_alias[strtolower($hook_name)]))
			$retro_hook_name = self::$_hook_alias[strtolower($hook_name)];
		foreach (self::$_hook_alias as $alias => $name)
			if ($hook_name == $name)
				return $alias;
		return $retro_hook_name;
	}


	/**
	 * Return hook ID from name
	 *
	 * @param string $hook_name Hook name
	 * @return integer Hook ID
	 */
	public static function getIdByName($hook_name)
	{
	 	if (!Validate::isHookName($hook_name))
	 		die(Tools::displayError());

		$retro_hook_name = Hook::getRetroHookName($hook_name);

		$result = Db::getInstance()->getRow('
		SELECT `id_hook`, `name`
		FROM `'._DB_PREFIX_.'hook`
		WHERE `name` = \''.pSQL($hook_name).'\'
		OR `name` = \''.pSQL($retro_hook_name).'\'');

		return ($result ? $result['id_hook'] : false);
	}

	/**
	 * Return Hooks List
	 *
	 * @param integer $position
	 * @return array Hooks List
	 */
	public static function getHooks($position = false)
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT * FROM `'._DB_PREFIX_.'hook` h
		'.($position ? 'WHERE h.`position` = 1' : ''));
	}

	/**
	 * Preload hook modules cache
	 *
	 * @return boolean preload_needed
	 */
	public static function preloadHookModulesCache()
	{
		if (!is_null(self::$_hook_modules_cache))
			return false;

		self::$_hook_modules_cache = array();
		$sql = 'SELECT h.id_hook, h.name as h_name, title, description, h.position, live_edit, hm.position as hm_position, m.id_module, m.name, active
				FROM `'._DB_PREFIX_.'hook` h
				INNER JOIN `'._DB_PREFIX_.'hook_module` hm ON (h.id_hook = hm.id_hook)
				INNER JOIN `'._DB_PREFIX_.'module` as m    ON (m.id_module = hm.id_module)
				WHERE hm.id_shop IN('.implode(', ', Context::getContext()->shop->getListOfID()).')
				GROUP BY hm.id_hook, hm.id_module
				ORDER BY hm.position';
		$results = Db::getInstance()->executeS($sql);
		foreach ($results as $result)
		{
			if (!isset(self::$_hook_modules_cache[$result['id_hook']]))
				self::$_hook_modules_cache[$result['id_hook']] = array();

			self::$_hook_modules_cache[$result['id_hook']][$result['id_module']] = array(
				'id_hook' => $result['id_hook'],
				'title' => $result['title'],
				'description' => $result['description'],
				'hm.position' => $result['position'],
				'live_edit' => $result['live_edit'],
				'm.position' => $result['hm_position'],
				'id_module' => $result['id_module'],
				'name' => $result['name'],
				'active' => $result['active'],
			);
		}
		return true;
	}

	/**
	 * Return Hooks List
	 *
	 * @param integer $hookID
	 * @param integer $moduleID (optionnal)
	 * @return array Modules List
	 */
	public static function getModulesFromHook($hookID, $moduleID = null)
	{
		Hook::preloadHookModulesCache();

		$list = (isset(self::$_hook_modules_cache[$hookID])) ? self::$_hook_modules_cache[$hookID] : array();
		if ($moduleID)
			return (isset($list[$moduleID])) ? array($list[$moduleID]) : array();
		return $list;
	}

	/**
	 * Get Hook Module Cache Exec, testing hook name and retrocompatible hook name
	 *
	 * @param string $hook_name Hook Name
	 * @param string $retro_hook_name Retrocompatible Hook Name
	 * @return string cache
	 */
	public static function getHookModulesCacheExec($hook_name, $retro_hook_name)
	{
		$hook_name = strtolower($hook_name);
		$retro_hook_name = strtolower($retro_hook_name);

		$return = array();
		if (isset(self::$_hook_modules_cache_exec[$hook_name]))
			$return = array_merge((array)$return, (array)self::$_hook_modules_cache_exec[$hook_name]);
		if (isset(self::$_hook_modules_cache_exec[$retro_hook_name]))
			$return = array_merge((array)$return, (array)self::$_hook_modules_cache_exec[$retro_hook_name]);

		if (count($return) > 0)
			return $return;
		return false;
	}

	/**
	 * Execute modules for specified hook
	 *
	 * @param string $hook_name Hook Name
	 * @param array $hookArgs Parameters for the functions
	 * @return string modules output
	 */
	public static function exec($hook_name, $hookArgs = array(), $id_module = NULL)
	{
		// Check errors
		$context = Context::getContext();
		if ((!empty($id_module) && !Validate::isUnsignedId($id_module)) || !Validate::isHookName($hook_name))
			die(Tools::displayError());

		// Check if hook exists
		if (!Hook::getIdByName($hook_name))
			return false;

		// Get retrocompatible hook name
		$retro_hook_name = Hook::getRetroHookName($hook_name);

		// Hook list for live edit
		$ctrl = $context->controller;
		if ($ctrl && !in_array($hook_name, $ctrl->hook_list));
			$context->controller->hook_list[Hook::getIdByName($hook_name)] = $hook_name;

		$live_edit = false;
		if (!isset($hookArgs['cookie']) || !$hookArgs['cookie'])
			$hookArgs['cookie'] = $context->cookie;
		if (!isset($hookArgs['cart']) || !$hookArgs['cart'])
			$hookArgs['cart'] = $context->cart;

		// If hook modules cache exec is empty, we load it
		if (!isset(self::$_hook_modules_cache_exec))
		{
			// Get shops and groups list
			$db = Db::getInstance(_PS_USE_SQL_SLAVE_);
			$list = $context->shop->getListOfID();
			if (isset($context->customer) && $context->customer->isLogged())
				$groups = $context->customer->getGroups();

			// SQL Request
			$sql = 'SELECT h.`name` as hook, m.`id_module`, h.`id_hook`, m.`name` as module, h.`live_edit`
				FROM `'._DB_PREFIX_.'module` m
				LEFT JOIN `'._DB_PREFIX_.'hook_module` hm ON hm.`id_module` = m.`id_module`';
			if (isset($context->customer) && $context->customer->isLogged())
				$sql .= 'LEFT JOIN `'._DB_PREFIX_.'module_group` mg ON mg.`id_module` = m.`id_module`';
			$sql .= 'LEFT JOIN `'._DB_PREFIX_.'hook` h ON hm.`id_hook` = h.`id_hook`
				 WHERE (SELECT COUNT(*) FROM '._DB_PREFIX_.'module_shop ms WHERE ms.id_module = m.id_module AND ms.id_shop IN('.implode(', ', $list).')) = '.count($list).'
				 AND hm.id_shop IN('.implode(', ', $list).')';
			if (isset($context->customer) && $context->customer->isLogged())
				$sql .= 'AND (mg.`id_group` IN('.implode(', ', $groups).'))';
			$sql .= 'GROUP BY hm.id_hook, hm.id_module
				 ORDER BY hm.`position`';
			$results = $db->executeS($sql);

			// Init cache var
			self::$_hook_modules_cache_exec = array();
			if ($results)
				foreach ($results as $row)
				{
					$row['hook'] = strtolower($row['hook']);
					if (!isset(self::$_hook_modules_cache_exec[$row['hook']]))
						self::$_hook_modules_cache_exec[$row['hook']] = array();
					self::$_hook_modules_cache_exec[$row['hook']][] = array('id_hook' => $row['id_hook'], 'module' => $row['module'], 'id_module' => $row['id_module'], 'live_edit' => $row['live_edit']);
				}
		}

		// If no modules associated to hook_name or recompatible hook name, we stop the function
		if (Hook::getHookModulesCacheExec($hook_name, $retro_hook_name) === false)
			return '';

		// We load the cache of the hook
		$hookModulesCache = Hook::getHookModulesCacheExec($hook_name, $retro_hook_name);

		// Look on modules list
		$altern = 0;
		$output = '';
		foreach ($hookModulesCache as $array)
		{
			// Check errors
			if ($id_module && $id_module != $array['id_module'])
				continue;
			if (!($moduleInstance = Module::getInstanceByName($array['module'])))
				continue;

			// Check permissions
			$exceptions = $moduleInstance->getExceptions($array['id_hook']);
			if (in_array(Dispatcher::getInstance()->getController(), $exceptions))
				continue;
			if (isset($context->employee) && !$moduleInstance->getPermission('view', $context->employee))
				continue;

			// Check which / if method is callable
			$hook_callable = is_callable(array($moduleInstance, 'hook'.$hook_name));
			$hook_retro_callable = is_callable(array($moduleInstance, 'hook'.$retro_hook_name));
			if (($hook_callable || $hook_retro_callable) && Module::preCall($moduleInstance->name))
			{
				$hookArgs['altern'] = ++$altern;

				// Call hook method
				if ($hook_callable)
					$display = call_user_func(array($moduleInstance, 'hook'.$hook_name), $hookArgs);
				else if ($hook_retro_callable)
					$display = call_user_func(array($moduleInstance, 'hook'.$retro_hook_name), $hookArgs);

				// Live edit
				if ($array['live_edit'] && ((Tools::isSubmit('live_edit') && Tools::getValue('ad') && (Tools::getValue('liveToken') == sha1(Tools::getValue('ad')._COOKIE_KEY_)))))
				{
					$live_edit = true;
					$output .= '<script type="text/javascript"> modules_list.push(\''.$moduleInstance->name.'\');</script>
								<div id="hook_'.$array['id_hook'].'_module_'.$moduleInstance->id.'_moduleName_'.$moduleInstance->name.'"
								class="dndModule" style="border: 1px dotted red;'.(!strlen($display) ? 'height:50px;' : '').'">
								<span><img src="'._MODULE_DIR_.$moduleInstance->name.'/logo.gif">'
							 	.$moduleInstance->displayName.'<span style="float:right">
							 	<a href="#" id="'.$array['id_hook'].'_'.$moduleInstance->id.'" class="moveModule">
							 		<img src="'._PS_ADMIN_IMG_.'arrow_out.png"></a>
							 	<a href="#" id="'.$array['id_hook'].'_'.$moduleInstance->id.'" class="unregisterHook">
							 		<img src="'._PS_ADMIN_IMG_.'delete.gif"></span></a>
							 	</span>'.$display.'</div>';
				}
				else
					$output .= $display;
			}
		}

		// Return html string
		return ($live_edit ? '<script type="text/javascript">hooks_list.push(\''.$hook_name.'\'); </script><!--<div id="add_'.$hook_name.'" class="add_module_live_edit">
			<a class="exclusive" href="#">Add a module</a></div>--><div id="'.$hook_name.'" class="dndHook" style="min-height:50px">' : '').$output.($live_edit ? '</div>' : '');
	}

	public static function updateOrderStatus($newOrderStatusId, $id_order)
	{
		$order = new Order((int)($id_order));
		$newOS = new OrderState((int)($newOrderStatusId), $order->id_lang);

		$return = ((int)($newOS->id) == Configuration::get('PS_OS_PAYMENT')) ? Hook::exec('paymentConfirm', array('id_order' => (int)($order->id))) : true;
		$return = Hook::exec('updateOrderStatus', array('newOrderStatus' => $newOS, 'id_order' => (int)($order->id))) AND $return;
		return $return;
	}

	public static function postUpdateOrderStatus($newOrderStatusId, $id_order)
	{
		$order = new Order((int)($id_order));
		$newOS = new OrderState((int)($newOrderStatusId), $order->id_lang);
		$return = Hook::exec('postUpdateOrderStatus', array('newOrderStatus' => $newOS, 'id_order' => (int)($order->id)));
		return $return;
	}

	public static function orderConfirmation($id_order)
	{
		if (Validate::isUnsignedId($id_order))
		{
			$params = array();
			$order = new Order((int)$id_order);
			$currency = new Currency((int)$order->id_currency);

			if (Validate::isLoadedObject($order))
			{
				$cart = new Cart((int)$order->id_cart);
				$params['total_to_pay'] = $cart->getOrderTotal();
				$params['currency'] = $currency->sign;
				$params['objOrder'] = $order;
				$params['currencyObj'] = $currency;

				return Hook::exec('orderConfirmation', $params);
			}
		}
		return false;
	}

	public static function paymentReturn($id_order, $id_module)
	{
		if (Validate::isUnsignedId($id_order) AND Validate::isUnsignedId($id_module))
		{
			$params = array();
			$order = new Order((int)($id_order));
			$currency = new Currency((int)($order->id_currency));

			if (Validate::isLoadedObject($order))
			{
				$cart = new Cart((int)$order->id_cart);
				$params['total_to_pay'] = $cart->getOrderTotal();
				$params['currency'] = $currency->sign;
				$params['objOrder'] = $order;
				$params['currencyObj'] = $currency;

				return Hook::exec('paymentReturn', $params, (int)($id_module));
			}
		}
		return false;
	}

	public static function PDFInvoice($pdf, $id_order)
	{
		if (!is_object($pdf) OR !Validate::isUnsignedId($id_order))
			return false;
		return Hook::exec('PDFInvoice', array('pdf' => $pdf, 'id_order' => $id_order));
	}

	public static function backBeforePayment($module)
	{
		$params['module'] = strval($module);
		if (!$params['module'])
			return false;
		return Hook::exec('backBeforePayment', $params);
	}

	public static function updateCarrier($id_carrier, $carrier)
	{
		if (!Validate::isUnsignedId($id_carrier) OR !is_object($carrier))
			return false;
		return Hook::exec('updateCarrier', array('id_carrier' => $id_carrier, 'carrier' => $carrier));
	}

	/**
	 * Return hook ID from name
	 *
	 * @param string $hookName Hook name
	 * @return integer Hook ID
	 *
	 * @deprecated since 1.5
	 */
	public static function get($hookName)
	{
		Tools::displayAsDeprecated();
	 	if (!Validate::isHookName($hookName))
	 		die(Tools::displayError());

		$result = Db::getInstance()->getRow('
		SELECT `id_hook`, `name`
		FROM `'._DB_PREFIX_.'hook`
		WHERE `name` = \''.pSQL($hookName).'\'');

		return ($result ? $result['id_hook'] : false);
	}

	/**
	 * Called when quantity of a product is updated.
	 *
	 * @param Product
	 * @param Order
	 */
	public static function newOrder($cart, $order, $customer, $currency, $orderStatus)
	{
		Tools::displayAsDeprecated();
		return Hook::exec('newOrder', array(
			'cart' => $cart,
			'order' => $order,
			'customer' => $customer,
			'currency' => $currency,
			'orderStatus' => $orderStatus));
	}

	/**
	 * @deprecated 1.5.0
	 */
	public static function updateQuantity($product, $order = null)
	{
		Tools::displayAsDeprecated();
		return Hook::exec('updateQuantity', array('product' => $product, 'order' => $order));
	}

	/**
	 * @deprecated 1.5.0
	 */
	public static function productFooter($product, $category)
	{
		Tools::displayAsDeprecated();
		return Hook::exec('productFooter', array('product' => $product, 'category' => $category));
	}

	/**
	 * @deprecated 1.5.0
	 */
	public static function productOutOfStock($product)
	{
		Tools::displayAsDeprecated();
		return Hook::exec('productOutOfStock', array('product' => $product));
	}

	/**
	 * @deprecated 1.5.0
	 */
	public static function addProduct($product)
	{
		Tools::displayAsDeprecated();
		return Hook::exec('addProduct', array('product' => $product));
	}

	/**
	 * @deprecated 1.5.0
	 */
	public static function updateProduct($product)
	{
		Tools::displayAsDeprecated();
		return Hook::exec('updateProduct', array('product' => $product));
	}

	/**
	 * @deprecated 1.5.0
	 */
	public static function deleteProduct($product)
	{
		Tools::displayAsDeprecated();
		return Hook::exec('deleteProduct', array('product' => $product));
	}

	/**
	 * @deprecated 1.5.0
	 */
	public static function updateProductAttribute($id_product_attribute)
	{
		Tools::displayAsDeprecated();
		return Hook::exec('updateProductAttribute', array('id_product_attribute' => $id_product_attribute));
	}
}

