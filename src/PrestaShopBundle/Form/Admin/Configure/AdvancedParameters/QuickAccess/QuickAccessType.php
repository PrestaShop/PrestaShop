<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\QuickAccess;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\DefaultLanguage;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class QuickAccessType extends TranslatorAwareType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TranslatableType::class, [
                'label' => $this->trans('Name', 'Admin.Global'),
                'constraints' => [
                    new DefaultLanguage(),
                ],
                'options' => [
                    'constraints' => [
                        new Length([
                            'max' => 32,
                            'maxMessage' => $this->trans(
                                'This field cannot be longer than %limit% characters',
                                'Admin.Notifications.Error',
                                ['%limit%' => 32]
                            ),
                        ]),
                    ],
                ],
            ])
            ->add('link', TextType::class, [
                'label' => $this->trans('URL', 'Admin.Global'),
                'help' => $this->trans(
                    'The URL must start with "index.php" (e.g. index.php?controller=AdminProducts).',
                    'Admin.Advparameters.Help'
                ),
                'constraints' => [
                    new NotBlank([
                        'message' => $this->trans(
                            'The %s field is required.',
                            'Admin.Notifications.Error',
                            [sprintf('"%s"', $this->trans('URL', 'Admin.Global'))]
                        ),
                    ]),
                    new Length([
                        'max' => 255,
                        'maxMessage' => $this->trans(
                            'This field cannot be longer than %limit% characters',
                            'Admin.Notifications.Error',
                            ['%limit%' => 255]
                        ),
                    ]),
                    new Regex([
                        'pattern' => '/^index\.php/',
                        'message' => $this->trans(
                            'The URL must point to a back office page (must start with "index.php").',
                            'Admin.Advparameters.Notification'
                        ),
                    ]),
                ],
            ])
            ->add('new_window', SwitchType::class, [
                'label' => $this->trans('Open in new tab', 'Admin.Navigation.Header'),
                'required' => false,
            ]);
    }
}
