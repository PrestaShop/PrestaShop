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

namespace PrestaShop\PrestaShop\Core\Grid\Query;

use PrestaShop\PrestaShop\Core\Grid\Exception\UnsupportedParameterException;

/**
 * This class offers a DBAL implementation of Query parser.
 */
final class DoctrineQueryParser implements QueryParserInterface
{
    /**
     * {@inheritdoc}
     */
    public function parse($query, array $queryParameters)
    {
        $keys = array();
        $values = $queryParameters;

        foreach ($queryParameters as $key => $value) {
            if (!is_string($key)) {
                throw new UnsupportedParameterException('Only named parameters are supported in prepared queries.');
            }

            $keys[] = '/:' . $key . '/';

            if (is_string($value)) {
                $values = $this->parseStringParameter($value, $values, $key);
            }

            if (is_array($value)) {
                $values = $this->parseArrayParameter($value, $values, $key);
            }

            if (is_bool($value)) {
                $values = $this->parseBooleanParameter($value, $values, $key);
            }

            if ($value === null) {
                $values = $this->parseNullParameter($values, $key);
            }
        }

        $query = preg_replace($keys, $values, $query);

        return $query;
    }

    /**
     * @param string $value
     * @param array $values
     * @param string $key
     *
     * @return array
     */
    private function parseStringParameter($value, $values, $key)
    {
        $values[$key] = "'" . $value . "'";

        return $values;
    }

    /**
     * @param array $value
     * @param array $values
     * @param string $key
     *
     * @return array
     */
    private function parseArrayParameter(array $value, $values, $key)
    {
        $values[$key] = "'" . implode("', '", $value) . "'";

        return $values;
    }

    /**
     * @param bool $value
     * @param array $values
     * @param string $key
     *
     * @return array
     */
    private function parseBooleanParameter($value, $values, $key)
    {
        $values[$key] = $value ? 'TRUE' : 'FALSE';

        return $values;
    }

    /**
     * @param array $values
     * @param string $key
     *
     * @return array
     */
    private function parseNullParameter($values, $key)
    {
        $values[$key] = 'NULL';

        return $values;
    }
}
