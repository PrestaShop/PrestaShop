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

namespace PrestaShop\PrestaShop\Core\Data\Layer;

use Exception;

/**
 * Abstract data layer class
 * Provides common behavior to any data layer
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
     * @var AbstractDataLayer|null
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
     * Read a data object, by identifier.
     *
     * Lower layer might be called if nothing found in current layer
     *
     * @param mixed $id
     *  The data object identifier
     *
     * @return mixed|null
     *  A data object. Null if not found.
     *
     * @throws DataLayerException
     */
    public function read($id)
    {
        $data = $this->doRead($id);

        // If nothing found, ask lower layer
        if (null === $data) {
            $data = $this->propagateRead($id);

            // If nothing was found deeper, there is nothing more to do
            if (null === $data) {
                return null;
            }

            // Save result for next Read requests
            $this->saveReadPropagationResult($id, $data);
        }

        return $data;
    }

    /**
     * Write a data object
     *
     * Write request is propagated to lower layer, and the propagation result is actually written in
     * current layer (because lower layer might hydrate/update the data object).
     *
     * @param mixed $id
     *  The data object identifier
     *
     * @param mixed $data
     *  The data object to write
     *
     * @return mixed
     *  The data object to be written by the upper data layer
     *  (each layer might hydrate/update the data object for upper layers)
     *
     * @throws DataLayerException
     *  When write fails
     */
    public function write($id, $data)
    {
        // First, write $data in lower layers and store the (probably hydrated/updated) result
        $data = $this->propagateWrite($id, $data);

        // Then write this result in current layer
        $data = $this->saveWritePropagationResult($id, $data);

        return $data;
    }

    /**
     * Propagate read to the lower layer
     *
     * @param $field
     *  The field to read
     *
     * @return mixed|null
     * @throws DataLayerException
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
     * @param mixed $id
     *  The data object identifier
     *
     * @param mixed $data
     *  The data object to write into this field
     *
     * @return mixed
     *  The data object to be written by the upper data layer
     *
     * @throws DataLayerException
     *  When write fails
     */
    protected function propagateWrite($id, $data)
    {
        if (isset($this->lowerDataLayer)) {
            return $this->lowerDataLayer->write($id, $data);
        }

        return $data;
    }

    /**
     * Save data object received from lower layers after a Read request
     *
     * This data object is written in the current layer to avoid read propagation next time.
     *
     * @param mixed $id
     *  Data object identifier
     *
     * @param mixed $data
     *  Data object received from lower layers
     *
     * @return void
     *
     * @throws DataLayerException
     *  When write failed
     */
    protected function saveReadPropagationResult($id, $data)
    {
        if ($this->isWritable()) {
            try {
                $this->doWrite($id, $data);
            } catch (Exception $e) {
                throw new DataLayerException(
                    'Unable to write into "' . $id . '"" (data layer : "' . __CLASS__ . '")',
                    0,
                    $e
                );
            }
        }
    }

    /**
     * Save data object received from lower layers after a Write request
     *
     * This data object is written in the current layer after lower layers have hydrated/updated (and written) it
     *
     * @param mixed $id
     *  Data object identifier
     *
     * @param mixed $data
     *  Data object received from lower layers
     *
     * @return mixed
     *  Data object to be written by upper layer
     *
     * @throws DataLayerException
     *  When write failed
     */
    protected function saveWritePropagationResult($id, $data)
    {
        if ($this->isWritable()) {
            // If update needed before write
            $data = $this->beforeWrite($data);

            try {
                $this->doWrite($id, $data);
            } catch (Exception $e) {
                throw new DataLayerException(
                    'Unable to write into "' . $id . '"" (data layer "' . __CLASS__ . '")',
                    0,
                    $e
                );
            }

            // If update needed after write
            $data = $this->afterWrite($data);
        }

        return $data;
    }

    /**
     * Process some updates on $data before writing it in the current layer
     *
     * @param mixed $data
     *  Data object to be updated before write
     *
     * @return mixed
     *  The updated data object
     */
    protected function beforeWrite($data)
    {
        return $data;
    }

    /**
     * Process some updates on $data after writing it in the current layer
     *
     * @param mixed $data
     *  Data object to be updated after write
     *
     * @return mixed
     *  The updated data object
     */
    protected function afterWrite($data)
    {
        return $data;
    }

    /**
     * Actually read a data object into the current layer
     *
     * Might be a file access, cache read, DB select...
     *
     * @param mixed $id
     *  The data object identifier
     *
     * @return mixed|null
     *  The wanted data object (null if not found)
     *
     * @throws DataLayerException
     *  When read fails
     */
    abstract protected function doRead($id);

    /**
     * Actually write a data object into the current layer
     *
     * Might be a file edit, cache update, DB insert/update...
     *
     * @param mixed $id
     *  The data object identifier
     *
     * @param mixed $data
     *  The data object to be written
     *
     * @return void
     *
     * @throws DataLayerException
     *  When write fails
     */
    abstract protected function doWrite($id, $data);
}
