<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
     * @return string|float|int the partial raw parameter
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
