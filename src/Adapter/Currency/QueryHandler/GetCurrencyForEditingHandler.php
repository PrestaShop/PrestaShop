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

namespace PrestaShop\PrestaShop\Adapter\Currency\QueryHandler;

use Currency;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Query\GetCurrencyForEditing;
use PrestaShop\PrestaShop\Core\Domain\Currency\QueryHandler\GetCurrencyForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Currency\QueryResult\EditableCurrency;
use PrestaShop\PrestaShop\Core\Localization\Currency\PatternTransformer;

/**
 * Class GetCurrencyForEditingHandler is responsible for retrieving required data used in currency form.
 *
 * @internal
 */
final class GetCurrencyForEditingHandler implements GetCurrencyForEditingHandlerInterface
{
    /**
     * @var int
     */
    private $contextShopId;

    /**
     * @var PatternTransformer
     */
    private $patternTransformer;

    /**
     * @param int $contextShopId
     */
    public function __construct(
        int $contextShopId,
        PatternTransformer $patternTransformer
    ) {
        $this->contextShopId = $contextShopId;
        $this->patternTransformer = $patternTransformer;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(GetCurrencyForEditing $query)
    {
        $entity = new Currency(
            $query->getCurrencyId()->getValue(),
            null,
            $this->contextShopId
        );

        if (0 >= $entity->id) {
            throw new CurrencyNotFoundException(sprintf('Currency object with id "%s" was not found for editing', $query->getCurrencyId()->getValue()));
        }

        $transformations = [];
        foreach ($entity->getLocalizedPatterns() as $langId => $pattern) {
            $transformations[$langId] = !empty($pattern) ? $this->patternTransformer->getTransformationType($pattern) : '';
        }

        return new EditableCurrency(
            $entity->id,
            $entity->iso_code,
            $entity->getLocalizedNames(),
            $entity->getLocalizedSymbols(),
            $transformations,
            $entity->conversion_rate,
            $entity->precision,
            $entity->active,
            $entity->unofficial,
            $entity->getAssociatedShops()
        );
    }
}
