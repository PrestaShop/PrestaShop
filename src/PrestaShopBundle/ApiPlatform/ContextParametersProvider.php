<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\ApiPlatform;

use PrestaShop\PrestaShop\Core\Context\ApiClientContext;
use PrestaShop\PrestaShop\Core\Context\CurrencyContext;
use PrestaShop\PrestaShop\Core\Context\LanguageContext;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopCollection;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;

/**
 * Provides an array containing the values from PrestaShop context services so they can be accessed
 * and used via the mapping.
 */
class ContextParametersProvider
{
    public function __construct(
        protected readonly ShopContext $shopContext,
        protected readonly LanguageContext $languageContext,
        protected readonly CurrencyContext $currencyContext,
        protected readonly ApiClientContext $apiClientContext,
    ) {
    }

    public function getContextParameters(): array
    {
        $shopConstraint = $this->shopContext->getShopConstraint();

        return [
            '_context' => [
                'shopConstraint' => [
                    'shopId' => $shopConstraint->getShopId()?->getValue(),
                    'shopGroupId' => $shopConstraint->getShopGroupId()?->getValue(),
                    'shopIds' => $shopConstraint instanceof ShopCollection ? array_map(fn (ShopId $shopId) => $shopId->getValue(), $shopConstraint->getShopIds()) : null,
                    'isStrict' => $shopConstraint->isStrict(),
                ],
                'shopId' => $this->shopContext->getId(),
                'shopIds' => $this->shopContext->getAssociatedShopIds(),
                'langId' => $this->languageContext->getId(),
                'currencyId' => $this->currencyContext->getId(),
                'apiClientId' => $this->apiClientContext->getApiClient()?->getId(),
            ],
        ];
    }
}
