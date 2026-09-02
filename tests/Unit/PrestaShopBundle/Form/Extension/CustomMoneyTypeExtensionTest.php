<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Form\Extension;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Currency\Repository\CurrencyRepository;
use PrestaShop\PrestaShop\Core\Localization\Locale;
use PrestaShop\PrestaShop\Core\Localization\Number\LocaleNumberTransformer;
use PrestaShop\PrestaShop\Core\Localization\Specification\Price;
use PrestaShopBundle\Form\Extension\CustomMoneyTypeExtension;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

class CustomMoneyTypeExtensionTest extends TestCase
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
        $localeNumberTransformer = $this->createMock(LocaleNumberTransformer::class);
        $localeNumberTransformer->method('getLocaleForNumberInputs')->willReturn('en');
        $currencyRepository = $this->createMock(CurrencyRepository::class);
        $currencyRepository->method('getIsoCode')->willReturn($currencyIso);
        $customMoneyType = new CustomMoneyTypeExtension(
            $this->mockLocale($cldrPattern, $symbol),
            self::DEFAULT_CURRENCY_ID,
            $currencyRepository,
            $localeNumberTransformer
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
