<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\Profile;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\DefaultLanguage;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\TypedRegex;
use PrestaShop\PrestaShop\Core\Domain\Profile\ProfileSettings;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use PrestaShopBundle\Translation\TranslatorAwareTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Builds form for Profile
 */
class ProfileType extends AbstractType
{
    use TranslatorAwareTrait;

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
            ->add('name', TranslatableType::class, [
                'label' => $this->trans('Name', [], 'Admin.Global'),
                'type' => TextType::class,
                'constraints' => [
                    new DefaultLanguage(),
                ],
                'options' => [
                    'constraints' => [
                        new TypedRegex([
                            'type' => 'generic_name',
                        ]),
                        new Length([
                            'max' => ProfileSettings::NAME_MAX_LENGTH,
                            'maxMessage' => $this->trans(
                                'This field cannot be longer than %limit% characters',
                                ['%limit%' => ProfileSettings::NAME_MAX_LENGTH],
                                'Admin.Notifications.Error'
                            ),
                        ]),
                    ],
                ],
            ])
            ->add('avatarUrl', FileType::class, [
                'block_prefix' => 'avatar_url',
                'label' => $this->trans('Avatar', [], 'Admin.Global'),
                'required' => false,
                'attr' => [
                    'accept' => 'gif,jpg,jpeg,jpe,png',
                ],
            ])
        ;
    }
}
