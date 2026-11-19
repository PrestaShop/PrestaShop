<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Form\Admin\Configure\ShopParameters\Store;

use PHPUnit\Framework\MockObject\MockObject;
use PrestaShop\PrestaShop\Core\Form\ConfigurableFormChoiceProviderInterface;
use PrestaShop\PrestaShop\Core\Form\FormChoiceAttributeProviderInterface;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use PrestaShop\PrestaShop\Core\Util\InternationalizedDomainNameConverter;
use PrestaShopBundle\Form\Admin\Configure\ShopParameters\Store\ContactDetailsType;
use PrestaShopBundle\Form\Admin\Type\CountryChoiceType;
use PrestaShopBundle\Form\Admin\Type\EmailType;
use PrestaShopBundle\Form\Admin\Type\MultistoreConfigurationType;
use PrestaShopBundle\Form\DataTransformer\IDNConverterDataTransformer;
use PrestaShopBundle\Form\Extension\AutoCompleteExtension;
use PrestaShopBundle\Form\Extension\MultistoreExtension;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\ConstraintValidatorFactory;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Throwable;

class ContactDetailsTypeTest extends TypeTestCase
{
    private const FRANCE_ID = 8;
    private const UNITED_STATES_ID = 21;
    private const ALABAMA_ID = 1;
    private const ALASKA_ID = 2;

    protected function getExtensions(): array
    {
        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')->willReturnArgument(0);

        /** @var FormChoiceProviderInterface&FormChoiceAttributeProviderInterface&MockObject $countriesChoiceProvider */
        $countriesChoiceProvider = $this->createMockForIntersectionOfInterfaces([
            FormChoiceProviderInterface::class,
            FormChoiceAttributeProviderInterface::class,
        ]);
        $countriesChoiceProvider->method('getChoices')->willReturn([
            'France' => self::FRANCE_ID,
            'United States' => self::UNITED_STATES_ID,
        ]);

        $statesChoiceProvider = $this->createMock(ConfigurableFormChoiceProviderInterface::class);
        $statesChoiceProvider->method('getChoices')->willReturnCallback(
            static fn (array $options): array => $options['id_country'] === self::UNITED_STATES_ID
                ? ['Alabama' => self::ALABAMA_ID, 'Alaska' => self::ALASKA_ID]
                : []
        );

        return [
            new PreloadedExtension([
                new ContactDetailsType($translator, [], $statesChoiceProvider, $this->createMock(UrlGeneratorInterface::class)),
                new CountryChoiceType($countriesChoiceProvider, $translator),
                new EmailType(new IDNConverterDataTransformer(new InternationalizedDomainNameConverter()), $translator),
                new MultistoreConfigurationType(),
            ], [
                // The multistore_configuration_key and autocomplete options come from these extensions,
                // which a standalone TypeTestCase does not load.
                FormType::class => [new MultistoreExtension()],
                ChoiceType::class => [new AutoCompleteExtension()],
            ]),
            new ValidatorExtension($this->buildValidator()),
        ];
    }

    public function testAStateOverriddenForAShopIsTakenFromTheInheritedCountryWhenTheCountryIsNotSubmitted(): void
    {
        $form = $this->factory->create(ContactDetailsType::class, $this->initialData());

        // A PATCH from a shop context where only State is overridden: the disabled Country is not submitted.
        $form->submit(['id_state' => (string) self::ALASKA_ID], false);

        $this->assertTrue($form->get('id_state')->isSynchronized());
        $this->assertSame(self::ALASKA_ID, $form->get('id_state')->getData());
    }

    public function testTheStatesFollowTheSubmittedCountry(): void
    {
        $form = $this->factory->create(ContactDetailsType::class, $this->initialData());

        $form->submit(['id_country' => (string) self::FRANCE_ID, 'id_state' => (string) self::ALABAMA_ID], false);

        $this->assertFalse($form->get('id_state')->isSynchronized());
    }

    private function initialData(): array
    {
        return [
            'name' => 'My US Shop',
            'email' => 'shop@example.com',
            'id_country' => self::UNITED_STATES_ID,
            'id_state' => self::ALABAMA_ID,
        ];
    }

    /**
     * TypedRegex resolves to a PrestaShop validator with its own dependencies, which a standalone
     * validator cannot build: it is stubbed out, this test is about the State choices.
     */
    private function buildValidator(): ValidatorInterface
    {
        return Validation::createValidatorBuilder()
            ->setConstraintValidatorFactory(new class() extends ConstraintValidatorFactory {
                public function getInstance(Constraint $constraint): ConstraintValidatorInterface
                {
                    try {
                        return parent::getInstance($constraint);
                    } catch (Throwable) {
                        return new class() extends ConstraintValidator {
                            public function validate($value, Constraint $constraint): void
                            {
                            }
                        };
                    }
                }
            })
            ->getValidator();
    }
}
