<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateSchemaCommand extends ContainerAwareCommand
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    private $metadata;

    private $dbName;

    private $dbPrefix;

    protected function configure()
    {
        $this
            ->setName('prestashop:schema:update-without-foreign')
            ->setDescription('Update the database');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $config = include __DIR__ . '/../../../app/config/parameters.php';
        if ($input->getOption('env') === 'test') {
            $this->dbName = 'test_' . $config['parameters']['database_name'];
        } else {
            $this->dbName = $config['parameters']['database_name'];
        }

        $this->dbPrefix = $config['parameters']['database_prefix'];

        $this->em = $this->getContainer()->get('doctrine')->getManager();
        $this->metadata = $this->em->getMetadataFactory()->getAllMetadata();

        $conn = $this->em->getConnection();
        $conn->beginTransaction();

        $output->writeln('Updating database schema...');
        $sqls = 0;

        // First drop any existing FK
        $query = $conn->query(
            'SELECT CONSTRAINT_NAME, TABLE_NAME 
                FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS
                WHERE CONSTRAINT_TYPE = "FOREIGN KEY" 
                    AND TABLE_SCHEMA = "' . $this->dbName . '"
                    AND TABLE_NAME LIKE "' . $this->dbPrefix . '%" '
        );

        $results = $query->fetchAll();
        foreach ($results as $result) {
            $drop = 'ALTER TABLE ' . $result['TABLE_NAME'] . ' DROP FOREIGN KEY ' . $result['CONSTRAINT_NAME'];
            $output->writeln('Executing: ' . $drop);
            $conn->executeQuery($drop);
            ++$sqls;
        }

        $schemaTool = new SchemaTool($this->em);
        $updateSchemaSql = $schemaTool->getUpdateSchemaSql($this->metadata, false);

        $removedTables = array();
        $dropForeignKeyQueries = array();

        // Remove the DROP TABLE
        foreach ($updateSchemaSql as $key => $sql) {
            $matches = array();
            if (preg_match('/DROP TABLE (.+?)$/', $sql, $matches)) {
                unset($updateSchemaSql[$key]);
                $removedTables[] = $matches[1];
            }
        }

        // Then remove the ALTER TABLE on removed tables
        foreach ($updateSchemaSql as $key => $sql) {
            $matches = array();
            if (preg_match('/ALTER TABLE (.+?) /', $sql, $matches)) {
                $alteredTables = $matches[1];
                if (in_array($alteredTables, $removedTables)) {
                    unset($updateSchemaSql[$key]);
                }
            }
        }

        // Remove duplicated DROP FOREIGN KEY
        foreach ($updateSchemaSql as $key => $sql) {
            if (preg_match('/ DROP FOREIGN KEY /', $sql)) {
                $hashedSql = md5($sql);
                if (in_array($hashedSql, $dropForeignKeyQueries)) {
                    unset($updateSchemaSql[$key]);
                } else {
                    $dropForeignKeyQueries[] = $hashedSql;
                }
            }
        }

        // Remove ADD CONSTRAINT
        foreach ($updateSchemaSql as $key => $sql) {
            if (preg_match('/ ADD CONSTRAINT /', $sql)) {
                unset($updateSchemaSql[$key]);
            }
        }

        $constraints = array();

        // Move DROP FOREIGN KEY at the beginning of the sql list
        foreach ($updateSchemaSql as $key => $sql) {
            if (preg_match('/ DROP FOREIGN KEY /', $sql)) {
                $constraints[] = $sql;
                unset($updateSchemaSql[$key]);
            }
        }

        foreach ($constraints as $constraint) {
            array_unshift($updateSchemaSql, $constraint);
        }

        // Put back DEFAULT fields, since it cannot be described in the ORM model
        foreach ($updateSchemaSql as $key => $sql) {
            $matches = array();
            if (preg_match('/ALTER TABLE (.+?) /', $sql, $matches)) {
                $tableName = $matches[1];
                $matches = array();
                if (preg_match_all('/([^\s,]*?) CHANGE (.+?) (.+?)(,|$)/', $sql, $matches)) {
                    foreach ($matches[2] as $matchKey => $fieldName) {
                        // remove table name
                        $matches[0][$matchKey] = preg_replace(
                            '/(.+?) CHANGE/',
                            ' CHANGE',
                            $matches[0][$matchKey]
                        );
                        // remove quote
                        $originalFieldName = $fieldName;
                        $fieldName = str_replace('`', '', $fieldName);
                        // get old default value
                        $query = $conn->query('SHOW FULL COLUMNS FROM ' . $tableName . ' WHERE Field="' . $fieldName . '"');
                        $results = $query->fetchAll();
                        $oldDefaultValue = $results[0]['Default'];
                        $extra = $results[0]['Extra'];
                        if ($oldDefaultValue !== null
                            && strpos($oldDefaultValue, 'CURRENT_TIMESTAMP') === false) {
                            $oldDefaultValue = "'" . $oldDefaultValue . "'";
                        }
                        if ($oldDefaultValue === null) {
                            $oldDefaultValue = 'NULL';
                        }
                        // set the old default value
                        if (!($results[0]['Null'] == 'NO' && $results[0]['Default'] === null)
                            && !($oldDefaultValue === 'NULL'
                                && strpos($matches[0][$matchKey], 'NOT NULL') !== false)
                            && (strpos($matches[0][$matchKey], 'BLOB') === false)
                            && (strpos($matches[0][$matchKey], 'TEXT') === false)
                        ) {
                            if (preg_match('/DEFAULT/', $matches[0][$matchKey])) {
                                $matches[0][$matchKey] =
                                    preg_replace('/DEFAULT (.+?)(,|$)/', 'DEFAULT ' .
                                        $oldDefaultValue . '$2' . ' ' . $extra, $matches[0][$matchKey]);
                            } else {
                                $matches[0][$matchKey] =
                                    preg_replace('/(.+?)(,|$)/uis', '$1 DEFAULT ' .
                                        $oldDefaultValue . ' ' . $extra . '$2', $matches[0][$matchKey]);
                            }
                        }
                        $updateSchemaSql[$key] = preg_replace(
                            '/ CHANGE ' . $originalFieldName . ' (.+?)(,|$)/uis',
                            $matches[0][$matchKey],
                            $updateSchemaSql[$key]
                        );
                    }
                }
            }
        }

        $sqls += count($updateSchemaSql);
        // Now execute the queries!
        foreach ($updateSchemaSql as $sql) {
            try {
                $output->writeln('Executing: ' . $sql);
                $conn->executeQuery($sql);
            } catch (\Exception $e) {
                $conn->rollBack();

                throw($e);
            }
        }
        $conn->commit();

        $pluralization = (1 > $sqls) ? 'query was' : 'queries were';
        $output->writeln(sprintf('Database schema updated successfully! "<info>%s</info>" %s executed', $sqls, $pluralization));
    }
}
