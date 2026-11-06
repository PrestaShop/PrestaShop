<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Form\Admin\Sell\BusinessEntity;

use PrestaShopBundle\Entity\Enum\BusinessEntityStatus;
use PrestaShopBundle\Form\Admin\Sell\BusinessEntity\EditBusinessEntityStatusType;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validation;
use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class EditBusinessEntityStatusTypeTest extends TypeTestCase
{
    protected function getExtensions(): array
    {
        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')->willReturnArgument(0);

        return [
            new PreloadedExtension([new EditBusinessEntityStatusType($translator, [])], []),
            new ValidatorExtension(Validation::createValidator()),
        ];
    }

    public function testItExposesOnlyTheStatus(): void
    {
        $form = $this->factory->create(EditBusinessEntityStatusType::class);

        $this->assertSame([EditBusinessEntityStatusType::FIELD_STATUS], array_keys($form->all()));
    }

    public function testTheStatusIsMandatory(): void
    {
        $form = $this->factory->create(EditBusinessEntityStatusType::class);

        $status = $form->get(EditBusinessEntityStatusType::FIELD_STATUS)->getConfig();
        $this->assertTrue($status->getOption('required'));
        $this->assertSame([NotBlank::class], array_map('get_class', $status->getOption('constraints')));
    }

    public function testTheDropdownOffersTheFourStatusesInTheOrderTheStoryLists(): void
    {
        $choices = $this->statusChoices();

        $this->assertSame(['pending', 'active', 'inactive', 'rejected'], array_column($choices, 'value'));
    }

    /**
     * EnumType hands the case itself to the view, and the form theme translates it because the enum
     * is translatable. Drop that interface and every option silently renders as PENDING, ACTIVE…
     * with nothing else turning red, so both the contract and the resulting wording are pinned here.
     */
    public function testEveryOptionCarriesItsTranslatableLabel(): void
    {
        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')->willReturnArgument(0);

        $labels = [];
        foreach ($this->statusChoices() as $choice) {
            $this->assertInstanceOf(TranslatableInterface::class, $choice->label);
            $labels[] = $choice->label->trans($translator);
        }

        $this->assertSame(['Pending', 'Active', 'Inactive', 'Rejected'], $labels);
    }

    public function testASubmissionMapsTheStatusToItsEnumCase(): void
    {
        $form = $this->factory->create(EditBusinessEntityStatusType::class);
        $form->submit([
            EditBusinessEntityStatusType::FIELD_STATUS => BusinessEntityStatus::REJECTED->value,
        ]);

        $this->assertTrue($form->isSynchronized());
        $this->assertTrue($form->isValid());

        $data = $form->getData();
        $this->assertSame(BusinessEntityStatus::REJECTED, $data[EditBusinessEntityStatusType::FIELD_STATUS]);
    }

    public function testASubmissionWithoutAStatusIsRejected(): void
    {
        $form = $this->factory->create(EditBusinessEntityStatusType::class);
        $form->submit([]);

        $this->assertTrue($form->isSynchronized());
        $this->assertFalse($form->isValid());
    }

    public function testASubmissionCarryingAFieldTheTypeNoLongerDeclaresIsRejected(): void
    {
        $form = $this->factory->create(EditBusinessEntityStatusType::class);
        $form->submit([
            EditBusinessEntityStatusType::FIELD_STATUS => BusinessEntityStatus::ACTIVE->value,
            'comment' => 'Expired registration document',
        ]);

        $this->assertTrue($form->isSynchronized());
        $this->assertFalse($form->isValid());
    }

    /**
     * @return \Symfony\Component\Form\ChoiceList\View\ChoiceView[]
     */
    private function statusChoices(): array
    {
        return $this->factory->create(EditBusinessEntityStatusType::class)
            ->createView()
            ->children[EditBusinessEntityStatusType::FIELD_STATUS]
            ->vars['choices'];
    }
}
