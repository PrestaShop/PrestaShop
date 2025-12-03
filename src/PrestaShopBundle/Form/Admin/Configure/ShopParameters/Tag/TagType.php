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

namespace PrestaShopBundle\Form\Admin\Configure\ShopParameters\Tag;

use PrestaShopBundle\Form\Admin\Type\ProductSearchType;
use PrestaShopBundle\Translation\TranslatorAwareTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class TagType
 */
class TagType extends AbstractType
{
    use TranslatorAwareTrait;
    /**
     * @var array
     */
    private $languagesChoices;

    /**
     * @param array $languagesChoices
     */
    public function __construct(
        array $languagesChoices,
        TranslatorInterface $translator,
    ) {
        $this->languagesChoices = $languagesChoices;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => $this->trans('Name', [], 'Admin.Global'),
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('language', ChoiceType::class, [
                'label' => $this->trans('Language', [], 'Admin.Global'),
                'choices' => $this->languagesChoices,
            ])
            ->add('products', ProductSearchType::class, [
                'include_combinations' => false,
                'label' => $this->trans('Products', [], 'Admin.Catalog.Feature'),
                'min_length' => 3,
                'limit' => 0,
            ])
        ;
    }
}
