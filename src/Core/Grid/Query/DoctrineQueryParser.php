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
        $values = [];
        foreach ($queryParameters as $key => $value) {
            if (!is_string($key)) {
                throw new UnsupportedParameterException('Only named parameters are supported in prepared queries.');
            }
            $values[':' . $key] = $this->parseValue($value);
        }

        return strtr($query, $values);
    }

    /**
     * @param mixed $value the parameter value
     *
     * @return string the partial raw parameter
     *
     * @throws UnsupportedParameterException
     */
    private function parseValue($value)
    {
        if (is_string($value)) {
            return $this->parseStringParameter($value);
        }

        if (is_numeric($value)) {
            return $this->parseNumericParameter($value);
        }

        if (is_array($value)) {
            return $this->parseArrayParameter($value);
        }

        if (is_bool($value)) {
            return $this->parseBooleanParameter($value);
        }

        if ($value === null) {
            return 'NULL';
        }

        throw new UnsupportedParameterException('Unsupported value type: ' . gettype($value));
    }

    /**
     * @param string $value
     *
     * @return string
     */
    private function parseStringParameter($value)
    {
        return "'" . addslashes($value) . "'";
    }

    /**
     * @param int|float $value
     *
     * @return int|float
     */
    private function parseNumericParameter($value)
    {
        return $value;
    }

    /**
     * @param array $value
     *
     * @return string
     */
    private function parseArrayParameter(array $value)
    {
        return "'" . implode("', '", array_map('addslashes', $value)) . "'";
    }

    /**
     * @param bool $value
     *
     * @return string
     */
    private function parseBooleanParameter($value)
    {
        return $value ? 'TRUE' : 'FALSE';
    }
}
