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

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Core\Environment\EnvironmentFinderInterface;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;

final class EnvironmentTypeChoiceProvider implements FormChoiceProviderInterface
{
    /**
     * @var EnvironmentFinderInterface
     */
    private EnvironmentFinderInterface $environmentFinder;

    /**
     * @param EnvironmentFinderInterface $environmentFinder
     */
    public function __construct(EnvironmentFinderInterface $environmentFinder)
    {
        $this->environmentFinder = $environmentFinder;
    }

    /**
     * {@inheritdoc}
     */
    public function getChoices()
    {
        $choices = [];

        foreach ($this->environmentFinder->findAll() as $environmentType)
        {
            $description = $environmentType->getDescription();
            $label = $environmentType->getName() . ($description ? ' ('.$description.') ' : '');
            $choices[$label] = $environmentType->getId();
        }

        return $choices;
    }
}
