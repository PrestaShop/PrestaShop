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

use PrestaShop\PrestaShop\Adapter\Country\ValueObject\Country;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Country\Query\GetCountries;
use PrestaShop\PrestaShop\Core\Domain\Country\QueryResult\Countries;
use PrestaShop\PrestaShop\Core\Form\ConfigurableFormChoiceProviderInterface;

/**
 * Class provides configurable country choices with ID values.
 */
final class CountryByIdConfigurableChoiceProvider implements ConfigurableFormChoiceProviderInterface
{
    /**
     * @var CommandBusInterface
     */
    private $queryBus;

    /**
     * @var int
     */
    private $langId;

    /**
     * @param int $langId
     * @param CommandBusInterface $queryBud
     */
    public function __construct(
        int $langId,
        CommandBusInterface $queryBud
    ) {
        $this->langId = $langId;
        $this->queryBus = $queryBud;
    }

    /**
     * Get country choices.
     *
     * {@inheritdoc}
     */
    public function getChoices(array $options): array
    {
        $active = isset($options['active']) && is_bool($options['active']) ?
            $options['active'] :
            false;

        $containsStates = isset($options['contains_states']) && is_bool($options['contains_states']) ?
            $options['contains_states'] :
            false;

        $listStates = isset($options['list_states']) && is_bool($options['list_states']) ?
            $options['list_states'] :
            false;

        $query = (new GetCountries($this->langId))
            ->setActive($active)
            ->setContainsStates($containsStates)
            ->setIncludeStatesList($listStates);

        /** @var Countries $countries */
        $countries = $this->queryBus->handle($query);
        $choices = [];

        /** @var Country $country */
        foreach ($countries->getCountries() as $country) {
            $choices[$country->getName()] = $country->getId();
        }

        return $choices;
    }
}
