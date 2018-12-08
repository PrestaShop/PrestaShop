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

namespace PrestaShop\PrestaShop\Core\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Adapter\Carrier\CarrierDataProvider;
use PrestaShop\PrestaShop\Adapter\Entity\Carrier;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;

/**
 * Class CarrierByReferenceChoiceProvider is responsible for providing carrier choices with value reference.
 */
final class CarrierByReferenceChoiceProvider implements FormChoiceProviderInterface
{
    /**
     * @var CarrierDataProvider
     */
    private $carrierDataProvider;

    /**
     * @var int
     */
    private $langId;

    /**
     * @param CarrierDataProvider $carrierDataProvider
     * @param int $langId
     */
    public function __construct(CarrierDataProvider $carrierDataProvider, $langId)
    {
        $this->carrierDataProvider = $carrierDataProvider;
        $this->langId = $langId;
    }

    /**
     * {@inheritdoc}
     */
    public function getChoices()
    {
        $choices = [];

        $carriers = $this->carrierDataProvider->getCarriers(
            $this->langId,
            false,
            false,
            false,
            null,
            Carrier::ALL_CARRIERS
        );

        foreach ($carriers as $carrier) {
            $choices[$carrier['name']] = $carrier['id_reference'];
        }

        return $choices;
    }
}
