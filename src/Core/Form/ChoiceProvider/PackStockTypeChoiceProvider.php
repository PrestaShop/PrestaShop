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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Core\Domain\Product\Pack\ValueObject\PackStockType;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class PackStockTypeChoiceProvider implements FormChoiceProviderInterface
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var int
     */
    private $defaultPackStockType;

    /**
     * @param TranslatorInterface $translator
     * @param int $defaultPackStockType
     */
    public function __construct(
        TranslatorInterface $translator,
        int $defaultPackStockType
    ) {
        $this->translator = $translator;
        $this->defaultPackStockType = $defaultPackStockType;
    }

    /**
     * {@inheritDoc}
     */
    public function getChoices()
    {
        $choices = $this->getLabelValuePairs();

        $defaultLabel = sprintf(
            '%s (%s)',
            $this->translator->trans('Default', [], 'Admin.Global'),
            array_search($this->defaultPackStockType, $choices, true)
        );

        $choices[$defaultLabel] = PackStockType::STOCK_TYPE_DEFAULT;

        return $choices;
    }

    /**
     * @return array<string, int>
     */
    private function getLabelValuePairs(): array
    {
        return [
            $this->translator->trans('Decrement pack only.', [], 'Admin.Catalog.Feature') => PackStockType::STOCK_TYPE_PACK_ONLY,
            $this->translator->trans('Decrement products in pack only.', [], 'Admin.Catalog.Feature') => PackStockType::STOCK_TYPE_PRODUCTS_ONLY,
            $this->translator->trans('Decrement both.', [], 'Admin.Catalog.Feature') => PackStockType::STOCK_TYPE_BOTH,
        ];
    }
}
