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
*  @version  Release: $Revision: 6844 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * This class search Prestashop version from current database structure
 * 
 * @since 1.4.2.3
 * @todo (priority low) compare index keys updates ; compare types range ; add custom methods to search versions with no SQL updates
 */
class GetVersionFromDb
{
	/**
	 * @var array Because of some errors and differences between some upgrades files and some db.sql files, we need to ignore some fields.
	 */
	private $ignore = array(
		'blocklink' => array(
			'fields' => array('id_link', 'id_blocklink'),
		),
		'blocklink_lang' => array(
			'fields' => array('id_link', 'id_blocklink'),
			'keys' => array('primary'),
		),
		'cms_block' => array(
			'fields' => array('id_block_cms', 'id_cms_block', 'display_store'),
		),
		'cms_block_lang' => array(
			'fields' => array('id_block_cms', 'id_cms_block'),
		),
		'cms_block_page' => array(
			'fields' => array('id_block_cms_page', 'id_cms_block_page'),
		),
		'cms_lang' => array(
			'types' => array('content'),
		),
		'log_email' => array(
			'keys' => array('date_add', 'id_cart'),
		),
		'newsletter' => array(
			'fields' => array('http_referer'),
		),
		'order_detail' => array(
			'fields' => array('group_reduction', 'ecotax_tax_rate', 'id_currency', 'reduction_percent', 'reduction_amount'),
		),
		'webservice_account' => array(
			'fields' =>	array('is_module', 'module_name'),
		),
	);
	
	/**
	 * @var string Path to install directory
	 */
	private $installPath;
	
	/**
	 * @var array Store schemas of all upgrades
	 */
	private $schemas = array();
	
	/**
	 * @var array Store db.sql schema
	 */
	private $generalSchema = null;
	
	/**
	 * @var array Store current database schema
	 */
	private $currentSchema = null;
	
	/**
	 * @var bool If set to false, will compare all data of each table to get full error log
	 */
	private $fastExecution = false;
	
	/**
	 * @var bool If true, fields type will be compared too
	 */
	private $compareTypes = true;
	
	/**
	 * @var int Store microtime of the script execution
	 */
	private $totalTime = 0;
	
	/**
	 * @var array Error log
	 */
	private $errors = array();
	
	/**
	 * @var array Prefix for current database
	 */
	private $prefix;
	
	/**
	 * @var string Found versions
	 */
	private $versions = array();

	/**
	 * Constructor, launch the compare algorithm
	 * 
	 * @param bool $compareTypes If true, will compare field types
	 * @param bool $fastExecution If false, will loose some performance but log all errors
	 */
	public function __construct($compareTypes = true, $fastExecution = false)
	{
		// added for compatibility prestashop version < 1.3.7
		if(!defined('_PS_CACHE_ENABLED_'))
			define('_PS_CACHE_ENABLED_', '0');

		$this->installPath = INSTALL_PATH . '/';
		$this->prefix = _DB_PREFIX_;
		$this->compareTypes = $compareTypes;
		$this->fastExecution = $fastExecution;

		$start = microtime(true);
		$this->generateSchemasFromUpdates();
		$this->getDatabaseSchema();
		$this->versions = $this->searchCurrentVersion();
		$this->totalTime = microtime(true) - $start;
	}
	
	/**
	 * @return int Get total execution time of class
	 */
	public function getTotalTime()
	{
		return $this->totalTime;
	}
	
	/**
	 * @return array Get list of differences between current database and all upgrades
	 */
	public function getErrors($version = null)
	{
		if (!is_null($version))
		{
			return isset($this->errors[$version]) ? $this->errors[$version] : array();
		}
		return $this->errors;
	}
	
	/**
	 * @return string Get found versions
	 */
	public function getVersions()
	{
		return $this->versions;
	}
	
	/**
	 * Add more informations in database schema property in order to fix some troubles with missing data between db.sql and upgrades
	 */
	private function completeDatabaseSchema()
	{
		// Add key "discount" in "discount_category" table, because the name is different between the 1.2.2 upgrade and db.sql
		if (isset($this->currentSchema['discount_category']['@keys']['id_discount']))
			$this->currentSchema['discount_category']['@keys']['discount'] = $this->currentSchema['discount_category']['@keys']['id_discount'];
	}

	/**
	 * Get the schema of all updates
	 */
	private function generateSchemasFromUpdates()
	{
		// Get list of sorted upgrades
		$fd = opendir($this->installPath . 'sql/upgrade');
		while ($file = readdir($fd))
		{
			if (substr($file, -3, 3) == 'sql' && version_compare(substr($file, 0, -4), INSTALL_VERSION) <= 0)
			{
				$list[] = $file;
			}
		}
		closedir($fd);
		usort($list, 'version_compare');
		$list = array_reverse($list);
		
		for ($i = 0, $total = count($list); $i < $total - 1; $i++)
		{
			$file = $list[$i];
			$next = $list[$i + 1];

			$this->generateSchema(substr($file, 0, -4), substr($next, 0, -4));
		}
	}

