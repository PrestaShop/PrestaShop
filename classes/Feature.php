<?php
/*
* 2007-2014 PrestaShop
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
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class FeatureCore extends ObjectModel
{
 	/** @var string Name */
	public $name;
	public $position;

	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'feature',
		'primary' => 'id_feature',
		'multilang' => true,
		'fields' => array(
			'position' => 	array('type' => self::TYPE_INT, 'validate' => 'isInt'),

			// Lang fields
			'name' => 		array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'required' => true, 'size' => 128),
		),
	);


	protected $webserviceParameters = array(
		'objectsNodeName' => 'product_features',
		'objectNodeName' => 'product_feature',
		'fields' => array(),
	);

	/**
	 * Get a feature data for a given id_feature and id_lang
	 *
	 * @param integer $id_lang Language id
	 * @param integer $id_feature Feature id
	 * @return array Array with feature's data
	 * @static
	 */
	public static function getFeature($id_lang, $id_feature)
	{
		return Db::getInstance()->getRow('
			SELECT *
			FROM `'._DB_PREFIX_.'feature` f
			LEFT JOIN `'._DB_PREFIX_.'feature_lang` fl
				ON ( f.`id_feature` = fl.`id_feature` AND fl.`id_lang` = '.(int)$id_lang.')
			WHERE f.`id_feature` = '.(int)$id_feature
		);
	}

	/**
	 * Get all features for a given language
	 *
	 * @param integer $id_lang Language id
	 * @return array Multiple arrays with feature's data
	 * @static
	 */
	public static function getFeatures($id_lang, $with_shop = true)
	{
		return Db::getInstance()->executeS('
		SELECT DISTINCT f.id_feature, f.*, fl.*
		FROM `'._DB_PREFIX_.'feature` f
		'.($with_shop ? Shop::addSqlAssociation('feature', 'f') : '').'
		LEFT JOIN `'._DB_PREFIX_.'feature_lang` fl ON (f.`id_feature` = fl.`id_feature` AND fl.`id_lang` = '.(int)$id_lang.')
		ORDER BY f.`position` ASC');
	}

	/**
	 * Delete several objects from database
	 *
	 * @param array $selection Array with items to delete
	 * @return boolean Deletion result
	 */
	public function deleteSelection($selection)
	{
		/* Also delete Attributes */
		foreach ($selection as $value)
		{
			$obj = new Feature($value);
			if (!$obj->delete())
				return false;
		}
		return true;
	}

	public function add($autodate = true, $nullValues = false)
	{
		if ($this->position <= 0)
			$this->position = Feature::getHigherPosition() + 1;

		$return = parent::add($autodate, true);
		Hook::exec('actionFeatureSave', array('id_feature' => $this->id));
		return $return;
	}

	public function delete()
	{
	 	/* Also delete related attributes */
		Db::getInstance()->execute('
			DELETE FROM `'._DB_PREFIX_.'feature_value_lang`
			WHERE `id_feature_value` IN (SELECT id_feature_value FROM `'._DB_PREFIX_.'feature_value` WHERE `id_feature` = '.(int)$this->id.')
		');
		Db::getInstance()->execute('
			DELETE FROM `'._DB_PREFIX_.'feature_value`
			WHERE `id_feature` = '.(int)$this->id
		);
		/* Also delete related products */
		Db::getInstance()->execute('
			DELETE FROM `'._DB_PREFIX_.'feature_product`
			WHERE `id_feature` = '.(int)$this->id
		);

		$return = parent::delete();
		if ($return)
			Hook::exec('actionFeatureDelete', array('id_feature' => $this->id));

		/* Reinitializing position */
		$this->cleanPositions();

		return $return;
	}

	public function update($nullValues = false)
	{
	 	$this->clearCache();

	 	$result = 1;
	 	$fields = $this->getFieldsLang();
		foreach ($fields as $field)
		{
			foreach (array_keys($field) as $key)
			 	if (!Validate::isTableOrIdentifier($key))
	 				die(Tools::displayError());

	 		$sql = 'SELECT `id_lang` FROM `'.pSQL(_DB_PREFIX_.$this->def['table']).'_lang`
	 				WHERE `'.$this->def['primary'].'` = '.(int)$this->id.'
	 					AND `id_lang` = '.(int)$field['id_lang'];
			$mode = Db::getInstance()->getRow($sql);
			$result &= (!$mode) ? Db::getInstance()->insert($this->def['table'].'_lang', $field) :
			Db::getInstance()->update(
				$this->def['table'].'_lang',
				$field,
				'`'.$this->def['primary'].'` = '.(int)$this->id.' AND `id_lang` = '.(int)$field['id_lang']
			);
		}
		Hook::exec('actionFeatureSave', array('id_feature' => $this->id));
		return $result;
	}

	/**
	* Count number of features for a given language
	*
	* @param integer $id_lang Language id
	* @return int Number of feature
	* @static
	*/
	public static function nbFeatures($id_lang)
	{
		return Db::getInstance()->getValue('
		SELECT COUNT(*) as nb
		FROM `'._DB_PREFIX_.'feature` ag
		LEFT JOIN `'._DB_PREFIX_.'feature_lang` agl 
		ON (ag.`id_feature` = agl.`id_feature` AND `id_lang` = '.(int)$id_lang.')
		');
	}

	/**
	* Create a feature from import
	*
	* @param integer $id_feature Feature id
	* @param integer $id_product Product id
	* @param array $value Feature Value
	*/
	public static function addFeatureImport($name, $position = false)
	{
		$rq = Db::getInstance()->getRow('
			SELECT `id_feature`
			FROM '._DB_PREFIX_.'feature_lang
			WHERE `name` = \''.pSQL($name).'\'
			GROUP BY `id_feature`
		');
		if (empty($rq))
		{
			// Feature doesn't exist, create it
			$feature = new Feature();
			$languages = Language::getLanguages();
			foreach ($languages as $language)
				$feature->name[$language['id_lang']] = strval($name);
			if ($position)
				$feature->position = (int)$position;
			else
				$feature->position = Feature::getHigherPosition() + 1;
			$feature->add();
			return $feature->id;
		}
		elseif(isset($rq['id_feature']) && $rq['id_feature'])
		{
			if (is_numeric($position) && $feature = new Feature((int)$rq['id_feature']))
			{
				$feature->position = (int)$position;
				if (Validate::isLoadedObject($feature))
					$feature->update();
			}

			return (int)$rq['id_feature'];
		}
	}

	public static function getFeaturesForComparison($list_ids_product, $id_lang)
	{
		if (!Feature::isFeatureActive())
			return false;

		$ids = '';
		foreach ($list_ids_product as $id)
			$ids .= (int)$id.',';

		$ids = rtrim($ids, ',');

		if (empty($ids))
			return false;

		return Db::getInstance()->executeS('
			SELECT * , COUNT(*) as nb
			FROM `'._DB_PREFIX_.'feature` f
			LEFT JOIN `'._DB_PREFIX_.'feature_product` fp
				ON f.`id_feature` = fp.`id_feature`
			LEFT JOIN `'._DB_PREFIX_.'feature_lang` fl
				ON f.`id_feature` = fl.`id_feature`
			WHERE fp.`id_product` IN ('.$ids.')
			AND `id_lang` = '.(int)$id_lang.'
			GROUP BY f.`id_feature`
			ORDER BY nb DESC
		');
	}

	/**
	 * This metohd is allow to know if a feature is used or active
	 * @since 1.5.0.1
	 * @return bool
	 */
	public static function isFeatureActive()
	{
		return Configuration::get('PS_FEATURE_FEATURE_ACTIVE');
	}

	/**
	 * Move a feature
	 * @param boolean $way Up (1)  or Down (0)
	 * @param integer $position
	 * @return boolean Update result
	 */
	public function updatePosition($way, $position, $id_feature = null)
	{
		if (!$res = Db::getInstance()->executeS('
			SELECT `position`, `id_feature`
			FROM `'._DB_PREFIX_.'feature`
			WHERE `id_feature` = '.(int)($id_feature ? $id_feature : $this->id).'
			ORDER BY `position` ASC'
		))
			return false;

		foreach ($res as $feature)
			if ((int)$feature['id_feature'] == (int)$this->id)
				$moved_feature = $feature;

		if (!isset($moved_feature) || !isset($position))
			return false;

		// < and > statements rather than BETWEEN operator
		// since BETWEEN is treated differently according to databases
		return (Db::getInstance()->execute('
			UPDATE `'._DB_PREFIX_.'feature`
			SET `position`= `position` '.($way ? '- 1' : '+ 1').'
			WHERE `position`
			'.($way
				? '> '.(int)$moved_feature['position'].' AND `position` <= '.(int)$position
				: '< '.(int)$moved_feature['position'].' AND `position` >= '.(int)$position))
		&& Db::getInstance()->execute('
			UPDATE `'._DB_PREFIX_.'feature`
			SET `position` = '.(int)$position.'
			WHERE `id_feature`='.(int)$moved_feature['id_feature']));
	}

	/**
	 * Reorder feature position
	 * Call it after deleting a feature.
	 *
	 * @return bool $return
	 */
	public static function cleanPositions()
	{
		//Reordering positions to remove "holes" in them (after delete for instance)
		$sql = "SELECT id_feature, position FROM "._DB_PREFIX_."feature ORDER BY position";
		$db = Db::getInstance();
		$r = $db->executeS($sql, false);
		$shiftTable = array(); //List of update queries (one query is necessary for each "hole" in the table)
		$currentDelta = 0;
		$minId = 0;
		$maxId = 0;
		$futurePosition = 0;
		while ($line = $db->nextRow($r))
		{
			$delta = $futurePosition - $line['position']; //Difference between current position and future position
			if ($delta != $currentDelta)
			{
				$shiftTable[] = array('minId' => $minId, 'maxId' => $maxId, 'delta' => $currentDelta);
				$currentDelta = $delta;
				$minId = $line['id_feature'];
			}
			$futurePosition++;
		}

		$shiftTable[] = array('minId' => $minId, 'delta' => $currentDelta);
		
		//Executing generated queries
		foreach ($shiftTable as $line)
		{
			$delta = $line['delta'];
			if ($delta == 0)
				continue;
			$delta = $delta > 0 ? '+'.(int)$delta : (int)$delta;
			$minId = $line['minId'];
			$sql = 'UPDATE '._DB_PREFIX_.'feature SET position = '.(int)$delta.' WHERE id_feature = '.(int)$minId;
			Db::getInstance()->execute($sql);
		}
	}

	/**
	 * getHigherPosition
	 *
	 * Get the higher feature position
	 *
	 * @return integer $position
	 */
	public static function getHigherPosition()
	{
		$sql = 'SELECT MAX(`position`)
				FROM `'._DB_PREFIX_.'feature`';
		$position = DB::getInstance()->getValue($sql);
		return (is_numeric($position)) ? $position : -1;
	}
}

