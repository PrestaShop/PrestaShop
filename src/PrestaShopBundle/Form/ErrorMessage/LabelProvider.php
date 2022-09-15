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

namespace PrestaShopBundle\Form\ErrorMessage;

use Symfony\Component\Form\FormInterface;

/** Responsible for finding what label is used for certain field in the form */
class LabelProvider
{
    /**
     * @param FormInterface $form
     * @param string $fieldName
     *
     * @return string
     */
    public function getLabel(FormInterface $form, string $fieldName): string
    {
        $view = $form->createView();
        foreach ($view->children as $child) {
            if (isset($child->vars['name']) && $fieldName === $child->vars['name']) {
                return $child->vars['label'] ?? $fieldName;
            }
        }

        /* If label not found return $fieldName as fallback */
        return $fieldName;
    }
}