	/**
	 * Generate the schema of the given version
	 * 
	 * @param string $currentVersion Version name
	 * @param string $nextVersion Next version
	 */
	private function generateSchema($currentVersion, $nextVersion)
	{
		static $storeTypes = array();
		
		// Load db.sql once
		if (is_null($this->generalSchema))
		{
			$this->loadGeneralSchema();
		}
		
		$this->schemas[] = array(
			'name' =>	$nextVersion,
			'struct' =>	array(),
		);
		
		// Read queries in upgrade files, and reverse structure from db.sql
		if (!file_exists($this->installPath . 'sql/upgrade/' . $currentVersion . '.sql'))
		{
			throw new Exception('File sql/upgrade/' . $currentVersion . '.sql not found');
		}
		
		$struct = (count($this->schemas) >= 2) ? $this->schemas[count($this->schemas) - 2]['struct'] : $this->generalSchema;
		$content = file_get_contents($this->installPath . 'sql/upgrade/' . $currentVersion . '.sql');
		$queries = array_reverse(preg_split("/;\s*[\r\n]+/", $content));
		foreach ($queries as $query)
		{
			$query = trim($query);
			
			// ALTER TABLE -> add, delete or update fields
			if (preg_match('#^alter\s+table\s+`?prefix_([a-z0-9_]+)`?\s+#si', $query, $m))
			{
				$table = $m[1];
				
				// Alter drop index
				preg_match_all('#\s*drop\s+(index|primary key)(\s+`?([a-z0-9_]+)`?)?#si', $query, $m);
				for ($i = 0, $total = count($m[0]); $i < $total; $i++)
				{
					$name = $m[3][$i];
					if (strtolower($m[1][$i]) == 'primary key')
					{
						$name = 'primary';
					}

					$struct[$table]['@keys'][$name] = '?';
					$query = str_replace($m[0][$i], '', $query);
				}
				
				// Alter add index
				preg_match_all('#\s*add\s+(index|primary key|key|unique key|unique)\s*\(?([a-z0-9_,`\s]+)\)?(\s+\(([a-z0-9_,` ]+)\))?\s*(,|;|$)#si', $query, $m);
				for ($i = 0, $total = count($m[0]); $i < $total; $i++)
				{
					$name = str_replace(array('`', ' '), array('', ''), $m[2][$i]);
					$tmpName = explode(',', $name);
					$name = $tmpName[0];
					
					if (strtolower($m[1][$i] == 'primary key'))
					{
						$name = 'primary';
					}

					if (isset($struct[$table]['@keys']) && !isset($struct[$table]['@keys'][$name]))
					{
						// If key name doesn't exist, we try to match with list of fields and type
						$type = strtolower($m[1][$i]);
						if ($type == 'primary key')
						{
							$type = 'primary';
						}
						else if ($type == 'key')
						{
							$type = 'index';
						}
						else if ($type == 'unique key')
						{
							$type = 'unique';
						}
						
						$fields = ($m[4][$i]) ? $m[4][$i] : $m[2][$i];
						$fields = explode(',', preg_replace('#[\s`]#si', '', $fields));

						foreach ($struct[$table]['@keys'] as $key => $data)
						{
							if ($data['type'] == $type && !array_diff($data['fields'], $fields))
							{
								// Found it !
								unset($struct[$table]['@keys'][$key]);
								break;
							}
						}
					}
					else
					{
						unset($struct[$table]['@keys'][$name]);
					}
					$query = str_replace($m[0][$i], '', $query);
				}
				
				// Alter add
				preg_match_all('#\s*add\s+`?([a-z0-9_]+)`?\s+(([a-z]+)(\([ 0-9,]+\))?(\s+unsigned)?)(\s+not)?(\s+null)?(\s+auto_increment)?(\s+primary)?#si', $query, $m);
				for ($i = 0, $total = count($m[0]); $i < $total; $i++)
				{
					unset($struct[$table][$m[1][$i]]);
					if (strtolower(trim($m[9][$i])) == 'primary')
					{
						unset($struct[$table]['@keys']['primary']);
					}
				}

				// Alter drop
				preg_match_all('#\s*drop\s+`?([a-z0-9_]+)`?#si', $query, $m);
				for ($i = 0, $total = count($m[0]); $i < $total; $i++)
				{
					$struct[$table][$m[1][$i]] = '?';
				}
				
				// Alter change
				preg_match_all('#\s*change\s+`([a-z0-9_]+)`\s+`?([a-z0-9_]+)`?\s+([a-z]+)(\s*\([ 0-9,]+\))?(\s+unsigned)?#si', $query, $m);
				for ($i = 0, $total = count($m[0]); $i < $total; $i++)
				{
					if ($m[1][$i] != $m[2][$i])
					{
						unset($struct[$table][$m[1][$i]]);
					}
					$struct[$table][$m[2][$i]] = '?';
				}
			}
			// CREATE TABLE -> delete this table from structure
			else if (preg_match('#^create\s+table\s+(if\s+not\s+exists\s+)?`?prefix_([a-z0-9_]+)`?#si', $query, $m) && !preg_match('#_tmp[0-9]?$#i', $m[2]))
			{
				unset($struct[$m[2]]);
			}
			// DROP TABLE -> add this table in structure
			else if (preg_match('#^drop\s*table\s+(if\s+exists\s+)?`?prefix_([a-z0-9_]+)`?#si', $query, $m) && !preg_match('#_tmp[0-9]?$#i', $m[2]))
			{
				$struct[$m[2]] = array();
			}
		}

		$this->schemas[count($this->schemas) - 1]['struct'] = $struct;
	}

