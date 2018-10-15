<?php
/**
 * 2007-2018 PrestaShop.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\SqlManager;

use ErrorException;
use PrestaShop\PrestaShop\Adapter\Entity\RequestSql;
use PrestaShopDatabaseException;

/**
 * Class SqlQueryValidator is responsible for validating Request SQL model data.
 */
class SqlQueryValidator
{
    /**
     * Check if SQL is valid for Reqest SQL model.
     * Only "Select" sqls should be valid.
     *
     * @param string $sql
     *
     * @return array Array of errors if any
     */
    public function validate($sql)
    {
        $errors = [];

        try {
            $requestSql = new RequestSql();
            $parser = $requestSql->parsingSql($sql);
            $validate = $requestSql->validateParser($parser, false, $sql);

            if (!$validate || count($requestSql->error_sql)) {
                $errors = $this->getErrors($requestSql->error_sql);
            }
        } catch (ErrorException $e) {
            $errors[] = [
                'key' => 'Bad SQL query',
                'parameters' => [],
                'domain' => 'Admin.Notifications.Error',
            ];
        } catch (PrestaShopDatabaseException $e) {
            $errors[] = [
                'key' => 'Bad SQL query',
                'parameters' => [],
                'domain' => 'Admin.Notifications.Error',
            ];
        }

        return $errors;
    }

    /**
     * Get request sql errors.
     *
     * @param array $sqlErrors
     *
     * @return array
     */
    private function getErrors(array $sqlErrors)
    {
        $errors = [];

        foreach ($sqlErrors as $key => $sqlError) {
            if (false === is_array($sqlError)) {
                $sqlError = [];
            }

            if ('checkedFrom' === $key) {
                $errors[] = $this->getFromKeywordError($sqlError);
            } elseif ('checkedSelect' === $key) {
                $errors[] = $this->getSelectKeywordError($sqlError);
            } elseif ('checkedWhere' === $key) {
                $errors[] = $this->getWhereKeywordError($sqlError);
            } elseif ('checkedHaving' === $key) {
                $errors[] = $this->getHavingKeywordError($sqlError);
            } elseif ('checkedOrder' === $key) {
                $errors[] = $this->getOrderKeywordError($sqlError);
            } elseif ('checkedGroupBy' === $key) {
                $errors[] = $this->getGroupKeywordError($sqlError);
            } elseif ('checkedLimit' === $key) {
                $errors[] = $this->getLimitKeywordError();
            } elseif ('returnNameTable' === $key) {
                $errors[] = $this->getReferenceError($sqlError);
            } elseif ('testedRequired' === $key) {
                $errors[] = $this->getRequiredKeyError($sqlError);
            } elseif ('testedUnauthorized' === $key) {
                $errors[] = $this->getUnauthorizedKeyError($sqlError);
            }
        }

        return $errors;
    }

    /**
     * Get SQL error for "FROM" keyword validation.
     *
     * @param array $legacyError
     *
     * @return array
     */
    private function getFromKeywordError(array $legacyError)
    {
        if (isset($legacyError['table'])) {
            return [
                'key' => 'The "%tablename%" table does not exist.',
                'parameters' => [
                    '%tablename%' => $legacyError['table'],
                ],
                'domain' => 'Admin.Advparameters.Notification',
            ];
        }

        if (isset($legacyError['attribut'])) {
            return [
                'key' => 'The "%attribute%" attribute does not exist in the "%table%" table.',
                'parameters' => [
                    '%attribute%' => $legacyError['attribut'][0],
                    '%table%' => $legacyError['attribut'][1],
                ],
                'domain' => 'Admin.Advparameters.Notification',
            ];
        }

        return [
            'key' => 'Undefined "%s" error',
            'parameters' => [
                'checkedForm',
            ],
            'domain' => 'Admin.Advparameters.Notification',
        ];
    }

    /**
     * Get SQL error for "SELECT" keyword validation.
     *
     * @param array $legacyError
     *
     * @return array
     */
    private function getSelectKeywordError(array $legacyError)
    {
        if (isset($legacyError['table'])) {
            return [
                'key' => 'The "%tablename%" table does not exist.',
                'parameters' => [
                    '%tablename%' => $legacyError['table'],
                ],
                'domain' => 'Admin.Advparameters.Notification',
            ];
        }

        if (isset($legacyError['attribut'])) {
            return [
                'key' => 'The "%attribute%" attribute does not exist in the "%table%" table.',
                'parameters' => [
                    '%attribute%' => $legacyError['attribut'][0],
                    '%table%' => $legacyError['attribut'][1],
                ],
                'domain' => 'Admin.Advparameters.Notification',
            ];
        }

        if (isset($legacyError['*'])) {
            return [
                'key' => 'The "*" operator cannot be used in a nested query.',
                'parameters' => [],
                'domain' => 'Admin.Advparameters.Notification',
            ];
        }

        return [
            'key' => 'Undefined "%s" error',
            'parameters' => [
                'checkedSelect',
            ],
            'domain' => 'Admin.Advparameters.Notification',
        ];
    }

