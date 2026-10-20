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
use Symfony\Component\Validator\Constraints\Length;
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

    public function testItExposesOnlyTheStatusAndTheComment(): void
    {
        $form = $this->factory->create(EditBusinessEntityStatusType::class);

        $this->assertSame(
            [EditBusinessEntityStatusType::FIELD_STATUS, EditBusinessEntityStatusType::FIELD_COMMENT],
            array_keys($form->all())
        );
    }

    public function testTheStatusIsMandatoryAndTheCommentIsNot(): void
    {
        $form = $this->factory->create(EditBusinessEntityStatusType::class);

        $status = $form->get(EditBusinessEntityStatusType::FIELD_STATUS)->getConfig();
        $this->assertTrue($status->getOption('required'));
        $this->assertSame([NotBlank::class], array_map('get_class', $status->getOption('constraints')));

        $comment = $form->get(EditBusinessEntityStatusType::FIELD_COMMENT)->getConfig();
        $this->assertFalse($comment->getOption('required'));
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

    public function testTheCommentIsCappedOnBothSides(): void
    {
        $comment = $this->factory->create(EditBusinessEntityStatusType::class)
            ->get(EditBusinessEntityStatusType::FIELD_COMMENT)
            ->getConfig();

        $this->assertSame(
            EditBusinessEntityStatusType::MAX_COMMENT_LENGTH,
            $comment->getOption('attr')['maxlength'],
            'The textarea must stop the merchant at the cap.'
        );

        $constraints = $comment->getOption('constraints');
        $this->assertSame([Length::class], array_map('get_class', $constraints));
        $this->assertSame(
            EditBusinessEntityStatusType::MAX_COMMENT_LENGTH,
            $constraints[0]->max,
            'The cap must also hold server side: maxlength is only an attribute, and a crafted POST ignores it.'
        );
    }

    public function testASubmissionMapsTheStatusToItsEnumCase(): void
    {
        $form = $this->factory->create(EditBusinessEntityStatusType::class);
        $form->submit([
            EditBusinessEntityStatusType::FIELD_STATUS => BusinessEntityStatus::REJECTED->value,
            EditBusinessEntityStatusType::FIELD_COMMENT => 'Expired registration document',
        ]);

        $this->assertTrue($form->isSynchronized());
        $this->assertTrue($form->isValid());

        $data = $form->getData();
        $this->assertSame(BusinessEntityStatus::REJECTED, $data[EditBusinessEntityStatusType::FIELD_STATUS]);
        $this->assertSame('Expired registration document', $data[EditBusinessEntityStatusType::FIELD_COMMENT]);
    }

    public function testASubmissionWithoutAStatusIsRejected(): void
    {
        $form = $this->factory->create(EditBusinessEntityStatusType::class);
        $form->submit([EditBusinessEntityStatusType::FIELD_COMMENT => 'No status given']);

        $this->assertTrue($form->isSynchronized());
        $this->assertFalse($form->isValid());
    }

    public function testACommentLongerThanTheCapIsRejected(): void
    {
        $form = $this->factory->create(EditBusinessEntityStatusType::class);
        $form->submit([
            EditBusinessEntityStatusType::FIELD_STATUS => BusinessEntityStatus::ACTIVE->value,
            EditBusinessEntityStatusType::FIELD_COMMENT => str_repeat('a', EditBusinessEntityStatusType::MAX_COMMENT_LENGTH + 1),
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

    public function testACommentExactlyAtTheCapIsAccepted(): void
    {
        $form = $this->factory->create(EditBusinessEntityStatusType::class);
        $form->submit([
            EditBusinessEntityStatusType::FIELD_STATUS => BusinessEntityStatus::ACTIVE->value,
            EditBusinessEntityStatusType::FIELD_COMMENT => str_repeat('a', EditBusinessEntityStatusType::MAX_COMMENT_LENGTH),
        ]);

        $this->assertTrue($form->isValid());
    }
}
