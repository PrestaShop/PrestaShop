<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

/**
 * This script will alter the data model and content. Use on empty base only.
 *
 * $relations = array(
 *   TABLENAME => array(
 *      'field' => array(
 *          'foreign table' => 'foreign field',
 *       ),
 *    ),
 * );
 *
 * $changes = array(
 *   'tablename' => array(
 *     'field' => array(
 *       '#type' => 'type(size)',   // Mandatory, all other are optionals
 *       '#name' => 'newfieldname',
 *       '#unsigned' => true,
 *       '#null' => true,
 *     ),
 *   ),
 * );
 */

// Include PrestaShop configuration
include __DIR__ . '/../../config/config.inc.php';

// Include $changes
include __DIR__ . '/changes.php';
// Include $relations
include __DIR__ . '/relations.php';

/**
 * Clear all foreign keys.
 */
function clearForeignKeys()
{
    $tables = Db::getInstance()->executeS('
        SELECT DISTINCT `table_name`, `constraint_name`
        FROM `information_schema`.`key_column_usage`
        WHERE `constraint_schema` = \'' . _DB_NAME_ . '\'
        AND `referenced_table_name` IS NOT NULL
    ');
    if (is_array($tables)) {
        foreach ($tables as $table) {
            Db::getInstance()->execute(
                '
                ALTER TABLE `' . $table['table_name'] . '`
				DROP FOREIGN KEY `' . $table['constraint_name'] . '`'
            );
        }
    }

    echo 'Foreign keys cleared' . PHP_EOL;
}

/**
 * Set id_parent to id_self when id_parent = 0.
 *
 * @param string $table table name to process
 */
function noNullParent($table)
{
    $rows = Db::getInstance()->executeS(
        '
        SELECT `id_' . $table . '`
		FROM `' . _DB_PREFIX_ . $table . '`
		WHERE `id_parent` = 0'
    );
    if (is_array($rows)) {
        foreach ($rows as $row) {
            Db::getInstance()->execute(
                '
                UPDATE `' . _DB_PREFIX_ . $table . '`
				SET `id_parent` = ' . (int) $row['id_' . $table] . '
				WHERE `id_' . $table . '` = ' . (int) $row['id_' . $table]
            );
        }
    }
}

/**
 * Alter the data in order to comply with the Foreign key model.
 */
function updateMismatchForeign()
{
    // Update id_parent so the foreign key match
    noNullParent('tab');
    noNullParent('category');
    noNullParent('cms_category');

    // Make sure that the id_tax_rules_group is set
    Db::getInstance()->execute(
        '
        UPDATE `' . _DB_PREFIX_ . 'tax_rules_group`
		SET `id_tax_rules_group` = 1
		WHERE `id_tax_rules_group` = 0'
    );
    // Make sure the currency is set
    Db::getInstance()->execute(
        '
        UPDATE `' . _DB_PREFIX_ . 'country`
        SET `id_currency` = 1
		WHERE `id_currency` = 0'
    );

    // If there is no country, create a default one
    if (!Db::getInstance()->getValue(
        '
        SELECT COUNT(*)
		FROM `' . _DB_PREFIX_ . 'country`'
    )) {
        Db::getInstance()->execute(
            '
            INSERT INTO `' . _DB_PREFIX_ . 'country`
			(`id_country`, `id_state`) VALUES(0, 1)'
        );
    }

    echo 'Updated mismatch foreign keys' . PHP_EOL;
}

/**
 * Execute the queries.
 *
 * @param array $arrayOfQueries array of queries
 *
 * @note This function dies on error.
 */
function executeQueries($arrayOfQueries)
{
    foreach ($arrayOfQueries as $table => $queries) {
        foreach ($queries as $query) {
            try {
                Db::getInstance()->execute($query);
            }catch (Exception $e) {
                echo "Error:". $e->getMessage() . PHP_EOL;
            }

        }
    }
}

/**
 * Forge the changes queries.
 *
 * @param array $changes array describing the changes
 *
 * @return array of queries to be executed
 */
function forgeChangesQueries($changes)
{
    $queries = array();

    foreach ($changes as $table => $fields) {
        foreach ($fields as $field => $params) {
            $q = 'ALTER TABLE `' . _DB_PREFIX_ . $table . '` CHANGE ';
            if (!isset($params['#type'])) {
                echo 'Warning, change on ' . $table . '.' . $field .
                    ' ignored due to no type specified' . "\n";

                continue;
            }
            $q .= $field . ' ' . (isset($params['#name']) ?
                        $params['#name'] : $field . ' ' . $params['#type']);
            $q .= (isset($params['#unsigned']) && $params['#unsigned'] ?
                        ' UNSIGNED' : '');
            $q .= (isset($params['#null']) && $params['#null'] ?
                        ' NULL' : ' NOT NULL');
            $queries[$table][] = $q . ';';
        }
    }

    return $queries;
}

/**
 * Forge the Foreign keys queries.
 *
 * @param array $relations Array describing the foreign keys
 *
 * @return array of queries to be executed
 */
function forgeRelationsQueries($relations)
{
    $queries = array();

    foreach ($relations as $table => $fields) {
        foreach ($fields as $field => $foreign) {
            $q = 'ALTER TABLE `' . _DB_PREFIX_ . $table . '`
							ADD FOREIGN KEY (`' . $field . '`)';
            foreach ($foreign as $fTable => $fField) {
                $q .= ' REFERENCES `' . _DB_PREFIX_ . $fTable . '`(`' . $fField . '`)
								  ON DELETE NO ACTION ON UPDATE NO ACTION,';
            }
            $queries[$table][] = rtrim($q, ',') . ';';
        }
    }

    return $queries;
}

clearForeignKeys();
updateMismatchForeign();

if (isset($changes)) {
    executeQueries(forgeChangesQueries($changes));
}
if (isset($relations)) {
    executeQueries(forgeRelationsQueries($relations));
}

echo 'OK';
