<?php
/*
* 2007-2015 PrestaShop
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
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/
class Core_Foundation_Database_EntityManager
{
	private $db;
	private $configuration;

	public function __construct(
        Core_Foundation_Database_Database $db,
        Core_Business_Configuration $configuration
    )
    {
		$this->db = $db;
		$this->configuration = $configuration;
    }

	public function getRepository($className)
	{
        if (property_exists($className, 'repositoryClass')) {
            $repositoryClass = $className::$repositoryClass;
        } else {
            $repositoryClass = 'Core_Foundation_Database_EntityRepository';
        }

        $repository = new $repositoryClass(
			$this->db,
			$this->configuration->get('_DB_PREFIX_'),
			$className
		);

		return $repository;
	}
	/**
	 * @return mixed
	 * @throws PrestaShopExceptionCore
	 */
	public function createNewRecord()
	{
		if (is_null($this->component))
			throw new PrestaShopExceptionCore('No repository currently in use! use getRepository() to load one !');
		$entity_name = $this->component.'Entity';
		return new $entity_name;
	}
	/**
	 * @TODO: Make it more generics to allow to get record by any column possible :)
	 * @param $id
	 * @return mixed
	 * @throws PrestaShopExceptionCore
	 */
	public function getRecordByID($id, $legacy = false)
	{
		if (is_null($this->component))
			throw new PrestaShopExceptionCore('No repository currently in use! use getRepository() to load one !');
		$entity_name = $this->component.($legacy ? '' : 'Entity');
		return new $entity_name($id);
	}
}
