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
	 * @var array List of executed hooks on this page
	 */
	public static $executed_hooks = array();

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

	/**
	 * @deprecated 1.5.0
	 */
	protected static $_hook_modules_cache = null;

	/**
	 * @deprecated 1.5.0
	 */
	protected static $_hook_modules_cache_exec = null;

	/**
	 * Return Hooks List
	 *
	 * @param bool $position
	 * @return array Hooks List
	 */
	public static function getHooks($position = false)
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT * FROM `'._DB_PREFIX_.'hook` h
			'.($position ? 'WHERE h.`position` = 1' : '')
		);
	}

	/**
	 * Return hook ID from name
	 *
	 * @param string $hook_name Hook name
	 * @return int Hook ID
	 */
	public static function getIdByName($hook_name)
	{
		if (!Validate::isHookName($hook_name))
			throw new PrestaShopException('Invalid hook name');

		$cache_id = 'hook_idbyname_'.$hook_name;
		if (!Cache::isStored($cache_id))
		{
			$retro_hook_name = Hook::getRetroHookName($hook_name);
			Cache::store($cache_id, Db::getInstance()->getValue('
				SELECT `id_hook`
				FROM `'._DB_PREFIX_.'hook`
				WHERE `name` = \''.pSQL($hook_name).'\'
					OR `name` = \''.pSQL($retro_hook_name).'\'
			'));
		}

		return Cache::retrieve($cache_id);
	}

	/**
	 * Get list of hook alias
	 *
	 * @since 1.5.0
	 * @return array
	 */
	public static function getHookAliasList()
	{
		$cache_id = 'hook_alias';
		if (!Cache::isStored($cache_id))
		{
			$hook_alias_list = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'hook_alias`');
			$hook_alias = array();
			if ($hook_alias_list)
				foreach ($hook_alias_list as $ha)
					$hook_alias[strtolower($ha['alias'])] = $ha['name'];
			Cache::store($cache_id, $hook_alias);
		}
		return Cache::retrieve($cache_id);
	}

	/**
	 * Return retrocompatible hook name
	 *
	 * @since 1.5.0
	 * @param string $hook_name Hook name
	 * @return int Hook ID
	 */
	public static function getRetroHookName($hook_name)
	{
		$alias_list = Hook::getHookAliasList();
		if (isset($alias_list[strtolower($hook_name)]))
			return $alias_list[strtolower($hook_name)];

		$retro_hook_name = array_search($hook_name, $alias_list);
		if ($retro_hook_name === false)
			return '';
		return $retro_hook_name;
	}

	/**
	 * Get list of all registered hooks with modules
	 *
	 * @since 1.5.0
	 * @return array
	 */
	public static function getHookModuleList()
	{
		$cache_id = 'hook_module_list';
		if (!Cache::isStored($cache_id))
		{
			$sql = 'SELECT h.id_hook, h.name as h_name, title, description, h.position, live_edit, hm.position as hm_position, m.id_module, m.name, active
					FROM `'._DB_PREFIX_.'hook` h
					INNER JOIN `'._DB_PREFIX_.'hook_module` hm ON (h.id_hook = hm.id_hook)
					INNER JOIN `'._DB_PREFIX_.'module` as m    ON (m.id_module = hm.id_module)
					WHERE hm.id_shop IN('.implode(', ', Context::getContext()->shop->getListOfID()).')
					GROUP BY hm.id_hook, hm.id_module
					ORDER BY hm.position';
			$results = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
			$list = array();
			foreach ($results as $result)
			{
				if (!isset($list[$result['id_hook']]))
					$list[$result['id_hook']] = array();

				$list[$result['id_hook']][$result['id_module']] = array(
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
			Cache::store($cache_id, $list);

			// @todo remove this in 1.6, we keep it in 1.5 for retrocompatibility
			Hook::$_hook_modules_cache = $list;
		}

		return Cache::retrieve($cache_id);
	}

	/**
	 * Return Hooks List
	 *
	 * @since 1.5.0
	 * @param int $id_hook
	 * @param int $id_module
	 * @return array Modules List
	 */
	public static function getModulesFromHook($id_hook, $id_module = null)
	{
		$hm_list = Hook::getHookModuleList();
		$module_list = (isset($hm_list[$id_hook])) ? $hm_list[$id_hook] : array();

		if ($id_module)
			return (isset($module_list[$id_module])) ? array($module_list[$id_module]) : array();
		return $module_list;
	}

	/**
	 * Get list of modules we can execute per hook
	 *
	 * @since 1.5.0
	 * @param string $hook_name Get list of modules for this hook if given
	 * @return array
	 */
	public static function getHookModuleExecList($hook_name = null)
	{
		$context = Context::getContext();
		$cache_id = 'hook_module_exec_list'.((isset($context->customer)) ? '_'.$context->customer->id : '');
		if (!Cache::isStored($cache_id))
		{
			// Get shops and groups list
			$shop_list = $context->shop->getListOfID();
			if (isset($context->customer) && $context->customer->isLogged())
				$groups = $context->customer->getGroups();

			// SQL Request
			$sql = new DbQuery();
			$sql->select('h.`name` as hook, m.`id_module`, h.`id_hook`, m.`name` as module, h.`live_edit`');
			$sql->from('module', 'm');
			$sql->leftJoin('hook_module', 'hm', 'hm.`id_module` = m.`id_module`');
			$sql->leftJoin('hook', 'h', 'hm.`id_hook` = h.`id_hook`');
			$sql->where('(SELECT COUNT(*) FROM '._DB_PREFIX_.'module_shop ms WHERE ms.id_module = m.id_module AND ms.id_shop IN('.implode(', ', $shop_list).')) = '.count($shop_list));
			$sql->where('hm.id_shop IN('.implode(', ', $shop_list).')');

			if (isset($context->customer) && $context->customer->isLogged())
			{
				$sql->leftJoin('module_group', 'mg', 'mg.`id_module` = m.`id_module`');
				$sql->where('mg.`id_group` IN('.implode(', ', $groups).')');
			}

			$sql->groupBy('hm.id_hook, hm.id_module');
			$sql->orderBy('hm.`position`');

			// Store results per hook name
			$results = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
			$list = array();
			if ($results)
				foreach ($results as $row)
				{
					$row['hook'] = strtolower($row['hook']);
					if (!isset($list[$row['hook']]))
						$list[$row['hook']] = array();

					$list[$row['hook']][] = array(
						'id_hook' => $row['id_hook'],
						'module' => $row['module'],
						'id_module' => $row['id_module'],
						'live_edit' => $row['live_edit'],
					);
				}

			Cache::store($cache_id, $list);

			// @todo remove this in 1.6, we keep it in 1.5 for retrocompatibility
			self::$_hook_modules_cache_exec = $list;
		}
		else
			$list = Cache::retrieve($cache_id);

		// If hook_name is given, just get list of modules for this hook
		if ($hook_name)
		{
			$retro_hook_name = Hook::getRetroHookName($hook_name);
			$hook_name = strtolower($hook_name);

			$return = array();
			if (isset($list[$hook_name]))
				$return = $list[$hook_name];
			if (isset($list[$retro_hook_name]))
				$return = array_merge($return, $list[$retro_hook_name]);

			if (count($return) > 0)
				return $return;
			return false;
		}
		else
			return $list;
	}

	/**
	 * Execute modules for specified hook
	 *
	 * @param string $hook_name Hook Name
	 * @param array $hookArgs Parameters for the functions
	 * @param int $id_module Execute hook for this module only
	 * @return string modules output
	 */
	public static function exec($hook_name, $hookArgs = array(), $id_module = null)
	{
		// Check arguments validity
		$context = Context::getContext();
		if (($id_module && !Validate::isUnsignedId($id_module)) || !Validate::isHookName($hook_name))
			throw new PrestaShopException('Invalid id_module or hook_name');

		// Check if hook exists
		if (!$id_hook = Hook::getIdByName($hook_name))
			return false;
		$retro_hook_name = Hook::getRetroHookName($hook_name);

		// Store list of executed hooks on this page
		Hook::$executed_hooks[$id_hook] = $hook_name;

		$live_edit = false;
		if (!isset($hookArgs['cookie']) || !$hookArgs['cookie'])
			$hookArgs['cookie'] = $context->cookie;
		if (!isset($hookArgs['cart']) || !$hookArgs['cart'])
			$hookArgs['cart'] = $context->cart;

		// If no modules associated to hook_name or recompatible hook name, we stop the function
		if (!$module_list = Hook::getHookModuleExecList($hook_name))
			return '';

		// Look on modules list
		$altern = 0;
		$output = '';
		foreach ($module_list as $array)
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
								<span style="font-family: Georgia;
font-size:13px;
font-style:italic;"><img style="padding-right:5px;" src="'._MODULE_DIR_.$moduleInstance->name.'/logo.gif">'
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



	/**
	 * @deprecated 1.5.0
	 */
	public static function updateOrderStatus($newOrderStatusId, $id_order)
	{
		Tools::displayAsDeprecated();
		$order = new Order((int)($id_order));
		$newOS = new OrderState((int)($newOrderStatusId), $order->id_lang);

		$return = ((int)($newOS->id) == Configuration::get('PS_OS_PAYMENT')) ? Hook::exec('paymentConfirm', array('id_order' => (int)($order->id))) : true;
		$return = Hook::exec('updateOrderStatus', array('newOrderStatus' => $newOS, 'id_order' => (int)($order->id))) && $return;
		return $return;
	}

	/**
	 * @deprecated 1.5.0
	 */
	public static function postUpdateOrderStatus($newOrderStatusId, $id_order)
	{
		Tools::displayAsDeprecated();
		$order = new Order((int)($id_order));
		$newOS = new OrderState((int)($newOrderStatusId), $order->id_lang);
		$return = Hook::exec('postUpdateOrderStatus', array('newOrderStatus' => $newOS, 'id_order' => (int)($order->id)));
		return $return;
	}

	/**
	 * @deprecated 1.5.0
	 */
	public static function orderConfirmation($id_order)
	{
		Tools::displayAsDeprecated();
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

	/**
	 * @deprecated 1.5.0
	 */
	public static function paymentReturn($id_order, $id_module)
	{
		Tools::displayAsDeprecated();
		if (Validate::isUnsignedId($id_order) && Validate::isUnsignedId($id_module))
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

	/**
	 * @deprecated 1.5.0
	 */
	public static function PDFInvoice($pdf, $id_order)
	{
		Tools::displayAsDeprecated();
		if (!is_object($pdf) || !Validate::isUnsignedId($id_order))
			return false;
		return Hook::exec('PDFInvoice', array('pdf' => $pdf, 'id_order' => $id_order));
	}

	/**
	 * @deprecated 1.5.0
	 */
	public static function backBeforePayment($module)
	{
		Tools::displayAsDeprecated();
		$params['module'] = strval($module);
		if (!$params['module'])
			return false;
		return Hook::exec('backBeforePayment', $params);
	}

	/**
	 * @deprecated 1.5.0
	 */
	public static function updateCarrier($id_carrier, $carrier)
	{
		Tools::displayAsDeprecated();
		if (!Validate::isUnsignedId($id_carrier) || !is_object($carrier))
			return false;
		return Hook::exec('updateCarrier', array('id_carrier' => $id_carrier, 'carrier' => $carrier));
	}

	/**
	 * Preload hook modules cache
	 *
	 * @deprecated 1.5.0 use Hook::getHookModuleList() instead
	 * @return boolean preload_needed
	 */
	public static function preloadHookModulesCache()
	{
		Tools::displayAsDeprecated();

		if (!is_null(self::$_hook_modules_cache))
			return false;

		self::$_hook_modules_cache = Hook::getHookModuleList();
		return true;
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

