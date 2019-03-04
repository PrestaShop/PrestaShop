<?php
/**
 * 2007-2018 PrestaShop
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Form\Admin\Improve\Design\Theme;

use PrestaShopBundle\Form\Admin\Type\ShopRestrictionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class ThemeLogosType is used to configure theme's logos.
 */
class ShopLogosType extends AbstractType
{
    /**
     * @var bool
     */
    private $isShopFeatureUsed;

    /**
     * @param bool $isShopFeatureUsed
     */
    public function __construct($isShopFeatureUsed)
    {
        $this->isShopFeatureUsed = $isShopFeatureUsed;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('header_logo', FileType::class, [
                'required' => false,
            ])
            ->add('mail_logo', FileType::class, [
                'required' => false,
            ])
            ->add('invoice_logo', FileType::class, [
                'required' => false,
            ])
            ->add('favicon', FileType::class, [
                'required' => false,
            ])
        ;

        $this->appendWithMultiShopFormFields($builder);
    }

    private function appendWithMultiShopFormFields(FormBuilderInterface $builder)
    {
        //todo: and not is all shop context
        $isAllowedToDisplay = $this->isShopFeatureUsed;
        /** @var FormBuilderInterface $form */
        foreach ($builder as $form) {
            $builder->add($form->getName() . '_is_restricted_to_shop', ShopRestrictionType::class, [
                'attr' => [
                    'is_allowed_to_display' => $isAllowedToDisplay,
                ],
            ]);
        }
    }
}
