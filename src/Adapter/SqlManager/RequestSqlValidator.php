<?php
/**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\SqlManager;

use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class RequestSqlValidator is responsible for validating Request SQL model data
 */
class RequestSqlValidator
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Check if SQL is valid for Reqest SQL model.
     * Only "Select" sqls should be valid.
     *
     * @param string $sql
     *
     * @return array        Array of errors if any
     */
    public function validateSql($sql)
    {
        $errors = [];

        $requestSql = new \RequestSql();
        $parser = $requestSql->parsingSql($sql);
        $validate = $requestSql->validateParser($parser, false, $sql);

        if (!$validate || count($requestSql->error_sql)) {
            $errors = $this->getErrors($requestSql->error_sql);
        }

        return $errors;
    }

    /**
     * Get request sql errors
     *
     * @param array $sqlErrors
     *
     * @return array
     */
    private function getErrors(array $sqlErrors)
    {
        $errors = [];

        foreach ($sqlErrors as $key => $sqlError) {
            switch ($key) {
                case 'checkedFrom':
                    if (isset($sqlError['table'])) {
                        $errors = $this->translator->trans(
                            'The "%tablename%" table does not exist.',
                            [
                                '%tablename%' => $sqlError['table'],
                            ],
                            'Admin.Advparameters.Notification'
                        );
                    } elseif (isset($sqlError['attribut'])) {
                        $errors = $this->translator->trans(
                            'The "%attribute%" attribute does not exist in the "%table%" table.',
                            array(
                                '%attribute%' => $sqlError['attribut'][0],
                                '%table%' => $sqlError['attribut'][1],
                            ),
                            'Admin.Advparameters.Notification'
                        );
                    } else {
                        $errors = $this->translator->trans('Undefined "%s" error', ['checkedForm'], 'Admin.Advparameters.Notification');
                    }
                    break;
                case 'checkedSelect':
                    if (isset($sqlError['table'])) {
                        $errors = $this->translator->trans(
                            'The "%tablename%" table does not exist.',
                            [
                                '%tablename%' => $sqlError['table'],
                            ],
                            'Admin.Advparameters.Notification'
                        );
                    } elseif (isset($sqlError['attribut'])) {
                        $errors = $this->translator->trans(
                            'The "%attribute%" attribute does not exist in the "%table%" table.',
                            [
                                '%attribute%' => $sqlError['attribut'][0],
                                '%table%' => $sqlError['attribut'][1],
                            ],
                            'Admin.Advparameters.Notification'
                        );
                    } elseif (isset($sqlError['*'])) {
                        $errors = $this->translator->trans('The "*" operator cannot be used in a nested query.', [], 'Admin.Advparameters.Notification');
                    } else {
                        $errors = $this->translator->trans('Undefined "%s" error', ['checkedSelect'], 'Admin.Advparameters.Notification');
                    }
                    break;
                case 'checkedWhere':
                    if (isset($sqlError['operator'])) {
                        $errors = $this->translator->trans(
                            'The operator "%s" is incorrect.',
                            [
                                '%operator%' => $sqlError['operator'],
                            ],
                            'Admin.Advparameters.Notification'
                        );
                    } elseif (isset($sqlError['attribut'])) {
                        $errors = $this->translator->trans(
                            'The "%attribute%" attribute does not exist in the "%table%" table.',
                            [
                                '%attribute%' => $sqlError['attribut'][0],
                                '%table%' => $sqlError['attribut'][1],
                            ],
                            'Admin.Advparameters.Notification'
                        );
                    } else {
                        $errors = $this->translator->trans('Undefined "%s" error', array('checkedWhere'), 'Admin.Advparameters.Notification');
                    }
                    break;
                case 'checkedHaving':
                    if (isset($sqlError['operator'])) {
                        $errors = $this->translator->trans(
                            'The "%operator%" operator is incorrect.',
                            [
                                '%operator%' => $sqlError['operator']
                            ],
                            'Admin.Advparameters.Notification'
                        );
                    } elseif (isset($sqlError['attribut'])) {
                        $errors = $this->translator->trans(
                            'The "%attribute%" attribute does not exist in the "%table%" table.',
                            [
                                '%attribute%' => $sqlError['attribut'][0],
                                '%table%' => $sqlError['attribut'][1],
                            ],
                            'Admin.Advparameters.Notification'
                        );
                    } else {
                        $errors = $this->translator->trans('Undefined "%s" error', ['checkedHaving'], 'Admin.Advparameters.Notification');
                    }
                    break;
                case 'checkedOrder':
                    if (isset($sqlError['attribut'])) {
                        $errors = $this->translator->trans(
                            'The "%attribute%" attribute does not exist in the "%table%" table.',
                            [
                                '%attribute%' => $sqlError['attribut'][0],
                                '%table%' => $sqlError['attribut'][1],
                            ],
                            'Admin.Advparameters.Notification'
                        );
                    } else {
                        $errors = $this->translator->trans('Undefined "%s" error', ['checkedOrder'], 'Admin.Advparameters.Notification');
                    }
                    break;
                case 'checkedGroupBy':
                    if (isset($sqlError['attribut'])) {
                        $errors = $this->translator->trans(
                            'The "%attribute%" attribute does not exist in the "%table%" table.',
                            [
                                '%attribute%' => $sqlError['attribut'][0],
                                '%table%' => $sqlError['attribut'][1],
                            ],
                            'Admin.Advparameters.Notification'
                        );
                    } else {
                        $errors = $this->translator->trans('Undefined "%s" error', ['checkedGroupBy'], 'Admin.Advparameters.Notification');
                    }
                    break;
                case 'checkedLimit':
                    $errors = $this->translator->trans('The LIMIT clause must contain numeric arguments.', [], 'Admin.Advparameters.Notification');
                    break;
                case 'returnNameTable':
                    if (isset($sqlError['reference'])) {
                        $errors = $this->translator->trans(
                            'The "%reference%" reference does not exist in the "%table%" table.',
                            [
                                '%reference%' => $sqlError['reference'][0],
                                '%table%' => $sqlError['attribut'][1],
                            ],
                            'Admin.Advparameters.Notification'
                        );
                    } else {
                        $errors = $this->translator->trans('When multiple tables are used, each attribute must refer back to a table.', [], 'Admin.Advparameters.Notification');
                    }
                    break;
                case 'testedRequired':
                    $errors = $this->translator->trans('"%key%" does not exist.', ['%key%' => $sqlError], 'Admin.Notifications.Error');
                    break;
                case 'testedUnauthorized':
                    $errors = $this->translator->trans('"%key%" is an unauthorized keyword.', ['%key%' => $sqlError], 'Admin.Advparameters.Notification');
                    break;
            }
        }

        if (!is_array($errors)) {
            $errors = [$errors];
        }

        return $errors;
    }
}
