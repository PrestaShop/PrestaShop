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

use PrestaShop\PrestaShop\Adapter\Category\Repository\CategoryRepository;
use PrestaShop\PrestaShop\Adapter\Image\ImagePathFactory;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\CategoryId;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use PrestaShop\PrestaShop\Core\Domain\Product\Query\GetProductsForListing;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\RedirectType;

/**
 * Provide dynamic complex options to the product type (like preview data that depend
 * on product current data).
 */
class ProductFormOptionsProvider implements FormOptionsProviderInterface
{
    /**
     * @var CommandBusInterface
     */
    private $queryBus;

    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * @var LegacyContext
     */
    private $legacyContext;

    /**
     * @var ImagePathFactory
     */
    private $categoryImagePathFactory;

    /**
     * @param CommandBusInterface $queryBus
     * @param CategoryRepository $categoryRepository
     * @param LegacyContext $legacyContext
     * @param ImagePathFactory $categoryImagePathFactory
     */
    public function __construct(
        CommandBusInterface $queryBus,
        CategoryRepository $categoryRepository,
        LegacyContext $legacyContext,
        ImagePathFactory $categoryImagePathFactory
    ) {
        $this->queryBus = $queryBus;
        $this->categoryRepository = $categoryRepository;
        $this->legacyContext = $legacyContext;
        $this->categoryImagePathFactory = $categoryImagePathFactory;
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
            $entityId = $data['seo']['redirect_option']['target'];
            $languageId = (int) $this->legacyContext->getLanguage()->id;

            $dataType = $data['seo']['redirect_option']['type'] ?? RedirectType::TYPE_NOT_FOUND;
            switch ($dataType) {
                case RedirectType::TYPE_CATEGORY_PERMANENT:
                case RedirectType::TYPE_CATEGORY_TEMPORARY:
                    $entities = [
                        [
                            'id' => $entityId,
                            'name' => $this->categoryRepository->getBreadcrumb(
                                new CategoryId($entityId),
                                new LanguageId($languageId)
                            ),
                            'image' => $this->categoryImagePathFactory->getPath($entityId),
                        ],
                    ];
                    break;
                case RedirectType::TYPE_PRODUCT_PERMANENT:
                case RedirectType::TYPE_PRODUCT_TEMPORARY:
                    $entities = $this->queryBus->handle(new GetProductsForListing(
                        [$entityId],
                        $languageId
                    ));
                    break;
            }
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
