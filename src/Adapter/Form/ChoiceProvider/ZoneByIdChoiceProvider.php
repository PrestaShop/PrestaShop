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

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Core\Form\ConfigurableFormChoiceProviderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Zone;

/**
 * This class will provide data from DB / ORM about Zone
 */
class ZoneByIdChoiceProvider implements ConfigurableFormChoiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getChoices(array $options): array
    {
        $options = $this->resolveOptions($options);

        $zones = Zone::getZones($options['active'], $options['active_first']);
        $choices = [];

        foreach ($zones as $zone) {
            $choices[$zone['name']] = (int) $zone['id_zone'];
        }

        return $choices;
    }

    private function resolveOptions(array $options): array
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            'active' => false,
            'active_first' => false,
        ]);
        $resolver->setAllowedTypes('active', 'bool');
        $resolver->setAllowedTypes('active_first', 'bool');

        return $resolver->resolve($options);
    }
}
