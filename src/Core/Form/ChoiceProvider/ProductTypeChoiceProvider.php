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

namespace PrestaShop\PrestaShop\Core\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductType;
use PrestaShop\PrestaShop\Core\Form\FormChoiceAttributeProviderInterface;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ProductTypeChoiceProvider implements FormChoiceProviderInterface, FormChoiceAttributeProviderInterface
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @param TranslatorInterface $translator
     * @param Configuration $configuration
     */
    public function __construct(
        TranslatorInterface $translator,
        Configuration $configuration
    ) {
        $this->translator = $translator;
        $this->configuration = $configuration;
    }

    /**
     * {@inheritDoc}
     */
    public function getChoicesAttributes()
    {
        return [
            $this->trans('Standard product', 'Admin.Catalog.Feature') => [
                'data-description' => $this->trans('A physical product that needs to be shipped.', 'Admin.Catalog.Feature'),
                'icon' => 'checkroom',
            ],
            $this->trans('Product with combinations', 'Admin.Catalog.Feature') => [
                'data-description' => $this->trans('A product with different variations (size, color, etc.) from which customers can choose.', 'Admin.Catalog.Feature'),
                'icon' => 'layers',
            ],
            $this->trans('Pack of products', 'Admin.Catalog.Feature') => [
                'data-description' => $this->trans('A collection of products from your catalog.', 'Admin.Catalog.Feature'),
                'icon' => 'grid_view',
            ],
            $this->trans('Virtual product', 'Admin.Catalog.Feature') => [
                'data-description' => $this->trans('An intangible product that doesn\'t require shipping. You can also add a downloadable file.', 'Admin.Catalog.Feature'),
                'icon' => 'qr_code',
            ],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getChoices()
    {
        $choices = [
            $this->trans('Standard product', 'Admin.Catalog.Feature') => ProductType::TYPE_STANDARD,
            $this->trans('Product with combinations', 'Admin.Catalog.Feature') => ProductType::TYPE_COMBINATIONS,
            $this->trans('Pack of products', 'Admin.Catalog.Feature') => ProductType::TYPE_PACK,
            $this->trans('Virtual product', 'Admin.Catalog.Feature') => ProductType::TYPE_VIRTUAL,
        ];

        if (!$this->configuration->combinationIsActive()) {
            unset($choices[$this->trans('Product with combinations', 'Admin.Catalog.Feature')]);
        }

        return $choices;
    }

    /**
     * @param string $id
     * @param string $domain
     *
     * @return string
     */
    private function trans(string $id, string $domain): string
    {
        return $this->translator->trans($id, [], $domain);
    }
}
