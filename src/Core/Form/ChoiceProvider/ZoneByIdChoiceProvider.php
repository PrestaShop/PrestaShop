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

use PrestaShop\PrestaShop\Core\Domain\Zone\DataProvider\ZoneDataProviderInterface;
use PrestaShop\PrestaShop\Core\Form\ConfigurableFormChoiceProviderInterface;

/**
 * Class provides Zone choices with ID values.
 */
final class ZoneByIdChoiceProvider implements ConfigurableFormChoiceProviderInterface
{
    /**
     * @var ZoneDataProviderInterface
     */
    private $zoneDataProvider;

    /**
     * @param ZoneDataProviderInterface $zoneDataProvider
     */
    public function __construct(ZoneDataProviderInterface $zoneDataProvider)
    {
        $this->zoneDataProvider = $zoneDataProvider;
    }

    /**
     * Get zone choices.
     *
     * {@inheritdoc}
     */
    public function getChoices(array $options): array
    {
        $active = isset($options['active']) && is_bool($options['active']) ?
            $options['active'] :
            false;

        $activeFirst = isset($options['activeFirst']) && is_bool($options['activeFirst']) ?
            $options['activeFirst'] :
            false;

        $zones = $this->zoneDataProvider->getZones($active, $activeFirst);
        $choices = [];

        foreach ($zones as $zone) {
            $choices[$zone['name']] = (int) $zone['id_zone'];
        }

        return $choices;
    }
}
