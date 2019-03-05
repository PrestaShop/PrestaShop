<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Form\Admin\Sell\Order\Delivery;

use PrestaShop\PrestaShop\Adapter\Order\Delivery\SlipPdfConfiguration;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;

/**
 * This class is responsible of managing the data manipulated pdf form
 * in "Sells > Orders > Delivery Slips" page.
 */
final class SlipPdfFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var SlipPdfConfiguration
     */
    private $configuration;

    public function __construct(SlipPdfConfiguration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function setData(array $data)
    {
        return $this->configuration->updateConfiguration($data['pdf']);
    }
}