	/**
	 * Read db.sql file and load tables structure
	 */
	private function loadGeneralSchema()
	{
		$this->generalSchema = array();
		if (!file_exists($this->installPath . 'sql/db.sql'))
		{
			throw new Exception('File sql/db.sql not found');
		}

		// Get create queries from db.sql file
		$content = file_get_contents($this->installPath . 'sql/db.sql');
		$queries = preg_split("/;\s*[\r\n]+/", $content);
		foreach ($queries as $query)
		{
			$query = trim($query);
			if (!preg_match('#^create table `?prefix_([a-z0-9_]+)`?\s#i', $query, $m))
			{
				continue;
			}

			$table = $m[1];
			$this->generalSchema[$table] = array();
			
			// Get fields list
			preg_match_all('#(\(|,)\s*`?([a-z0-9_]+)`?\s+(([a-z]+)(\([ 0-9,]+\))?(\s+unsigned)?)\s*[^,]*#si', $query, $m);
			for ($i = 0, $total = count($m[0]); $i < $total; $i++)
			{
				if (in_array(strtolower($m[2][$i]), array('primary', 'key', 'unique', 'index')))
				{
					continue;
				}
				$this->generalSchema[$table][$m[2][$i]] = $this->parseType($m[4][$i]);
			}
			
			// Get index
			$this->generalSchema[$table]['@keys'] = array();
			preg_match_all('#(primary\s+key|unique\s+key|key|index)\s+\(?`?([a-z0-9_,` ]+)`?\)?(\s+\(([a-z0-9_,` ]+)\))?\s*(,|\))#si', $query, $m);
			for ($i = 0, $total = count($m[0]); $i < $total; $i++)
			{
				$type = strtolower($m[1][$i]);
				if ($type == 'primary key')
				{
					$type = 'primary';
				}
				else if ($type == 'key')
				{
					$type = 'index';
				}
				else if ($type == 'unique key')
				{
					$type = 'unique';
				}
				
				$name = str_replace(array('`', ' '), array('', ''), $m[2][$i]);
				$tmpName = explode(',', $name);
				$name = $tmpName[0];
				if ($type == 'primary')
				{
					$name = 'primary';
				}

				$fields = ($m[4][$i]) ? $m[4][$i] : $m[2][$i];
				$fields = explode(',', preg_replace('#[\s`]#si', '', $fields));

				$this->generalSchema[$table]['@keys'][$name] = array(
					'type' =>	$type,
					'fields' =>	$fields,
				);
			}
		}

		$this->schemas[] = array(
			'name' =>	INSTALL_VERSION,
			'struct' =>	$this->generalSchema,
		);
	}

	/**
	 * Get the schema of current database
	 */
	private function getDatabaseSchema()
	{
		$struct = array();
		$sql = 'SHOW TABLES';
		foreach (Db::getInstance()->executeS($sql) as $row)
		{
			$table = current($row);
			if (substr($table, 0, strlen($this->prefix)) != $this->prefix)
			{
				continue;
			}
			
			$virtualTable = substr($table, strlen($this->prefix));
			$struct[$virtualTable] = array();

			// List fields
			$sql = 'SHOW FIELDS FROM ' . $table;
			foreach (Db::getInstance()->executeS($sql) as $rowField)
			{
				$struct[$virtualTable][$rowField['Field']] = $this->parseType($rowField['Type']);
			}
			
			// List keys
			$struct[$virtualTable]['@keys'] = array();
			$sql = 'SHOW INDEX FROM ' . $table;
			foreach (Db::getInstance()->executeS($sql) as $rowIndex)
			{
				$keyName = strtolower($rowIndex['Key_name']);
				$type = 'index';
				if ($keyName == 'primary')
				{
					$type = 'primary';
				}
				else if (!$rowIndex['Non_unique'])
				{
					$type = 'unique';
				}

				if (!isset($struct[$virtualTable]['@keys'][$keyName]))
				{
					$struct[$virtualTable]['@keys'][$keyName] = array(
						'type' =>	$type,
						'fields' =>	array(),
					);
				}
				$struct[$virtualTable]['@keys'][$keyName]['fields'][] = $rowIndex['Column_name'];
			}
		}

		$this->currentSchema = $struct;
		$this->completeDatabaseSchema();
	}

