<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Context;

use Context;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\ShopException;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

/**
 * This service is used to help prepare multiple context builders, this is convenient
 * for install processes or in tests, but it shouldn't be used in other use cases.
 *
 * Usually the context builders are initialized via request listeners, this service allows
 * preparing the builder in advance even if we are not sure if they will be used.
 *
 * Since Context services are build lazily by these builders (which are factories) it's safer
 * to anticipate preparing them in edge cases.
 *
 * @internal
 */
class ContextBuilderPreparer
{
    public function __construct(
        private readonly LanguageContextBuilder $languageContextBuilder,
        private readonly ShopContextBuilder $shopContextBuilder,
    ) {
    }

    public function prepareFromLegacyContext(Context $context): void
    {
        if ($context->language && $context->language->id) {
            $this->prepareLanguageId((int) $context->language->id);
        }

        // A shop group or all shops context may carry no shop object, its scope is still prepared; a context
        // without a valid shop (the installation, before any shop exists) has no shop scope to prepare
        try {
            $shopConstraint = $context->getShopConstraint();
        } catch (ShopException) {
            return;
        }
        $shopId = $context->shop?->id;
        $this->prepareShopContext($shopConstraint, $shopId ? (int) $shopId : null);
    }

    public function prepareLanguageId(int $languageId): void
    {
        $this->languageContextBuilder->setLanguageId($languageId);
        $this->languageContextBuilder->setDefaultLanguageId($languageId);
    }

    /**
     * @param ShopConstraint $shopConstraint the scope of the context: a shop, a shop group or all shops
     * @param int|null $shopId the shop the context runs in: the one of a single shop constraint by default,
     *                         left to the builder when neither gives it
     */
    public function prepareShopContext(ShopConstraint $shopConstraint, ?int $shopId = null): void
    {
        $this->shopContextBuilder->setShopConstraint($shopConstraint);

        $shopId ??= $shopConstraint->getShopId()?->getValue();
        if (null !== $shopId) {
            $this->shopContextBuilder->setShopId($shopId);
        }
    }
}
