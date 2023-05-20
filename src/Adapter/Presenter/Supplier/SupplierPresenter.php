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

namespace PrestaShop\PrestaShop\Adapter\Presenter\Supplier;

use Hook;
use Language;
use Link;
use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use Supplier;

class SupplierPresenter
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
     * @param array|Supplier $supplier Supplier object or an array
     * @param Language $language
     *
     * @return SupplierLazyArray
     */
    public function present($supplier, $language)
    {
        // Convert to array if a Supplier object was passed
        if (is_object($supplier)) {
            $supplier = (array) $supplier;
        }

        // Normalize IDs
        if (empty($supplier['id_supplier'])) {
            $supplier['id_supplier'] = $supplier['id'];
        }
        if (empty($supplier['id'])) {
            $supplier['id'] = $supplier['id_supplier'];
        }

        $supplierLazyArray = new SupplierLazyArray(
            $supplier,
            $language,
            $this->imageRetriever,
            $this->link
        );

        Hook::exec('actionPresentSupplier',
            ['presentedSupplier' => &$supplierLazyArray]
        );

        return $supplierLazyArray;
    }
}
