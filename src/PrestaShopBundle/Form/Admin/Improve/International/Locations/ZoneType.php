<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Improve\International\Locations;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\TypedRegex;
use PrestaShopBundle\Form\Admin\Type\ShopChoiceTreeType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class ZoneType extends AbstractType
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var bool
     */
    private $isMultistoreEnabled;

    /**
     * ZoneType constructor.
     *
     * @param TranslatorInterface $translator
     * @param bool $isMultistoreEnabled
     */
    public function __construct(TranslatorInterface $translator, bool $isMultistoreEnabled)
    {
        $this->translator = $translator;
        $this->isMultistoreEnabled = $isMultistoreEnabled;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'required' => true,
                'help' => $this->translator->trans('Zone name (e.g. Africa, West Coast, Neighboring Countries).', [], 'Admin.International.Help'),
                'label' => $this->translator->trans('Name', [], 'Admin.Global'),
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans(
                            'This field cannot be empty.', [], 'Admin.Notifications.Error'
                        ),
                    ]),
                    new Length([
                        'max' => 64,
                        'maxMessage' => $this->translator->trans(
                            'This field cannot be longer than %limit% characters.',
                            ['%limit%' => 64],
                            'Admin.Notifications.Error'
                        ),
                    ]),
                    new TypedRegex([
                        'type' => 'generic_name',
                    ]),
                ],
            ])
            ->add('enabled', SwitchType::class, [
                'required' => false,
                'help' => $this->translator->trans('Allow or disallow shipping to this zone.', [], 'Admin.International.Help'),
                'label' => $this->translator->trans('Active', [], 'Admin.Global'),
            ]);

        if ($this->isMultistoreEnabled) {
            $builder->add('shop_association', ShopChoiceTreeType::class, [
                'required' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans(
                            'This field cannot be empty.', [], 'Admin.Notifications.Error'
                        ),
                    ]),
                ],
                'label' => $this->translator->trans('Store association', [], 'Admin.Global'),
            ]);
        }
    }
}
