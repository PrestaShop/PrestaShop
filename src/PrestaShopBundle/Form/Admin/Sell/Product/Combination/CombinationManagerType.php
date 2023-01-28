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

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Sell\Product\Combination;

use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * This form type is not really used for product form data. It is actually rendered specifically via its form theme
 * (src/PrestaShopBundle/Resources/views/Admin/Sell/Catalog/Product/FormTheme/combination.html.twig) to include the
 * layout to render all the combination management controls. Among which the combination paginated list which is itself
 * rendered via a controller action from the CombinationController.
 *
 * Some form inputs will be rendered in this sub form but they actually belong to another form (CombinationListType) so
 * they have different property path from the product form and won't be handled by it (since Form::getData is based on
 * the form naming structure it will naturally ignore all those fields).
 */
class CombinationManagerType extends TranslatorAwareType
{
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['productId'] = $options['product_id'];
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'label' => $this->trans('Manage product combinations', 'Admin.Catalog.Feature'),
                'label_tag_name' => 'h2',
                'form_theme' => '@PrestaShop/Admin/Sell/Catalog/Product/FormTheme/combination.html.twig',
            ])
            ->setRequired([
                'product_id',
            ])
            ->setAllowedTypes('product_id', 'int')
        ;
    }
}
