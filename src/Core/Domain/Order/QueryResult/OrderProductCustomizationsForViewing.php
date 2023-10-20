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

namespace PrestaShop\PrestaShop\Core\Domain\Order\QueryResult;

use PrestaShop\PrestaShop\Adapter\Entity\Product;

class OrderProductCustomizationsForViewing
{
    /**
     * @var OrderProductCustomizationForViewing[]
     */
    private $textCustomizations = [];

    /**
     * @var OrderProductCustomizationForViewing[]
     */
    private $fileCustomizations = [];

    /**
     * @param OrderProductCustomizationForViewing[] $customizations
     */
    public function __construct(array $customizations)
    {
        foreach ($customizations as $customization) {
            $this->addCustomization($customization);
        }
    }

    /**
     * @param OrderProductCustomizationForViewing $customization
     */
    private function addCustomization(OrderProductCustomizationForViewing $customization): void
    {
        if (Product::CUSTOMIZE_FILE === $customization->getType()) {
            $this->fileCustomizations[] = $customization;
        } else {
            $this->textCustomizations[] = $customization;
        }
    }

    /**
     * Returns customizations of type FILE
     *
     * @return OrderProductCustomizationForViewing[]
     */
    public function getFileCustomizations(): array
    {
        return $this->fileCustomizations;
    }

    /**
     * Returns customizations of type TEXT
     *
     * @return OrderProductCustomizationForViewing[]
     */
    public function getTextCustomizations(): array
    {
        return $this->textCustomizations;
    }
}
