<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Customization\Field\Command;

use PrestaShop\PrestaShop\Core\Domain\Product\Customization\Field\ValueObject\CustomizationFieldId;

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
     * @var int|null
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
     * @return int|null
     */
    public function getType(): ?int
    {
        return $this->type;
    }

    /**
     * @param int|null $type
     *
     * @return UpdateCustomizationFieldCommand
     */
    public function setType(?int $type): UpdateCustomizationFieldCommand
    {
        //@todo: validate type
        $this->type = $type;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getRequired(): ?bool
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
    public function getAddedByModule(): ?bool
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
     * @param string[]|null $localizedNames
     *
     * @return UpdateCustomizationFieldCommand
     */
    public function setLocalizedNames(?array $localizedNames): UpdateCustomizationFieldCommand
    {
        $this->localizedNames = $localizedNames;

        return $this;
    }
}
