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

@trigger_error(
    sprintf(
        '%s is deprecated since version 8.0.0 and will be removed in the next major version.',
        TypeaheadRedirectionTargetTransformer::class
    ),
    E_USER_DEPRECATED
);

/**
 * @deprecated Since 8.0.0 and will be removed in the next major version.
 *
 * This transformer was useful when the form used a TypeAhead form type, with the new EntitySearchInputType
 * it became useless because the format is more adapted by default and not complex enough to justify a transformer.
 */
class TypeaheadRedirectionTargetTransformer implements DataTransformerInterface
{
    /**
     * {@inheritDoc}
     */
    public function transform($targetId)
    {
        if (null === $targetId) {
            return null;
        }

        return [
            'data' => [
                (int) $targetId,
            ],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function reverseTransform($targetData)
    {
        // TypeaheadProductCollectionType contains a collection of hidden inputs, for redirection
        // only one target is selected and we just want to retrieve the first (and only) selected ID
        if (!isset($targetData['data'][0])) {
            return null;
        }

        return (int) $targetData['data'][0];
    }
}
