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

namespace PrestaShopBundle\Twig\Extension;

use PrestaShopBundle\Form\Admin\Type\EntitySearchInputType;
use Symfony\Component\Form\FormView;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * This filter is mostly related with @see EntitySearchInputType to allow displaying additional
 * data. It is used in the prestashop ui kit form theme but can be used in custom form themes.
 */
class EntitySearchExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('entity_field', [$this, 'getEntityField']),
        ];
    }

    /**
     * Display the value from a form's value based on the field name. This is useful if your
     * data holds more than your form inputs so that you can display extra data (like name,
     * description, image, ...)
     *
     * Besides when the value is not present (which happens when the prototype is rendered
     * it uses the prototype mapping defined so that appropriate placeholders are placed. If
     * it can't fond one then it is automatically generated based on the field name.
     *
     * @param FormView $form
     * @param string $fieldName
     *
     * @return string
     */
    public function getEntityField(FormView $form, string $fieldName): string
    {
        if (!empty($form->vars['value'][$fieldName])) {
            return (string) $form->vars['value'][$fieldName];
        }

        $parentForm = $form->parent;
        $prototypeMapping = $parentForm->vars['prototype_mapping'] ?? [];
        $fieldPlaceholder = $prototypeMapping[$fieldName] ?? sprintf('__%s__', $fieldName);

        return $fieldPlaceholder;
    }
}
