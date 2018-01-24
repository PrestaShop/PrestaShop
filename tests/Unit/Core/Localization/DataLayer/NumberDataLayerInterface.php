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

/**
 * Number data layer classes interface
 *
 * Describes the behavior of NumberDataLayer classes
 */
interface NumberDataLayerInterface
{
    /**
     * Read a field's value
     *
     * @param string $field
     *  The field to read
     *
     * @return mixed
     *  The searched field's value
     */
    public function read($field);

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
     */
    public function write($field, $value);

    /**
     * Set the lower layer.
     * When reading data, if nothing is found then it will try to read in the lower data layer
     * When writing data, the data will also be written in the lower data layer
     *
     * @param NumberDataLayerInterface $lowerLayer
     *  The lower data layer.
     *
     * @return self
     */
    public function setLowerLayer(NumberDataLayerInterface $lowerLayer);
}
