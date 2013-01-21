<?php
if (!defined('_PS_VERSION_'))
	exit;
class TMHeaderlinks extends Module
{
	public function __construct()
	{
		$this->name = 'tmheaderlinks';
		$this->tab = 'front_office_features';
		$this->version = 1.5;
		$this->author = 'TM';
		$this->need_instance = 0;
		parent::__construct();
		$this->displayName = $this->l('TM Headerlinks block');
		$this->description = $this->l('Adds a block that displays permanent links such as sitemap, contact, etc.');
	}
	public function install()
	{
			return (parent::install() && $this->registerHook('top'));
	}
	public function hookTop($params)
	{
		return $this->display(__FILE__, 'tmheaderlinks.tpl');
	}
}