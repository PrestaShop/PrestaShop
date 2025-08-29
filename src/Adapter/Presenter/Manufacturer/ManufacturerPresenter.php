<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Adapter\Presenter\Manufacturer;

use Hook;
use Language;
use Link;
use Manufacturer;
use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;

class ManufacturerPresenter
{
    /**
     * @var ImageRetriever
     */
    protected $imageRetriever;

    /**
     * @var Link
     */
    protected $link;

    public function __construct(Link $link)
    {
        $this->link = $link;
        $this->imageRetriever = new ImageRetriever($link);
    }

    /**
     * @param array|Manufacturer $manufacturer Manufacturer object or an array
     * @param Language $language
     *
     * @return ManufacturerLazyArray
     */
    public function present(array|Manufacturer $manufacturer, Language $language)
    {
        // Convert to array if a Manufacturer object was passed
        if (is_object($manufacturer)) {
            $manufacturer = (array) $manufacturer;
        }

        // Normalize IDs
        if (empty($manufacturer['id_manufacturer'])) {
            $manufacturer['id_manufacturer'] = $manufacturer['id'];
        }
        if (empty($manufacturer['id'])) {
            $manufacturer['id'] = $manufacturer['id_manufacturer'];
        }

        $manufacturerLazyArray = new ManufacturerLazyArray(
            $manufacturer,
            $language,
            $this->imageRetriever,
            $this->link
        );

        Hook::exec('actionPresentManufacturer',
            ['presentedManufacturer' => &$manufacturerLazyArray]
        );

        return $manufacturerLazyArray;
    }
}
