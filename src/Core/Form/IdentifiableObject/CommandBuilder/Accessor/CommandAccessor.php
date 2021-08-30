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

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Accessor;

use PrestaShopBundle\Form\Admin\Extension\ModifyAllShopsExtension;
use Symfony\Component\PropertyAccess\Exception\NoSuchIndexException;
use Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyAccess\PropertyPath;

/**
 * This class is inspired from the PropertyAccessor component from Symfony but it's centered around
 * prefilling command objects based on a config and the data available in the flattened form data array.
 */
class CommandAccessor
{
    /**
     * @var CommandAccessorConfig
     */
    private $config;

    /**
     * @var PropertyAccessorInterface
     */
    private $propertyAccessor;

    public function __construct(CommandAccessorConfig $config)
    {
        $this->config = $config;
        $this->propertyAccessor = PropertyAccess::createPropertyAccessorBuilder()
            ->enableExceptionOnInvalidIndex()
            ->enableExceptionOnInvalidPropertyPath()
            ->disableMagicCall()
            ->getPropertyAccessor()
        ;
    }

    /**
     * @param array $data
     * @param mixed $shopCommand
     * @param mixed|null $allShopsCommand
     *
     * @return array
     */
    public function buildCommands(
        array $data,
        $shopCommand,
        $allShopsCommand = null
    ): array {
        $modifiedCommands = [];
        foreach ($this->config->getFields() as $commandField) {
            try {
                $value = $this->propertyAccessor->getValue($data, $commandField->getDataPath());
                $castedValue = $this->castValue($value, $commandField->getType());
                $command = $this->getAppropriateCommand($data, $commandField->getDataPath(), $shopCommand, $allShopsCommand);
                $this->propertyAccessor->setValue($command, $commandField->getCommandSetter(), $castedValue);
                if (!in_array($command, $modifiedCommands)) {
                    $modifiedCommands[] = $command;
                }
            } catch (NoSuchIndexException $e) {
                // Data has no value for this field
            }
        }

        // Make sure the order of returned commands is always consistent (single shop comes first)
        $commands = [];
        if (in_array($shopCommand, $modifiedCommands)) {
            $commands[] = $shopCommand;
        }
        if (in_array($allShopsCommand, $modifiedCommands)) {
            $commands[] = $allShopsCommand;
        }

        return $commands;
    }

    /**
     * Check if the data has a mapping checkbox to modify all shops for the tested field, if so use the allShopsCommand
     *
     * @param array $data
     * @param string $dataPath
     * @param mixed $shopCommand
     * @param mixed|null $allShopsCommand
     *
     * @return mixed
     */
    private function getAppropriateCommand(
        array $data,
        string $dataPath,
        $shopCommand,
        $allShopsCommand
    ) {
        if (null === $allShopsCommand) {
            return $shopCommand;
        }

        $propertyPath = new PropertyPath($dataPath);
        $lastElement = $propertyPath->getElement($propertyPath->getLength() - 1);
        $multiShopName = ModifyAllShopsExtension::MODIFY_ALL_SHOPS_PREFIX . $lastElement;

        // Replace last element
        if (($pos = strrpos($dataPath, $lastElement)) !== false) {
            $dataPath = substr_replace($dataPath, $multiShopName, $pos, strlen($lastElement));
        }

        try {
            $modifyAll = $this->propertyAccessor->getValue($data, $dataPath);

            return $modifyAll ? $allShopsCommand : $shopCommand;
        } catch (NoSuchIndexException | NoSuchPropertyException $e) {
            return $shopCommand;
        }
    }

    /**
     * @param mixed $value
     * @param string $type
     *
     * @return bool|int|mixed|string
     */
    private function castValue($value, string $type)
    {
        switch ($type) {
            case CommandField::TYPE_STRING:
                return (string) $value;
            case CommandField::TYPE_BOOL:
                return (bool) $value;
            case CommandField::TYPE_INT:
                return (int) $value;
            default:
                return $value;
        }
    }
}
