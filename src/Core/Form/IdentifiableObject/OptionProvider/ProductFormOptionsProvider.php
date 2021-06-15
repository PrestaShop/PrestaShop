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

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\OptionProvider;

use PrestaShop\PrestaShop\Adapter\Product\Options\RedirectTargetProvider;

/**
 * Provide dynamic complex options to the product type (like preview data that depend
 * on product current data).
 */
class ProductFormOptionsProvider implements FormOptionsProviderInterface
{
    /**
     * @var RedirectTargetProvider
     */
    private $targetProvider;

    /**
     * @param RedirectTargetProvider $targetProvider
     */
    public function __construct(
        RedirectTargetProvider $targetProvider
    ) {
        $this->targetProvider = $targetProvider;
    }

    /**
     * {@inheritDoc}
     */
    public function getOptions(int $id, array $data): array
    {
        return array_merge(
            [
                'virtual_product_file_id' => $data['stock']['virtual_product_file']['virtual_product_file_id'] ?? null,
            ],
            $this->getRedirectOptions($data)
        );
    }

    /**
     * @param array $data
     *
     * @return array
     */
    private function getRedirectOptions(array $data): array
    {
        $entities = null;
        if (!empty($data['seo']['redirect_option']['target'])) {
            $redirectTarget = $this->targetProvider->getRedirectTarget(
                $data['seo']['redirect_option']['type'],
                (int) $data['seo']['redirect_option']['target']
            );

            $entities = [
                [
                    'id' => $redirectTarget->getId(),
                    'name' => $redirectTarget->getName(),
                    'image' => $redirectTarget->getImage(),
                ],
            ];
        }

        return [
            'redirect_target' => $entities,
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getDefaultOptions(array $data): array
    {
        return [];
    }
}
