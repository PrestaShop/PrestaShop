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

namespace PrestaShop\PrestaShop\Adapter\ImageType\QueryHandler;

use PrestaShop\PrestaShop\Adapter\ImageType\AbstractImageTypeHandler;
use PrestaShop\PrestaShop\Core\Domain\ImageType\Query\GetImageTypeForEditing;
use PrestaShop\PrestaShop\Core\Domain\ImageType\QueryHandler\GetImageTypeForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\ImageType\QueryResult\EditableImageType;

/**
 * Handles query which gets image type for editing.
 */
final class GetImageTypeForEditingHandler extends AbstractImageTypeHandler implements GetImageTypeForEditingHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(GetImageTypeForEditing $query): EditableImageType
    {
        $imageType = $this->getImageType($query->getImageTypeId());

        return new EditableImageType(
            $query->getImageTypeId(),
            $imageType->name,
            $imageType->width,
            $imageType->height,
            (bool) $imageType->products,
            (bool) $imageType->categories,
            (bool) $imageType->manufacturers,
            (bool) $imageType->suppliers,
            (bool) $imageType->stores
        );
    }
}
