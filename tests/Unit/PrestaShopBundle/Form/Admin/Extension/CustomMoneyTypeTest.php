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

namespace Tests\Unit\PrestaShopBundle\Form\Admin\Extension;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Currency\Repository\CurrencyRepository;
use PrestaShop\PrestaShop\Core\Localization\Locale;
use PrestaShop\PrestaShop\Core\Localization\Specification\Price;
use PrestaShopBundle\Form\Admin\Type\CustomMoneyType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

class CustomMoneyTypeTest extends TestCase
{
    private const DEFAULT_CURRENCY_ID = 1;

    /**
     * @dataProvider getDataForTestBuildViewAssignsCorrectMoneyPatternVariable
     *
     * @param string $currencyIso
     * @param string $symbol
     * @param string $cldrPattern
     * @param string $expectedPattern
     */
    public function testBuildViewAssignsCorrectMoneyPatternVariable(
        string $currencyIso,
        string $symbol,
        string $cldrPattern,
        string $expectedPattern
    ): void {
        $currencyRepository = $this->createMock(CurrencyRepository::class);
        $currencyRepository->method('getIsoCode')->willReturn($currencyIso);
        $customMoneyType = new CustomMoneyType(
            $this->mockLocale($cldrPattern, $symbol),
            self::DEFAULT_CURRENCY_ID,
            $currencyRepository
        );
        $formView = $this->mockFormView();

        $customMoneyType->buildView($formView, $this->mockFormInterface(), [
            'currency' => $currencyIso,
        ]);

        $this->assertArrayHasKey('money_pattern', $formView->vars);
        $this->assertSame($expectedPattern, $formView->vars['money_pattern']);
    }

    /**
     * @return FormView
     */
    private function mockFormView(): FormView
    {
        return $this->getMockBuilder(FormView::class)->getMock();
    }

    /**
     * @return FormInterface
     */
    private function mockFormInterface(): FormInterface
    {
        return $this->getMockBuilder(FormInterface::class)->getMock();
    }

    /**
     * @param string $pattern
     * @param string $symbol
     *
     * @return Locale
     */
    private function mockLocale(string $pattern, string $symbol): Locale
    {
        $locale = $this->getMockBuilder(Locale::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getPriceSpecification'])
            ->getMock()
        ;

        $locale->method('getPriceSpecification')->willReturn($this->mockPriceSpecification($pattern, $symbol));

        return $locale;
    }

    /**
     * @param string $pattern
     * @param string $symbol
     *
     * @return Price
     */
    private function mockPriceSpecification(string $pattern, string $symbol): Price
    {
        $price = $this->getMockBuilder(Price::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getPositivePattern', 'getCurrencySymbol'])
            ->getMock()
        ;

        $price->method('getPositivePattern')->willReturn($pattern);
        $price->method('getCurrencySymbol')->willReturn($symbol);

        return $price;
    }

    /**
     * @return iterable
     */
    public function getDataForTestBuildViewAssignsCorrectMoneyPatternVariable(): iterable
    {
        yield ['EUR', '€', "¤\u{00A0}#,##0.00", "€\u{00A0}{{ widget }}"];
        yield ['USD', '$', '¤#,##0.00', '${{ widget }}'];
        yield ['custom', 'custom', "#,##0.00\u{00A0}¤", "{{ widget }}\u{00A0}custom"];
        yield ['SEK', 'kr', '#,##0.00¤', '{{ widget }}kr'];
    }
}
