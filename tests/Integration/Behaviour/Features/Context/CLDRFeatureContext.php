<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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

namespace Tests\Integration\Behaviour\Features\Context;

use PrestaShop\PrestaShop\Core\Localization\Locale\RepositoryInterface;
use RuntimeException;

class CLDRFeatureContext extends AbstractPrestaShopFeatureContext
{
    /**
     * @Then a price of :price using :currencyIsoCode in locale :locale should look like :expectedPrice
     */
    public function assertDisplayPrice($price, $currencyIsoCode, $locale, $expectedPrice)
    {
        /** @var RepositoryInterface $localeRepository */
        $localeRepository = CommonFeatureContext::getContainer()->get('prestashop.core.localization.locale.repository');
        $locale = $localeRepository->getLocale($locale);
        $displayedPrice = $locale->formatPrice($price, $currencyIsoCode);

        if ($expectedPrice !== $displayedPrice) {
            throw new RuntimeException(sprintf(
                'Displayed price is "%s" but "%s" was expected',
                $displayedPrice,
                $expectedPrice
            ));
        }
    }
}