    /**
     * Get SQL error for "WHERE" keyword validation.
     *
     * @param array $legacyError
     *
     * @return array
     */
    private function getWhereKeywordError(array $legacyError)
    {
        if (isset($legacyError['operator'])) {
            return [
                'key' => 'The operator "%s" is incorrect.',
                'parameters' => [
                    '%operator%' => $legacyError['operator'],
                ],
                'domain' => 'Admin.Advparameters.Notification',
            ];
        }

        if (isset($legacyError['attribut'])) {
            return [
                'key' => 'The "%attribute%" attribute does not exist in the "%table%" table.',
                'parameters' => [
                    '%attribute%' => $legacyError['attribut'][0],
                    '%table%' => $legacyError['attribut'][1],
                ],
                'domain' => 'Admin.Advparameters.Notification',
            ];
        }

        return [
            'key' => 'Undefined "%s" error',
            'parameters' => [
                'checkedWhere',
            ],
            'domain' => 'Admin.Advparameters.Notification',
        ];
    }

    /**
     * Get SQL error for "HAVING" keyword validation.
     *
     * @param array $legacyError
     *
     * @return array
     */
    private function getHavingKeywordError(array $legacyError)
    {
        if (isset($legacyError['operator'])) {
            return [
                'key' => 'The "%operator%" operator is incorrect.',
                'parameters' => [
                    '%operator%' => $legacyError['operator'],
                ],
                'domain' => 'Admin.Advparameters.Notification',
            ];
        }

        if (isset($legacyError['attribut'])) {
            return [
                'key' => 'The "%attribute%" attribute does not exist in the "%table%" table.',
                'parameters' => [
                    '%attribute%' => $legacyError['attribut'][0],
                    '%table%' => $legacyError['attribut'][1],
                ],
                'domain' => 'Admin.Advparameters.Notification',
            ];
        }

        return [
            'key' => 'Undefined "%s" error',
            'parameters' => [
                'checkedHaving',
            ],
            'domain' => 'Admin.Advparameters.Notification',
        ];
    }

    /**
     * Get SQL error for "ORDER" keyword validation.
     *
     * @param array $legacyError
     *
     * @return array
     */
    private function getOrderKeywordError(array $legacyError)
    {
        if (isset($legacyError['attribut'])) {
            return [
                'key' => 'The "%attribute%" attribute does not exist in the "%table%" table.',
                'parameters' => [
                    '%attribute%' => $legacyError['attribut'][0],
                    '%table%' => $legacyError['attribut'][1],
                ],
                'domain' => 'Admin.Advparameters.Notification',
            ];
        }

        return [
            'key' => 'Undefined "%s" error',
            'parameters' => [
                'checkedOrder',
            ],
            'domain' => 'Admin.Advparameters.Notification',
        ];
    }

    /**
     * Get SQL error for "GROUP" keyword validation.
     *
     * @param array $legacyError
     *
     * @return array
     */
    private function getGroupKeywordError(array $legacyError)
    {
        if (isset($legacyError['attribut'])) {
            return [
                'key' => 'The "%attribute%" attribute does not exist in the "%table%" table.',
                'parameters' => [
                    '%attribute%' => $legacyError['attribut'][0],
                    '%table%' => $legacyError['attribut'][1],
                ],
                'domain' => 'Admin.Advparameters.Notification',
            ];
        }

        return [
            'key' => 'Undefined "%s" error',
            'parameters' => [
                'checkedGroupBy',
            ],
            'domain' => 'Admin.Advparameters.Notification',
        ];
    }

    /**
     * Get SQL error for "LIMIT" keyword validation.
     *
     * @return array
     */
    private function getLimitKeywordError()
    {
        return [
            'key' => 'The LIMIT clause must contain numeric arguments.',
            'parameters' => [],
            'domain' => 'Admin.Advparameters.Notification',
        ];
    }

    /**
     * Get reference related SQL error.
     *
     * @param array $legacyError
     *
     * @return array
     */
    private function getReferenceError(array $legacyError)
    {
        if (isset($legacyError['reference'])) {
            return [
                'key' => 'The "%reference%" reference does not exist in the "%table%" table.',
                'parameters' => [
                    '%reference%' => $legacyError['reference'][0],
                    '%table%' => $legacyError['attribut'][1],
                ],
                'domain' => 'Admin.Advparameters.Notification',
            ];
        }

        return [
            'key' => 'When multiple tables are used, each attribute must refer back to a table.',
            'parameters' => [],
            'domain' => 'Admin.Advparameters.Notification',
        ];
    }

    /**
     * Get required key error.
     *
     * @param string $legacyError
     *
     * @return array
     */
    private function getRequiredKeyError($legacyError)
    {
        return [
            'key' => '"%key%" does not exist.',
            'parameters' => [
                '%key%' => $legacyError,
            ],
            'domain' => 'Admin.Notifications.Error',
        ];
    }

    /**
     * Get unauthorized key error.
     *
     * @param string $legacyError
     *
     * @return array
     */
    private function getUnauthorizedKeyError($legacyError)
    {
        return [
            'key' => '"%key%" is an unauthorized keyword.',
            'parameters' => [
                '%key%' => $legacyError,
            ],
            'domain' => 'Admin.Advparameters.Notification',
        ];
    }
}
