<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Form\Admin\Configure\ShopParameters\ProductPreferences;

use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShopBundle\Form\Admin\Configure\ShopParameters\ProductPreferences\GeneralType;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Form\FormFactoryInterface;

/**
 * "Number of days for which the product is considered new" is only type checked by the configuration
 * adapter (setAllowedTypes int), so without a constraint a negative number reaches the database and every
 * consumer then quietly falls back to its own default instead of showing the merchant an error.
 */
class GeneralTypeTest extends KernelTestCase
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        global $kernel;
        $kernel = self::$kernel;

        // the multistore form extension resolves a shop context while building this type
        $shopContextBuilder = self::getContainer()->get('test_shop_context_builder');
        $shopContextBuilder->setShopId(1);
        $shopContextBuilder->setShopConstraint(ShopConstraint::shop(1));

        $languageContextBuilder = self::getContainer()->get('test_language_context_builder');
        $languageContextBuilder->setLanguageId(1);

        $this->formFactory = self::getContainer()->get('form.factory');
    }

    public function testANegativeNumberOfDaysIsRejected(): void
    {
        $form = $this->submitNewDaysNumber('-1');

        $this->assertFalse($form->isValid(), 'a negative number of days must not pass validation');
        $this->assertGreaterThan(0, count($form->get('new_days_number')->getErrors()));
    }

    /**
     * @dataProvider provideAcceptableValues
     */
    public function testAcceptableNumbersOfDaysPass(string $value): void
    {
        $form = $this->submitNewDaysNumber($value);

        $this->assertCount(
            0,
            $form->get('new_days_number')->getErrors(),
            sprintf('%s should be an acceptable number of days', $value)
        );
    }

    public static function provideAcceptableValues(): iterable
    {
        yield 'zero' => ['0'];
        yield 'one' => ['1'];
        yield 'the default' => ['20'];
    }

    private function submitNewDaysNumber(string $value)
    {
        $form = $this->formFactory->create(GeneralType::class);
        $form->submit(['new_days_number' => $value], false);

        return $form;
    }
}
