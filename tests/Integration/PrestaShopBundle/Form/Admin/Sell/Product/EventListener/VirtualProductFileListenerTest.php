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

namespace Tests\Integration\PrestaShopBundle\Form\Admin\Sell\Product\EventListener;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\TypedRegex;
use PrestaShopBundle\Form\Admin\Sell\Product\EventListener\VirtualProductFileListener;
use PrestaShopBundle\Form\Admin\Sell\Product\Stock\VirtualProductFileType;
use PrestaShopBundle\Form\Admin\Type\CommonAbstractType;
use PrestaShopBundle\Form\FormCloner;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;
use Tests\Integration\PrestaShopBundle\Form\FormListenerTestCase;

class VirtualProductFileListenerTest extends FormListenerTestCase
{
    /**
     * @var FormCloner
     */
    private $formCloner;

    /**
     * {@inheritDoc}
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->formCloner = new FormCloner();
    }

    public function testSubscribedEvents(): void
    {
        // Only events are relevant, the matching function is up to implementation
        $expectedSubscribedEvents = [
            FormEvents::PRE_SUBMIT,
        ];
        $subscribedEvents = VirtualProductFileListener::getSubscribedEvents();
        $this->assertSame($expectedSubscribedEvents, array_keys($subscribedEvents));
    }

    /**
     * @dataProvider getTestData
     *
     * @param array<string, mixed> $formData
     * @param array<string, Constraint[]> $expectedFieldConstraints
     */
    public function testUpdateFormConstraints(array $formData, array $expectedFieldConstraints): void
    {
        $form = $this->createForm(SimpleVirtualProductFileFormTest::class);
        $virtualProductFileForm = $form->get('virtual_product_file');
        $eventListener = new VirtualProductFileListener($this->formCloner);

        // first assert expected constraints in unchanged form
        $this->assertConstraints($virtualProductFileForm, $this->getDefaultConstrains());

        $eventListener->adaptFormConstraints($this->createEventMock($formData, $virtualProductFileForm));

        // then assert expected constraints in form after listener changes
        $this->assertConstraints($virtualProductFileForm, $expectedFieldConstraints);
    }

    public function getTestData(): iterable
    {
        yield 'File is being added. All constraints must remain intact 0' => [
            [
                'has_file' => '1',
            ],
            $this->getDefaultConstrains(),
        ];
        yield 'File is being added. All constraints should remain intact 1' => [
            [
                'has_file' => true,
            ],
            $this->getDefaultConstrains(),
        ];

        $constraints = $this->getDefaultConstrains();
        $constraints['file'] = [];
        yield 'Existing file info is being updated. Only file constraints should be removed 0' => [
            [
                'has_file' => true,
                'virtual_product_file_id' => 1,
                'file' => null,
            ],
            $constraints,
        ];
        yield 'Existing file info is being updated. Only file constraints should be removed 1' => [
            [
                'has_file' => true,
                'virtual_product_file_id' => '10',
            ],
            $constraints,
        ];

        yield 'Removing existing file. All constraints should be removed 0' => [
            [
                'has_file' => false,
                'virtual_product_file_id' => 10,
            ],
            [],
        ];
        yield 'Removing existing file. All constraints should be removed 1' => [
            [
                'virtual_product_file_id' => 10,
                'file' => null,
            ],
            [],
        ];
        yield 'Removing existing file. All constraints should be removed 2' => [
            [
                'virtual_product_file_id' => '100',
                'file' => '',
            ],
            [],
        ];
        yield 'Removing existing file. All constraints should be removed 3' => [
            [
                'has_file' => 0,
                'virtual_product_file_id' => '100',
            ],
            [],
        ];
        yield 'There was no file and it is not being added 0' => [
            [
                'has_file' => false,
            ],
            [],
        ];
        yield 'There was no file and it is not being added 1' => [
            [
                'has_file' => 0,
            ],
            [],
        ];
    }

    /**
     * @param FormInterface $form
     * @param array<string, Constraint[]> $expectedFieldConstraints
     */
    private function assertConstraints(FormInterface $form, array $expectedFieldConstraints): void
    {
        if (empty($expectedFieldConstraints)) {
            foreach ($form->all() as $formField) {
                $this->assertEquals([], $formField->getConfig()->getOption('constraints'));
            }

            return;
        }

        foreach ($expectedFieldConstraints as $fieldName => $constraints) {
            $formFieldConstraints = $form->get($fieldName)->getConfig()->getOption('constraints');
            if (empty($constraints)) {
                $this->assertEmpty($formFieldConstraints);
                continue;
            }

            $this->assertCount(
                count($constraints),
                $formFieldConstraints,
                'expected and actual constraints count doesn\'t match'
            );

            foreach ($constraints as $index => $expectedConstraint) {
                $actualConstraint = $formFieldConstraints[$index];
                $this->assertInstanceOf(get_class($expectedConstraint), $actualConstraint);
            }
        }
    }

    /**
     * @return array<string, Constraint[]>
     */
    private function getDefaultConstrains(): array
    {
        return [
            'file' => [
                // constraint options doesn't matter as we only assert existence of these constraints
                new File(['maxSize' => 100]),
                new NotBlank(),
            ],
            'name' => [
                new NotBlank(),
                new TypedRegex(TypedRegex::TYPE_GENERIC_NAME),
                new Length(['max' => 10]),
            ],
            'download_times_limit' => [
                new LessThanOrEqual(['value' => 10]),
            ],
            'access_days_limit' => [
                new LessThanOrEqual(['value' => 10]),
            ],
        ];
    }
}

class SimpleVirtualProductFileFormTest extends CommonAbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('virtual_product_file', VirtualProductFileType::class);
    }
}
