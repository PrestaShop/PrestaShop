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

namespace PrestaShop\PrestaShop\Core\Domain\Product\Customization\Command;

use PrestaShop\PrestaShop\Core\Domain\Product\Customization\ValueObject\CustomizationFieldId;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\ValueObject\CustomizationFieldType;

/**
 * Updates single customization field
 */
class UpdateCustomizationFieldCommand
{
    /**
     * @var CustomizationFieldId
     */
    private $customizationFieldId;

    /**
     * @var CustomizationFieldType|null
     */
    private $type;

    /**
     * @var bool|null
     */
    private $required;

    /**
     * @var bool|null
     */
    private $addedByModule;

    /**
     * @var string[]|null
     */
    private $localizedNames;

    /**
     * @param int $customizationFieldId
     */
    public function __construct(int $customizationFieldId)
    {
        $this->customizationFieldId = new CustomizationFieldId($customizationFieldId);
    }

    /**
     * @return CustomizationFieldId
     */
    public function getCustomizationFieldId(): CustomizationFieldId
    {
        return $this->customizationFieldId;
    }

    /**
     * @return CustomizationFieldType|null
     */
    public function getType(): ?CustomizationFieldType
    {
        return $this->type;
    }

    /**
     * @param int $type
     *
     * @return UpdateCustomizationFieldCommand
     */
    public function setType(int $type): UpdateCustomizationFieldCommand
    {
        $this->type = new CustomizationFieldType($type);

        return $this;
    }

    /**
     * @return bool|null
     */
    public function isRequired(): ?bool
    {
        return $this->required;
    }

    /**
     * @param bool|null $required
     *
     * @return UpdateCustomizationFieldCommand
     */
    public function setRequired(?bool $required): UpdateCustomizationFieldCommand
    {
        $this->required = $required;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function isAddedByModule(): ?bool
    {
        return $this->addedByModule;
    }

    /**
     * @param bool|null $addedByModule
     *
     * @return UpdateCustomizationFieldCommand
     */
    public function setAddedByModule(?bool $addedByModule): UpdateCustomizationFieldCommand
    {
        $this->addedByModule = $addedByModule;

        return $this;
    }

    /**
     * @return string[]|null
     */
    public function getLocalizedNames(): ?array
    {
        return $this->localizedNames;
    }

    /**
     * @param string[]|null $localizedNames [key => value] pairs where key is language id and value is the name in that language
     *
     * @return UpdateCustomizationFieldCommand
     */
    public function setLocalizedNames(?array $localizedNames): UpdateCustomizationFieldCommand
    {
        $this->localizedNames = $localizedNames;

        return $this;
    }
}
