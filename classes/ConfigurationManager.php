<?php


class ConfigurationManager {


	/**
	 * Get a single configuration value (in one language only)
	 *
	 * @param string $key Key wanted
	 * @param integer $id_lang Language ID
	 * @return string Value
	 */
	public function get($key, $id_lang = null, $id_shop_group = null, $id_shop = null)
	{
		return Configuration::get($key, $id_lang, $id_shop_group, $id_shop);
	}
}