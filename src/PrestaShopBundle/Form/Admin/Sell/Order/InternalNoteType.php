<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Sell\Order;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\CleanHtml;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class InternalNoteType is used to add internal notes about order.
 */
class InternalNoteType extends AbstractType
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * InternalNoteType constructor.
     *
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('note', TextareaType::class, [
                'required' => false,
                'empty_data' => '',
                'attr' => [
                    'placeholder' => $this->translator->trans('Add a note on this order. It will only be visible to you.', [], 'Admin.Orderscustomers.Feature'),
                ],
                'constraints' => [
                    new CleanHtml([
                        'message' => $this->translator->trans('%s is invalid.', [], 'Admin.Notifications.Error'),
                    ]),
                ],
            ]);
    }
}
