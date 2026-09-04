<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Form\Admin\Sell\BusinessEntity;

use PrestaShop\PrestaShop\Adapter\Group\GroupDataProvider;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\CleanHtml;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\TypedRegex;
use PrestaShop\PrestaShop\Core\Form\ChoiceProvider\GroupByIdChoiceProvider;
use PrestaShopBundle\Entity\Enum\BusinessEntityStatus;
use PrestaShopBundle\Form\Admin\Sell\BusinessEntity\BusinessEntityGeneralInformationType;
use PrestaShopBundle\Form\Admin\Sell\BusinessEntity\BusinessEntityIdentityType;
use PrestaShopBundle\Form\Admin\Sell\BusinessEntity\BusinessEntitySettingsType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Extension\ColumnsNumberExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\ConstraintValidatorFactory;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Throwable;

/**
 * AC4 asks the edit page to enforce "the same validation as creation". That is guaranteed by
 * construction — both actions autowire the same form builder, hence this single type — so what is
 * worth pinning here is the contract that guarantee rests on: which fields are mandatory, which one
 * is optional, and that the mandatory ones actually carry their constraints.
 */
class BusinessEntityGeneralInformationTypeTest extends TypeTestCase
{
    protected function getExtensions(): array
    {
        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')->willReturnArgument(0);

        // GroupByIdChoiceProvider is final, so it cannot be doubled: build the real one on top of a
        // mocked data provider instead.
        $groupDataProvider = $this->createMock(GroupDataProvider::class);
        $groupDataProvider->method('getGroups')->willReturn([
            ['id_group' => 3, 'name' => 'Customer'],
            ['id_group' => 1, 'name' => 'Visitor'],
        ]);
        $groupByIdChoiceProvider = new GroupByIdChoiceProvider($groupDataProvider, 1);

        return [
            new PreloadedExtension([
                new BusinessEntityGeneralInformationType($translator, []),
                new BusinessEntityIdentityType($translator, []),
                new BusinessEntitySettingsType($translator, [], $groupByIdChoiceProvider),
                new SwitchType(),
            ], [
                // The two sections declare columns_number and two fields declare column_breaker;
                // both options come from this extension, which a standalone TypeTestCase does not load.
                FormType::class => [new ColumnsNumberExtension()],
            ]),
            // Registers the "constraints" option; the custom PrestaShop validators behind
            // TypedRegex/CleanHtml are not exercised here, only the wiring is asserted.
            new ValidatorExtension($this->buildValidator()),
        ];
    }

    /**
     * TypedRegex and CleanHtml resolve to PrestaShop validators with their own dependencies, which a
     * standalone validator cannot build. They are stubbed out: this test asserts the wiring of the
     * constraints and the mapping of a submission, not the behaviour of those two validators.
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

    public function testNameAndLegalNameAreMandatoryAndConstrained(): void
    {
        $form = $this->factory->create(BusinessEntityGeneralInformationType::class);

        foreach ([BusinessEntityGeneralInformationType::FIELD_NAME, BusinessEntityGeneralInformationType::FIELD_LEGAL_NAME] as $field) {
            $config = $this->field($form, $field)->getConfig();

            $this->assertTrue($config->getOption('required'), sprintf('"%s" must be mandatory.', $field));

            $constraints = $config->getOption('constraints');
            $this->assertNotEmpty($constraints, sprintf('"%s" must carry validation constraints.', $field));

            // All four, not just the two obvious ones: dropping TypedRegex or CleanHtml would
            // otherwise leave this test green while the field lost its sanitisation.
            $this->assertSame(
                [NotBlank::class, Length::class, TypedRegex::class, CleanHtml::class],
                array_map('get_class', $constraints),
                sprintf('"%s" must keep its four constraints, in the order the create path uses.', $field)
            );
        }
    }

    /**
     * AC4 asks for the mandatory fields to be enforced. "required => true" is only an HTML
     * attribute, and the edit page renders the form with novalidate — so without a constraint a
     * crafted POST omitting these keys was accepted, and reached the command as null: a TypeError
     * (hence a 500) for the status, and a silently persisted id_customer_group = 0 for the group.
     */
    public function testStatusAndCustomerGroupAreRejectedWhenMissing(): void
    {
        $form = $this->factory->create(BusinessEntityGeneralInformationType::class);
        $form->submit($this->submission([
            BusinessEntityGeneralInformationType::FIELD_NAME => 'Probe',
            BusinessEntityGeneralInformationType::FIELD_LEGAL_NAME => 'Probe Legal',
            BusinessEntityGeneralInformationType::FIELD_EXTERNAL_REF => 'REF',
            BusinessEntityGeneralInformationType::FIELD_DELIVERY_AUTHORIZED => '1',
        ]));

        $this->assertTrue($form->isSynchronized());
        $this->assertFalse($form->isValid(), 'A submission without status nor customer group must be rejected.');

        $fieldsInError = [];
        foreach ($form->getErrors(true) as $error) {
            $fieldsInError[] = $error->getOrigin()->getName();
        }

        $this->assertContains(BusinessEntityGeneralInformationType::FIELD_STATUS, $fieldsInError);
        $this->assertContains(BusinessEntityGeneralInformationType::FIELD_CUSTOMER_GROUP_ID, $fieldsInError);
    }

