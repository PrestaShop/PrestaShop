<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Form\Admin\Sell\Customer;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Form\ChoiceProvider\GroupByIdChoiceProvider;
use PrestaShop\PrestaShop\Adapter\Language\LanguageDataProvider;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Form\ChoiceProvider\LanguageByIdChoiceProvider;
use PrestaShop\PrestaShop\Core\Security\PasswordPolicyConfiguration;
use PrestaShopBundle\Form\Admin\Sell\Customer\CustomerType;
use PrestaShopBundle\Form\FormCloner;
use ReflectionClass;
use ReflectionProperty;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Contracts\Translation\TranslatorInterface;

class CustomerTypeTest extends TestCase
{
    public function testItLoadsLanguageChoicesDuringBuildFormUsingShopIdOption(): void
    {
        $expectedShopId = 42;
        $expectedChoices = [
            'English' => 1,
            'French' => 2,
        ];
        $capturedLanguageOptions = null;

        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')->willReturnCallback(static function (string $id): string {
            return $id;
        });

        $configuration = $this->createMock(ConfigurationInterface::class);
        $configuration->method('get')->willReturnCallback(static function (string $key) {
            return match ($key) {
                PasswordPolicyConfiguration::CONFIGURATION_MINIMUM_SCORE => 0,
                PasswordPolicyConfiguration::CONFIGURATION_MAXIMUM_LENGTH => 255,
                PasswordPolicyConfiguration::CONFIGURATION_MINIMUM_LENGTH => 8,
                default => null,
            };
        });

        $languageDataProvider = $this->createMock(LanguageDataProvider::class);
        $languageDataProvider
            ->expects($this->once())
            ->method('getLanguages')
            ->with(true, $expectedShopId)
            ->willReturn([
                ['id_lang' => 1, 'name' => 'English'],
                ['id_lang' => 2, 'name' => 'French'],
            ])
        ;

        $groupProviderPlaceholder = (new ReflectionClass(GroupByIdChoiceProvider::class))
            ->newInstanceWithoutConstructor();

        $customerType = new CustomerType(
            $translator,
            $groupProviderPlaceholder,
            [],
            [],
            false,
            true,
            $configuration,
            $this->createMock(FormCloner::class),
            new LanguageByIdChoiceProvider($languageDataProvider)
        );

        $groupProviderProperty = new ReflectionProperty(CustomerType::class, 'groupByIdChoiceProvider');
        $groupProviderProperty->setValue($customerType, new class() {
            public function getChoices(): array
            {
                return [];
            }
        });

        /** @var FormBuilderInterface&MockObject $builder */
        $builder = $this->createMock(FormBuilderInterface::class);
        $builder
            ->method('add')
            ->willReturnCallback(function (string $name, ?string $type = null, array $options = []) use (&$capturedLanguageOptions, $builder) {
                if ('language_id' === $name) {
                    $capturedLanguageOptions = $options;
                }

                return $builder;
            })
        ;
        $builder
            ->method('addEventListener')
            ->willReturnCallback(function (string $eventName, callable $listener) use ($builder) {
                if (FormEvents::PRE_SUBMIT === $eventName) {
                    $this->assertIsCallable($listener);
                }

                return $builder;
            })
        ;

        $customerType->buildForm($builder, [
            'is_password_required' => false,
            'show_guest_field' => false,
            'shop_id' => $expectedShopId,
        ]);

        $this->assertNotNull($capturedLanguageOptions);
        $this->assertSame($expectedChoices, $capturedLanguageOptions['choices']);
    }
}
