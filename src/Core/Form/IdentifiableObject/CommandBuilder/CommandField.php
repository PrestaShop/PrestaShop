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

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder;

use PrestaShop\PrestaShop\Core\Exception\InvalidArgumentException;

class CommandField
{
    /**
     * @var string
     */
    private $commandSetter;

    /**
     * @var array<int, DataField>
     */
    private $dataFields;

    /**
     * @var bool
     */
    private $isMultiShopField;

    protected function __construct(string $commandSetter, array $dataFields, bool $isMultiShopField)
    {
        if (empty($dataFields)) {
            throw new InvalidArgumentException(
                sprintf('No data field provided to command setter "%s"', $commandSetter)
            );
        }
        foreach ($dataFields as $dataField) {
            if (!$dataField instanceof DataField) {
                throw new InvalidArgumentException(sprintf(
                    'Invalid data field type "%s", expected "%s"',
                    is_object($dataField) ? get_class($dataField) : gettype($dataField),
                    DataField::class
                ));
            }
        }
        $this->commandSetter = $commandSetter;
        $this->dataFields = array_values($dataFields);
        $this->isMultiShopField = $isMultiShopField;
    }

    /**
     * Returns a new command field for single shop
     *
     * @param string $commandSetter
     * @param array<int, DataField> $dataFields
     *
     * @return static
     */
    public static function createAsSingleShop(string $commandSetter, array $dataFields): self
    {
        return new static($commandSetter, $dataFields, false);
    }

    /**
     * Returns a new command field for multiple shops
     *
     * @param string $commandSetter
     * @param array<int, DataField> $dataFields
     *
     * @return static
     */
    public static function createAsMultiShop(string $commandSetter, array $dataFields): self
    {
        return new static($commandSetter, $dataFields, true);
    }

    /**
     * @return string
     */
    public function getCommandSetter(): string
    {
        return $this->commandSetter;
    }

    /**
     * @return array<int, DataField>
     */
    public function getDataFields(): array
    {
        return $this->dataFields;
    }

    /**
     * @return bool
     */
    public function isMultiShopField(): bool
    {
        return $this->isMultiShopField;
    }
}
