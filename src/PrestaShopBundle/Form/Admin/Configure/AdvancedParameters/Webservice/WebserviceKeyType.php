<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\Webservice;

use PrestaShop\PrestaShop\Core\Domain\Webservice\ValueObject\Key;
use PrestaShopBundle\Form\Admin\Type\GeneratableTextType;
use PrestaShopBundle\Form\Admin\Type\Material\MaterialMultipleChoiceTableType;
use PrestaShopBundle\Form\Admin\Type\ShopChoiceTreeType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Translation\TranslatorAwareTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Is used to create form for adding/editing Webservice Key
 */
class WebserviceKeyType extends AbstractType
{
    use TranslatorAwareTrait;

    /**
     * @var bool
     */
    private $isMultistoreFeatureUsed;

    /**
     * @var array
     */
    private $resourceChoices;

    /**
     * @var array
     */
    private $permissionChoices;

    /**
     * @param bool $isMultistoreFeatureUsed
     * @param array $resourceChoices
     * @param array $permissionChoices
     */
    public function __construct(
        $isMultistoreFeatureUsed,
        array $resourceChoices,
        array $permissionChoices
    ) {
        $this->isMultistoreFeatureUsed = $isMultistoreFeatureUsed;
        $this->resourceChoices = $resourceChoices;
        $this->permissionChoices = $permissionChoices;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('key', GeneratableTextType::class, [
                'generated_value_length' => Key::LENGTH,
                'constraints' => [
                    new NotBlank([
                        'message' => $this->trans('This field is required', [], 'Admin.Notifications.Error'),
                    ]),
                    new Length([
                        'min' => Key::LENGTH,
                        'max' => Key::LENGTH,
                        'exactMessage' => $this->trans(
                            'Key length must be 32 character long.',
                            [],
                            'Admin.Advparameters.Notification'
                        ),
                    ]),
                ],
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
                'empty_data' => '',
            ])
            ->add('status', SwitchType::class, [
                'required' => false,
            ])
            ->add('permissions', MaterialMultipleChoiceTableType::class, [
                'required' => false,
                'choices' => $this->resourceChoices,
                'multiple_choices' => $this->getPermissionChoicesForResources(),
                'scrollable' => false,
                'headers_to_disable' => ['all'],
            ])
        ;

        // remove "all" configuration since it's not an actual permission
        $builder->get('permissions')->addModelTransformer(new CallbackTransformer(
            function ($value) {
                return $value;
            },
            function ($value) {
                if (isset($value['all'])) {
                    unset($value['all']);
                }

                return $value;
            }
        ));

        if ($this->isMultistoreFeatureUsed) {
            $builder->add('shop_association', ShopChoiceTreeType::class);

            $builder->get('shop_association')->addModelTransformer(new CallbackTransformer(
                function ($value) {
                    return null === $value ? [] : $value;
                },
                function ($value) {
                    return null === $value ? [] : $value;
                }
            ));
        }
    }

    /**
     * @return array
     */
    private function getPermissionChoicesForResources()
    {
        $choices = [];

        foreach ($this->permissionChoices as $name => $permission) {
            $choices[] = [
                'name' => $permission,
                'label' => $name,
                'multiple' => true,
                'choices' => $this->resourceChoices,
            ];
        }

        return $choices;
    }
}
