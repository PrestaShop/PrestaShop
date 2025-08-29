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

namespace PrestaShop\PrestaShop\Core\Context;

use Context;

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
    ) {
    }

    public function prepareFromLegacyContext(Context $context): void
    {
        if ($context->language && $context->language->id) {
            $this->languageContextBuilder->setLanguageId($context->language->id);
            $this->languageContextBuilder->setDefaultLanguageId($context->language->id);
        }
    }

    public function prepareLanguageId(int $languageId): void
    {
        $this->languageContextBuilder->setLanguageId($languageId);
        $this->languageContextBuilder->setDefaultLanguageId($languageId);
    }
}
