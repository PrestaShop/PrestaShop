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

use Symfony\Component\PropertyAccess\Exception\NoSuchIndexException;
use Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * This class is inspired from the PropertyAccessor component from Symfony, but it's centered around
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
     * If only single store command is provided then only this one will be prefilled, however if a command for all
     * stores is also provided then this component will check in the data (for each field) if the modification targets
     * all the stores, or not, and prefill the appropriate command accordingly.
     *
     * @param array $data
     * @param mixed $singleStoreCommand
     * @param mixed|null $allStoresCommand
     *
     * @return array Returns modified commands (if no field was detected an empty array is returned)
     */
    public function buildCommands(
        array $data,
        $singleStoreCommand,
        $allStoresCommand = null
    ): array {
        $modifiedCommands = [];
        foreach ($this->config->getFields() as $commandField) {
            try {
                $value = $this->propertyAccessor->getValue($data, $commandField->getDataPath());
                $castedValue = $this->castValue($value, $commandField->getType());

                $command = $this->getAppropriateCommand($data, $commandField, $singleStoreCommand, $allStoresCommand);
                $this->propertyAccessor->setValue($command, $commandField->getCommandSetter(), $castedValue);
                if (!in_array($command, $modifiedCommands)) {
                    $modifiedCommands[] = $command;
                }
            } catch (NoSuchIndexException $e) {
                // Data has no value for this field
            }
        }

        // Make sure the order of returned commands is always consistent (single store comes first)
        $commands = [];
        if (in_array($singleStoreCommand, $modifiedCommands)) {
            $commands[] = $singleStoreCommand;
        }
        if (in_array($allStoresCommand, $modifiedCommands)) {
            $commands[] = $allStoresCommand;
        }

        return $commands;
    }

    /**
     * Check if the data has a mapping checkbox to modify all stores for the tested field, if so use the allStoresCommand
     *
     * @param array $data
     * @param CommandField $commandField
     * @param mixed $singleStoreCommand
     * @param mixed|null $allStoresCommand
     *
     * @return mixed
     */
    private function getAppropriateCommand(
        array $data,
        CommandField $commandField,
        $singleStoreCommand,
        $allStoresCommand
    ) {
        if (null === $allStoresCommand || !$commandField->isMultistoreField()) {
            return $singleStoreCommand;
        }

        $dataPath = $commandField->getDataPath();
        $lastElement = $dataPath->getElement($dataPath->getLength() - 1);
        $modifyAllNamePrefix = $this->config->getModifyAllNamePrefix() . $lastElement;

        // Replace last element
        $stringPath = (string) $dataPath;
        if (($pos = strrpos($stringPath, $lastElement)) !== false) {
            $stringPath = substr_replace($stringPath, $modifyAllNamePrefix, $pos, strlen($lastElement));
        }

        try {
            $modifyAll = $this->propertyAccessor->getValue($data, $stringPath);

            return $modifyAll ? $allStoresCommand : $singleStoreCommand;
        } catch (NoSuchIndexException | NoSuchPropertyException $e) {
            return $singleStoreCommand;
        }
    }

    /**
     * @param mixed $value
     * @param string $type
     *
     * @return bool|int|mixed|string|array
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
            case CommandField::TYPE_ARRAY:
                return (array) $value;
            default:
                return $value;
        }
    }
}
