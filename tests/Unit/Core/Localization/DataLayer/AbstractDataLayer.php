<?php

/**
 * 2007-2018 PrestaShop
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

namespace PrestaShop\PrestaShop\Core\Localization\DataLayer;

use Exception;
use PrestaShop\PrestaShop\Core\Localization\Exception\LocalizationException;

/**
 * Abstract Localization data layer class
 *
 * Provides common behavior to any Localization data layer
 * (a data layer acts as a middleware, chained to other data layers,
 * and is meant to read/write data from/to it's data source and other data layers)
 */
abstract class AbstractDataLayer
{
    /**
     * The lower data layer to communicate with (read/write)
     *
     * @var AbstractDataLayer
     */
    protected $lowerDataLayer;

    /**
     * Is this data layer writable ?
     *
     * @var bool
     */
    protected $isWritable = true;

    /**
     * Read a field's value
     *
     * @param string $field
     *  The field to read
     *
     * @return mixed
     *  The searched field's value
     *
     * @throws LocalizationException
     */
    public function read($field)
    {
        $result = $this->doRead($field);

        /* TODO : maybe the result should be wrapped in a result object,
         * so the null value is not interpreted as a missing result ?
         * Sometimes, null value can be wanted and meaningful and should not trigger
         * all the data layers chain...
         */
        if (null === $result) {
            $result = $this->lowerDataLayer->read($field);
        }

        // Save result for later read
        if ($this->isWritable()) {
            try {
                $this->doWrite($field, $result);
            } catch (Exception $e) {
                throw new LocalizationException(
                    'Unable to write into "' . $field . '"" (data layer "' . __CLASS__ . '")',
                    0,
                    $e
                );
            }
        }

        return $result;
    }

    /**
     * Write a field's value
     *
     * @param $field
     *  The field to write
     *
     * @param $value
     *  The value to write into this field
     *
     * @return mixed
     *  The value to be written by the upper data layer
     *
     * @throws LocalizationException
     */
    public function write($field, $value)
    {
        // If the current layer is read only, $field's value should never be altered, even if upper layer just did it.
        if (!$this->isWritable()) {
            return $value;
        }

        try {
            $this->doWrite($field, $value);
        } catch (Exception $e) {
            throw new LocalizationException(
                'Unable to write into "' . $field . '"" (data layer "' . __CLASS__ . '")',
                0,
                $e
            );
        }

        return $this->read($field);
    }

    /**
     * Is this data layer writable ?
     *
     * @return bool
     *  True if this data layer is writable
     */
    public function isWritable()
    {
        return $this->isWritable;
    }

    /**
     * Actual read into the current layer
     * Might be a file access, cache read, DB select...
     *
     * @param $field
     *  Field to be read
     *
     * @return mixed
     *  The read result (either the field's value, or null if not found)
     *  TODO : maybe the result should be wrapped in a result object,
     *  so the null value is not interpreted as a missing result ?
     *  Sometimes, null value can be wanted and meaningful and should not trigger
     *  all the data layers chain...
     */
    abstract protected function doRead($field);

    /**
     * Actual write into the current layer
     * Might be a file edit, cache update, DB insert/update...
     *
     * @param $field
     *  The field to write
     *
     * @param $value
     *  The value to write into this field
     *
     * @throws LocalizationException
     *  When write failed
     */
    abstract protected function doWrite($field, $value);
}