	/**
	 * Launch comparison and search current version
	 */
	private function searchCurrentVersion()
	{
		$this->errors = array();
		$version = null;
		$versions = array();
		$state = 0;

		// Browse all schema, we will compare all data of listed schema to current database structure, evertytime something is different
		// we break and go to next schema
		foreach ($this->schemas as $schema)
		{
			$struct = $schema['struct'];
			$this->errors[$schema['name']] = array();
			$isThisVersion = true;
			
			// Browse all table for schema
			foreach ($struct as $table => $fields)
			{
				// Is this table ignored ?
				if (isset($this->ignore[$table]) && !$this->ignore[$table])
				{
					continue;
				}

				// Check if table exists
				if (!isset($this->currentSchema[$table]))
				{
					$this->errors[$schema['name']][] = "Table '$table' not found";
					$isThisVersion = false;
					
					if ($this->fastExecution)
					{
						break;
					}
					continue;
				}
				
				// Browse all fields for this table
				foreach ($fields as $field => $type)
				{
					if ($field == '@keys')
					{
						continue;
					}
					
					// Is this field ignored ?
					if (isset($this->ignore[$table], $this->ignore[$table]['fields']) && in_array($field, $this->ignore[$table]['fields']))
					{
						continue;
					}

					// Check if field exists
					if (!isset($this->currentSchema[$table][$field]))
					{
						$this->errors[$schema['name']][] = "Field '$field' in table '$table' not found";
						$isThisVersion = false;
						
						if ($this->fastExecution)
						{
							break 2;
						}
						continue;
					}
					
					// Is this type ignored ?
					if (isset($this->ignore[$table], $this->ignore[$table]['types']) && in_array($field, $this->ignore[$table]['types']))
					{
						continue;
					}
					
					// Compare the field type, ignore comparaison if we don't know the field type (? char)
					if ($this->compareTypes && $type != '?' && $this->currentSchema[$table][$field] != $type)
					{
						$this->errors[$schema['name']][] = "The field '$field' in table '$table' has a different type ('$type' :: '{$this->currentSchema[$table][$field]}')";
						$isThisVersion = false;
						
						if ($this->fastExecution)
						{
							break 2;
						}
					}
				}
				
				// Compare index
				if (isset($fields['@keys']))
				{
					foreach ($fields['@keys'] as $name => $data)
					{
						if ($data == '?')
						{
							continue;
						}
						
						// Is this index ignored ?
						if (isset($this->ignore[$table], $this->ignore[$table]['keys']) && in_array($name, $this->ignore[$table]['keys']))
						{
							continue;
						}

						// Test if index exists
						if (!isset($this->currentSchema[$table]['@keys'][$name]))
						{
							$this->errors[$schema['name']][] = "Key '$name' in table '$table' not found";
							$isThisVersion = false;
							
							if ($this->fastExecution)
							{
								break 2;
							}
							continue;
						}
						
						// Compare index type
						if ($this->currentSchema[$table]['@keys'][$name]['type'] != $data['type'])
						{
							$this->errors[$schema['name']][] = "Key '$name' in table '$table' has different type ('{$this->currentSchema[$table]['@keys'][$name]['type']}', '{$data['type']}')";
							$isThisVersion = false;
							
							if ($this->fastExecution)
							{
								break 2;
							}
						}
						
						// Compare list of fields
						$diff = array_diff($this->currentSchema[$table]['@keys'][$name]['fields'], $data['fields']);
						if ($diff)
						{
							$this->errors[$schema['name']][] = "Key '$name' in table '$table' has different fields ('" . implode("', '", $diff) . "')";
							$isThisVersion = false;
							
							if ($this->fastExecution)
							{
								break 2;
							}
						}
					}
				}
			}
			
			if ($isThisVersion)
			{
				$versions[] = $schema['name'];
				$state = 1;
			}
			
			if (!$isThisVersion && $state == 1)
			{
				break;
			}
		}
		
		return ($versions);
	}
	
	/**
	 * Format correctly a field type name
	 * 
	 * @param string $type Type name
	 * @return Parsed type name
	 */
	private function parseType($type)
	{
		$type = strtolower(preg_replace('#^([a-z]+).*$#i', '\\1', $type));
		$type = str_replace('boolean', 'tinyint', $type);

		return $type;
	}
}
