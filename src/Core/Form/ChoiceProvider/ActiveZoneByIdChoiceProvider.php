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

namespace PrestaShop\PrestaShop\Core\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Core\Domain\Zone\DataProvider\ActiveZoneDataProviderInterface;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;

/**
 * Class provides Zone choices with ID values.
 */
final class ActiveZoneByIdChoiceProvider implements FormChoiceProviderInterface
{
    /**
     * @var ActiveZoneDataProviderInterface
     */
    private $activeZoneDataProvider;

    /**
     * @param ActiveZoneDataProviderInterface $activeZoneDataProvider
     */
    public function __construct(ActiveZoneDataProviderInterface $activeZoneDataProvider)
    {
        $this->activeZoneDataProvider = $activeZoneDataProvider;
    }

    /**
     * Get zone choices.
     *
     * {@inheritdoc}
     */
    public function getChoices(): array
    {
        $zones = $this->activeZoneDataProvider->getActiveZones();
        $choices = [];

        foreach ($zones as $zone) {
            $choices[$zone['name']] = (int) $zone['id_zone'];
        }

        return $choices;
    }
}
