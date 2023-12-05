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

namespace PrestaShopBundle\Form\Admin\Type;

use PrestaShopBundle\Form\Admin\Type\Material\MaterialChoiceTreeType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class CategoryChoiceTreeType.
 * 
 * @link https://devdocs.prestashop-project.org/8/development/components/form/types-reference/category-choice-tree/
 */
class CategoryChoiceTreeType extends AbstractType
{
    /**
     * @var array
     */
    private $categoryTreeChoices;

    /**
     * @param array $categoryTreeChoices
     */
    public function __construct(array $categoryTreeChoices)
    {
        $this->categoryTreeChoices = $categoryTreeChoices;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choices_tree' => $this->categoryTreeChoices,
            'choice_label' => 'name',
            'choice_value' => 'id_category',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return MaterialChoiceTreeType::class;
    }
}
