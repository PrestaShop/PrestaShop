<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Sell\Product\Combination;

use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Contracts\Translation\TranslatorInterface;

class LowStockThresholdType extends TranslatorAwareType
{
    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        RouterInterface $router
    ) {
        parent::__construct($translator, $locales);
        $this->router = $router;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('low_stock_alert', SwitchType::class, [
                'show_choices' => false,
                'required' => false,
                'label' => $this->trans(
                    'Receive a low stock alert by email',
                    'Admin.Catalog.Feature'
                ),
                'label_help_box' => $this->trans(
                    'The email will be sent to all users who have access to the Stock page. To modify permissions, go to [1]Advanced Parameters > Team[/1].',
                    'Admin.Catalog.Help',
                    [
                        '[1]' => sprintf(
                            '<a target="_blank" href="%s">',
                            $this->router->generate('admin_employees_index')
                        ),
                        '[/1]' => '</a>',
                    ]
                ),
                'disabling_switch' => true,
                'disabled_value' => false,
                'modify_all_shops' => true,
            ])
            ->add('threshold_value', NumberType::class, [
                'label' => false,
                'constraints' => [
                    new Type(['type' => 'numeric']),
                ],
                'required' => false,
                // These two options allow to have a default data equals to zero but displayed as empty string
                'default_empty_data' => 0,
                'empty_view_data' => null,
                'modify_all_shops' => true,
                'attr' => [
                    'class' => 'small-input',
                ],
            ]);
    }
}
