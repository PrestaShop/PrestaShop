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

namespace PrestaShopBundle\Form\Admin\Sell\Order\Invoices;

use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;

/**
 * Class is responsible of managing the data manipulated using invoice options form
 * in "Sell > Orders > Invoices" page.
 */
final class InvoiceOptionsDataProvider implements FormDataProviderInterface
{
    /**
     * @var DataConfigurationInterface
     */
    private $invoiceOptionsConfiguration;

    /**
     * @var int
     */
    private $nextInvoiceNumber;

    /**
     * @param DataConfigurationInterface $invoiceOptionsConfiguration
     */
    public function __construct(
        DataConfigurationInterface $invoiceOptionsConfiguration,
        $nextInvoiceNumber
    ) {
        $this->invoiceOptionsConfiguration = $invoiceOptionsConfiguration;
        $this->nextInvoiceNumber = $nextInvoiceNumber;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return $this->invoiceOptionsConfiguration->getConfiguration();
    }

    /**
     * {@inheritdoc}
     */
    public function setData(array $data)
    {
        return $this->invoiceOptionsConfiguration->updateConfiguration($data);
    }
}
