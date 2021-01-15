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

namespace PrestaShopBundle\Form\Partial;

use Symfony\Component\Form\FormBuilder;

/**
 * PartialFormBuilder is a form builder that returns a PartialForm instead of a Form.
 * Since the class used by the Symfony FormBuilder cannot be configured we need to override
 * the builder.
 *
 * @see PartialForm
 * @see ResolvedPartialFormType
 */
class PartialFormBuilder extends FormBuilder
{
    /**
     * {@inheritdoc}
     */
    public function getForm()
    {
        // Get dispatcher before it becomes ImmutableEventDispatcher so that PartialForm can add an internal listener
        $dispatcher = $this->getEventDispatcher();

        // The purpose of this builder is only to change the type of the returned Form
        // so we use the parent method and let it build the Form as usual, then the returned
        // form is used as input for PartialForm which is mostly a wrapper
        $form = parent::getForm();

        return new PartialForm($form, $dispatcher);
    }
}
