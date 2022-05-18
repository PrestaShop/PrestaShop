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

namespace PrestaShopBundle\Twig\Extension;

use Symfony\Component\Form\ChoiceList\View\ChoiceGroupView;
use Symfony\Component\Form\ChoiceList\View\ChoiceView;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class FormExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('findchoice', [$this, 'findChoice']),
        ];
    }

    public function findChoice(array $choiceGroupViews, string $value): ChoiceView
    {
        return $this->findChoiceFromGroups($choiceGroupViews, $value);
    }

    private function findChoiceFromGroups(array $choiceGroupViews, string $value): ?ChoiceView
    {
        foreach ($choiceGroupViews as $choiceGroupView) {
            foreach ($choiceGroupView->choices as $choice) {
                if ($choice instanceof ChoiceView && $choice->value === $value) {
                    return $choice;
                } elseif ($choice instanceof ChoiceGroupView) {
                    $foundChoice = $this->findChoiceFromGroups([$choice], $value);
                    if (null !== $foundChoice) {
                        return $foundChoice;
                    }
                }
            }
        }

        return null;
    }
}
