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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Sell\Product\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

/**
 * The form type used for target expects a collection of entities, but the provider only
 * provides one because in this case only one entity is expect (data limit == 1). So this
 * transformer turns the single entity into an array and vice versa.
 */
class RedirectionTargetTransformer implements DataTransformerInterface
{
    /**
     * {@inheritDoc}
     */
    public function transform($redirectionData)
    {
        if (isset($redirectionData['target'])) {
            $redirectionData['target'] = [
                $redirectionData['target'],
            ];
        }

        return $redirectionData;
    }

    /**
     * {@inheritDoc}
     */
    public function reverseTransform($redirectionData)
    {
        // EntitySearchInputType contains a collection of hidden inputs, for redirection only one target is selected
        // and we just want to retrieve the first (and only) selected ID
        if (!empty($redirectionData['target'])) {
            $redirectionData['target'] = reset($redirectionData['target']);
        }

        return $redirectionData;
    }
}
