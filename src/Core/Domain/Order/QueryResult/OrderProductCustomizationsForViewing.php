<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
