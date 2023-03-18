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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShopBundle\Form\Admin\Configure\ShopParameters\Store;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\DefaultLanguage;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\NoTags;
use PrestaShopBundle\Form\Admin\Type\EmailType;
use PrestaShopBundle\Form\Admin\Type\ShopChoiceTreeType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class StoreType
 */
class StoreType extends TranslatorAwareType
{
    public const MAX_TITLE_LENGTH = 255;

    /**
     * @var bool
     */
    private $isShopFeatureEnabled;

    /**
     * @var DataTransformerInterface
     */
    private $singleDefaultLanguageArrayToFilledArrayDataTransformer;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param DataTransformerInterface $singleDefaultLanguageArrayToFilledArrayDataTransformer
     * @param bool $isShopFeatureEnabled
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        DataTransformerInterface $singleDefaultLanguageArrayToFilledArrayDataTransformer,
        $isShopFeatureEnabled
    ) {
        parent::__construct($translator, $locales);
        $this->isShopFeatureEnabled = $isShopFeatureEnabled;
        $this->singleDefaultLanguageArrayToFilledArrayDataTransformer = $singleDefaultLanguageArrayToFilledArrayDataTransformer;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TranslatableType::class, [
                'label' => $this->trans('Name', 'Admin.Global'),
                'help' => $this->trans('Store name (e.g. City Center Mall Store). Allowed characters: letters, spaces and %s', 'Admin.Shopparameters.Help'),
                'constraints' => [
                    new DefaultLanguage(),
                ],
            ])
        ;

        $builder->get('title')->addModelTransformer($this->singleDefaultLanguageArrayToFilledArrayDataTransformer);

        if ($this->isShopFeatureEnabled) {
            $builder->add('shop_association', ShopChoiceTreeType::class, [
                'label' => $this->trans('Store association', 'Admin.Global'),
                'constraints' => [
                    new NotBlank([
                        'message' => $this->trans(
                            'The %s field is required.',
                            'Admin.Notifications.Error',
                            [
                                sprintf('"%s"', $this->trans('Store association', 'Admin.Global')),
                            ]
                        ),
                    ]),
                ],
            ]);
        }
    }
}
