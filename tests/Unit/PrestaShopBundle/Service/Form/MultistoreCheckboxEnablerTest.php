<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

use PrestaShop\PrestaShop\Adapter\Configuration as ShopConfiguration;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Extension\MultistoreExtension;
use PrestaShopBundle\Form\FormCloner;
use PrestaShopBundle\Service\Form\MultistoreCheckboxEnabler;
use PrestaShopBundle\Service\Form\MultistoreConfigurationDropdownRenderer;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\Test\TypeTestCase;

class MultistoreCheckboxEnablerTest extends TypeTestCase
{
    public $mockedShopConfiguration;

    protected function setUp(): void
    {
        $this->mockedShopConfiguration = $this->createShopConfigurationMock();
        parent::setUp();
    }

    /**
     * @dataProvider provideShouldAddMultistoreElements
     *
     * @param bool $isMultistoreUsed
     * @param bool $isAllShopContext
     */
    public function testShouldAddMultistoreElements(bool $isMultistoreUsed, bool $isAllShopContext, bool $expectedValue): void
    {
        $checkboxEnabler = new MultistoreCheckboxEnabler(
            $this->mockedShopConfiguration,
            $this->mockShopContext($isMultistoreUsed, $isAllShopContext, !$isAllShopContext),
            new FormCloner(),
            $this->createMultistoreConfigurationDropdownRendererMock(),
        );

        $this->assertEquals($expectedValue, $checkboxEnabler->shouldAddMultistoreElements());
    }

    /**
     * @return array
     */
    public function provideShouldAddMultistoreElements(): array
    {
        return [
            [true, false, true],
            [true, true, true],
            [false, true, false],
            [false, false, false],
        ];
    }

    /**
     * @throws PrestaShop\PrestaShop\Core\Domain\Shop\Exception\ShopException
     */
    public function testAddMultistoreElements(): void
    {
        $form = $this->getFormToTest();

        $checkboxEnabler = new MultistoreCheckboxEnabler(
            $this->mockedShopConfiguration,
            $this->mockShopContext(),
            new FormCloner(),
            $this->createMultistoreConfigurationDropdownRendererMock(),
        );

        $checkboxEnabler->addMultistoreElements($form);
        $this->assertTrue($form->has(MultistoreCheckboxEnabler::MULTISTORE_FIELD_PREFIX . 'first_field'));
        $this->assertTrue($form->has('first_field'));
        $this->assertTrue($form->has('second_field'));
        $this->assertFalse($form->has(MultistoreCheckboxEnabler::MULTISTORE_FIELD_PREFIX . 'second_field'));

        // the added multistore checkbox must have the correct `multistore_configuration_key` attribute
        $multistoreFirstFieldCheckboxOptions = $form->get(MultistoreCheckboxEnabler::MULTISTORE_FIELD_PREFIX . 'first_field')->getConfig()->getOptions();
        $this->assertEquals('TEST_CONFIGURATION_KEY', $multistoreFirstFieldCheckboxOptions['multistore_configuration_key']);
        $this->assertArrayHasKey('multistore_dropdown', $multistoreFirstFieldCheckboxOptions);
    }

    /**
     * @return FormInterface
     */
    private function getFormToTest(): FormInterface
    {
        $formFactory = Forms::createFormFactoryBuilder()
            ->addTypeExtension(new MultistoreExtension())
            ->getFormFactory();

        $formBuilder = $formFactory->createBuilder();

        $formBuilder
            // first field will have a multistore checkbox (it has the `multistore_configuration_key` attribute)
            ->add(
                'first_field',
                SwitchType::class,
                [
                    'required' => true,
                    'multistore_configuration_key' => 'TEST_CONFIGURATION_KEY',
                ]
            )
            // second field will not have a multistore checkbox (it doesn't have the `multistore_configuration_key` attribute)
            ->add(
                'second_field',
                SwitchType::class,
                [
                    'required' => true,
                ]
            );

        return $formBuilder->getForm();
    }

    private function mockShopContext(bool $isMultiShopUsed = true, bool $isAllShopContext = false, bool $isGroupShopContext = true): ShopContext
    {
        $shopContextMock = $this->createMock(ShopContext::class);
        $shopContextMock->method('isMultiShopUsed')->willReturn($isMultiShopUsed);

        // We only need to mock getShopConstraint, all the related method to check shop context derive from it
        if ($isAllShopContext) {
            $shopConstraint = ShopConstraint::allShops();
        } elseif ($isGroupShopContext) {
            $shopConstraint = ShopConstraint::shopGroup(1);
        } else {
            $shopConstraint = ShopConstraint::shop(1);
        }
        $shopContextMock->method('getShopConstraint')->willReturn($shopConstraint);

        return $shopContextMock;
    }

    /**
     * @return ShopConfiguration
     */
    private function createShopConfigurationMock(): ShopConfiguration
    {
        $stub = $this->createMock(ShopConfiguration::class);
        $stub->method('get')->willReturn(true);
        $stub->method('has')->willReturn(true);

        return $stub;
    }

    /**
     * @return MultistoreConfigurationDropdownRenderer
     */
    private function createMultistoreConfigurationDropdownRendererMock(): MultistoreConfigurationDropdownRenderer
    {
        $dropdownRenderer = $this->createMock(MultistoreConfigurationDropdownRenderer::class);
        $dropdownRenderer->method('renderDropdown')->willReturn('');

        return $dropdownRenderer;
    }
}
