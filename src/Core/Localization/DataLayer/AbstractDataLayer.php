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
 * Provides common behavior to any Localization data layer
 * (be it CLDR locale data layer, currency data layer...)
 *
 * A data layer acts as a middleware, chained to other data layers, and is meant to
 * read/write data from/to it's data source but also from/to other data layers
 *
 * A data layer knows only the next (lower) layer for read/write chaining.
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
     * If it is writable, then it will be allowed to insert/update into its data source.
     * Read/write propagation is NOT affected by this property.
     *
     * A read-only data layer can only hydrate data from lower layers, and return it to the upper layer.
     * Data present in a read-only data layer is very likely to be incomplete and can be used only to hydrate data
     * from lower layers (unless this is the very last layer containing comprehensive reference data).
     *
     * @var bool
     */
    protected $isWritable = true;

    /**
     * Is this data layer writable ?
     *
     * @return bool
     *  True if writable. False if read-only layer.
     */
    public function isWritable()
    {
        return $this->isWritable;
    }

    /**
     * Validate a databag against type, data structure and data content rules
     *
     * This validation is very important because databag structure must remain the
     * same when passed through all data layers.
     *
     * @param mixed $databag
     *  The databag to be validated
     *
     * @return void
     *
     * @throws LocalizationException
     *  When validation failed
     */
    abstract protected function validateDatabag($databag);

    /**
     * Read a databag object, by code.
     *
     * Lower layer might be called if nothing found in current layer
     *
     * @param string $code
     *  The databag identifier
     *
     * @return mixed|null
     *  A databag object. Null if not found.
     *
     * @throws LocalizationException
     */
    public function read($code)
    {
        $databag = $this->doRead($code);

        // If nothing found, ask lower layer
        if (null === $databag) {
            $databag = $this->propagateRead($code);

            // If nothing was found deeper, there is nothing more to do
            if (null === $databag) {
                return null;
            }

            // Save result for next Read requests
            $this->saveReadPropagationResult($code, $databag);
        }

        return $databag;
    }

    /**
     * Write a databag object
     *
     * Write request is propagated to lower layer, and the propagation result is actually written in
     * current layer (because lower layer might hydrate/update the databag).
     *
     * @param $code
     *  The databag identifier
     *
     * @param $databag
     *  The databag to write
     *
     * @return mixed
     *  The databag to be written by the upper data layer
     *  (each layer might hydrate/update the databag for upper layers)
     *
     * @throws LocalizationException
     *  When write fails
     */
    public function write($code, $databag)
    {
        // First, write $databag in lower layers and store the (probably hydrated/updated) result
        $databag = $this->propagateWrite($code, $databag);

        // Then write this result in current layer
        $databag = $this->saveWritePropagationResult($code, $databag);

        return $databag;
    }

    /**
     * Propagate read to the lower layer
     *
     * @param $field
     *  The field to read
     *
     * @return mixed|null
     * @throws LocalizationException
     */
    protected function propagateRead($field)
    {
        if (isset($this->lowerDataLayer)) {
            return $this->lowerDataLayer->read($field);
        }

        return null;
    }

    /**
     * Propagate write to lower layer
     *
     * @param $code
     *  The databag identifier
     *
     * @param $databag
     *  The databag to write into this field
     *
     * @return mixed
     *  The databag to be written by the upper data layer
     *
     * @throws LocalizationException
     *  When write fails
     */
    protected function propagateWrite($code, $databag)
    {
        if (isset($this->lowerDataLayer)) {
            return $this->lowerDataLayer->write($code, $databag);
        }

        return $databag;
    }

    /**
     * Save databag received from lower layers after a Read request
     *
     * This databag is written in the current layer to avoid read propagation next time.
     *
     * @param string $code
     *  Databag identifier
     *
     * @param mixed $databag
     *  Databag received from lower layers
     *
     * @return void
     *
     * @throws LocalizationException
     *  When $databag is invalid, or write failed
     */
    protected function saveReadPropagationResult($code, $databag)
    {
        // Validation before we try to save anything in current layer
        $this->validateDatabag($databag);

        if ($this->isWritable()) {
            try {
                $this->doWrite($code, $databag);
            } catch (Exception $e) {
                throw new LocalizationException(
                    'Unable to write into "' . $code . '"" (data layer : "' . __CLASS__ . '")',
                    0,
                    $e
                );
            }
        }
    }

    /**
     * Save databag received from lower layers after a Write request
     *
     * This databag is written in the current layer after lower layers have hydrated/updated (and written) it
     *
     * @param string $code
     *  Databag identifier
     *
     * @param mixed $databag
     *  Databag received from lower layers
     *
     * @return mixed
     *  Databag to be written by upper layer
     *
     * @throws LocalizationException
     *  When $databag is invalid, or write failed
     */
    protected function saveWritePropagationResult($code, $databag)
    {
        // Validation before we try to save anything in current layer
        $this->validateDatabag($databag);

        if ($this->isWritable()) {
            // If update needed before write
            $databag = $this->beforeWrite($databag);

            try {
                $this->doWrite($code, $databag);
            } catch (Exception $e) {
                throw new LocalizationException(
                    'Unable to write into "' . $code . '"" (data layer "' . __CLASS__ . '")',
                    0,
                    $e
                );
            }

            // If update needed after write
            $databag = $this->afterWrite($databag);
        }

        return $databag;
    }

    /**
     * Process some updates on $databag before writing it in the current layer
     *
     * @param mixed $databag
     *  Databag to be updated before write
     *
     * @return mixed
     *  The updated databag
     */
    protected function beforeWrite($databag)
    {
        return $databag;
    }

    /**
     * Process some updates on $databag after writing it in the current layer
     *
     * @param mixed $databag
     *  Databag to be updated after write
     *
     * @return mixed
     *  The updated databag
     */
    protected function afterWrite($databag)
    {
        return $databag;
    }

    /**
     * Actually read a databag into the current layer
     *
     * Might be a file access, cache read, DB select...
     *
     * @param $code
     *  The databag identifier
     *
     * @return mixed|null
     *  The wanted databag (null if not found)
     *
     * @throws LocalizationException
     *  When read fails
     */
    abstract protected function doRead($code);

    /**
     * Actually write a databag into the current layer
     *
     * Might be a file edit, cache update, DB insert/update...
     *
     * @param $code
     *  The databag identifier
     *
     * @param $databag
     *  The databag to be written
     *
     * @return void
     *
     * @throws LocalizationException
     *  When write fails
     */
    abstract protected function doWrite($code, $databag);
}