    public function testExternalRefIsOptionalSoItCanBeCleared(): void
    {
        $form = $this->factory->create(BusinessEntityGeneralInformationType::class);
        $config = $this->field($form, BusinessEntityGeneralInformationType::FIELD_EXTERNAL_REF)->getConfig();

        $this->assertFalse($config->getOption('required'));
        $this->assertEmpty($config->getOption('constraints'));
    }

    /**
     * The exact round trip a NULL external_ref takes: the data provider exposes it as '', the field
     * renders pre-filled with '', and the merchant saves without touching it.
     */
    public function testAnUntouchedEmptyExternalRefComesBackAsNullNotAsAnEmptyString(): void
    {
        $form = $this->factory->create(BusinessEntityGeneralInformationType::class, [
            BusinessEntityGeneralInformationType::FIELD_NAME => 'Stored Entity',
            BusinessEntityGeneralInformationType::FIELD_LEGAL_NAME => 'Stored Legal',
            // What BusinessEntityFormDataProvider::getData() produces for a NULL column.
            BusinessEntityGeneralInformationType::FIELD_EXTERNAL_REF => '',
            BusinessEntityGeneralInformationType::FIELD_DELIVERY_AUTHORIZED => false,
            BusinessEntityGeneralInformationType::FIELD_STATUS => BusinessEntityStatus::PENDING,
            BusinessEntityGeneralInformationType::FIELD_CUSTOMER_GROUP_ID => 3,
        ]);

        $form->submit($this->submission([
            BusinessEntityGeneralInformationType::FIELD_NAME => 'Stored Entity',
            BusinessEntityGeneralInformationType::FIELD_LEGAL_NAME => 'Stored Legal',
            BusinessEntityGeneralInformationType::FIELD_EXTERNAL_REF => '',
            BusinessEntityGeneralInformationType::FIELD_DELIVERY_AUTHORIZED => '0',
            BusinessEntityGeneralInformationType::FIELD_STATUS => BusinessEntityStatus::PENDING->value,
            BusinessEntityGeneralInformationType::FIELD_CUSTOMER_GROUP_ID => 3,
        ]));

        $this->assertNull(
            $form->getData()[BusinessEntityGeneralInformationType::FIELD_EXTERNAL_REF],
            'Symfony normalises an empty optional text field to NULL, so the column keeps its NULL.'
        );
    }

    public function testItMapsASubmissionOntoTheSixEditableFields(): void
    {
        $form = $this->factory->create(BusinessEntityGeneralInformationType::class);

        $form->submit($this->submission([
            BusinessEntityGeneralInformationType::FIELD_NAME => 'Edited Entity',
            BusinessEntityGeneralInformationType::FIELD_LEGAL_NAME => 'Edited Legal',
            BusinessEntityGeneralInformationType::FIELD_EXTERNAL_REF => '',
            BusinessEntityGeneralInformationType::FIELD_DELIVERY_AUTHORIZED => '1',
            BusinessEntityGeneralInformationType::FIELD_STATUS => BusinessEntityStatus::ACTIVE->value,
            BusinessEntityGeneralInformationType::FIELD_CUSTOMER_GROUP_ID => 3,
        ]));

        $this->assertTrue($form->isSynchronized());

        $data = $form->getData();
        $this->assertSame('Edited Entity', $data[BusinessEntityGeneralInformationType::FIELD_NAME]);
        $this->assertSame('Edited Legal', $data[BusinessEntityGeneralInformationType::FIELD_LEGAL_NAME]);
        // A cleared optional text field comes back as null, which the data handler forwards as NULL.
        $this->assertNull($data[BusinessEntityGeneralInformationType::FIELD_EXTERNAL_REF]);
        $this->assertTrue($data[BusinessEntityGeneralInformationType::FIELD_DELIVERY_AUTHORIZED]);
        $this->assertSame(BusinessEntityStatus::ACTIVE, $data[BusinessEntityGeneralInformationType::FIELD_STATUS]);
        $this->assertSame(3, $data[BusinessEntityGeneralInformationType::FIELD_CUSTOMER_GROUP_ID]);
    }

    /**
     * The two sections exist to lay the fields out and carry inherit_data, so the submitted payload
     * is nested while the resulting data stays flat — which is what the assertions above rely on.
     */
    private function sectionOf(string $field): string
    {
        return in_array($field, [
            BusinessEntityGeneralInformationType::FIELD_NAME,
            BusinessEntityGeneralInformationType::FIELD_LEGAL_NAME,
        ], true)
            ? BusinessEntityGeneralInformationType::SECTION_IDENTITY
            : BusinessEntityGeneralInformationType::SECTION_SETTINGS;
    }

    private function field(FormInterface $form, string $field): FormInterface
    {
        return $form->get($this->sectionOf($field))->get($field);
    }

    /**
     * @param array<string, mixed> $values
     *
     * @return array<string, array<string, mixed>>
     */
    private function submission(array $values): array
    {
        $submission = [
            BusinessEntityGeneralInformationType::SECTION_IDENTITY => [],
            BusinessEntityGeneralInformationType::SECTION_SETTINGS => [],
        ];

        foreach ($values as $field => $value) {
            $submission[$this->sectionOf($field)][$field] = $value;
        }

        return $submission;
    }
}
