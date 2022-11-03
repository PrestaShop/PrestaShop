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

use InvalidArgumentException;
use PrestaShop\PrestaShop\Core\Util\DateTime\DateTime;
use Symfony\Component\PropertyAccess\Exception\NoSuchIndexException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * This class is inspired from the PropertyAccessor component from Symfony, but it's centered around preparing CQRS
 * command objects based on a config and the data available in the flattened form data array. The component is based on
 * a configuration which allows it to prefill a command, this reduces the size of code used since all array checking and
 * setters calling are performed by the component itself.
 *
 * For example instead of doing:
 *
 *     $modificationDetected = false;
 *     $command = new CQRSCommand();
 *     if (isset($data['number'])) {
 *         $modificationDetected = true;
 *         $command->setNumber((int) $data['number']);
 *     }
 *     if (!empty($data['author']['name'])) {
 *         $modificationDetected = true;
 *         $command->setAuthorName($data['author']['name']);
 *     }
 *     if (!empty($data['isValid'])) {
 *         $modificationDetected = true;
 *         $command->setRedirectOption(
 *             $data['seo']['redirect_option']['type'],
 *             $data['seo']['redirect_option']['target']['id'] ?? 0
 *         );
 *     }
 *
 *     return $modificationDetected ? [$command] : [];
 *
 * You can do instead:
 *
 *     $config = new CommandBuilderConfig();
 *     $config
 *         ->addField('[number]', 'setNumber', DataField::TYPE_INT)
 *         ->addField('[author][name]', 'setAuthorName', DataField::TYPE_STRING)
 *         ->addCompoundField('setRedirectOption', [
 *             '[seo][redirect_option][type]' => DataField::TYPE_STRING,
 *             '[seo][redirect_option][target][id]' => [
 *                 'type' => DataField::TYPE_INT,
 *                 'default' => 0,
 *             ],
 *         ])
 *     ;
 *     $builder = new CommandBuilder($config);
 *
 *     return $builder->buildCommands($data, new CQRSCommand());
 *
 * The code is cleaner, and you can focus on your data format instead of some trivial checking. When dealing with multi
 * shop commands it becomes even more interesting.
 */
class CommandBuilder
{
    /**
     * @var CommandBuilderConfig
     */
    private $config;

    /**
     * @var PropertyAccessorInterface
     */
    private $propertyAccessor;

    /**
     * @param CommandBuilderConfig $config contains all the configuration of the fields that need to be handled by the
     *                                     builder along with the multishop prefix, if needed
     */
    public function __construct(CommandBuilderConfig $config)
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
     * This method prepares CQRS commands, based on the configuration (set in the constructor) and the provided data
     * (which is likely coming from a form or any other input).
     *
     * It can be used for two use cases:
     *
     * - Fill a single command: this is the most common use case, in this case only the single shop command parameter
     * is required, the builder will search for matching values in the flattened array input data, and fill them using
     * the appropriate setters of the command. The command is fully filled and returned via an array (which will contain
     * only one command naturally), only when a modification has been detected though.
     *
     * - Fill a single command AND an all shops command: in this particular use case you need to specify two input
     * commands as parameters. The builder performs the same research of command fields in the input array. However, in
     * this case it will check if the data was set to be modified for ALL the shops (not just a single one). To do this
     * it checks the data for a boolean value which key matches the command field:
     *
     *      ex:
     *          The price is modified for all shops
     *          ['price' => 15.45, 'modify_all_price' => true]
     *
     *          The price is modified for all shops, but the tax for a single shop only
     *          ['price' => 15.45, 'tax_rate' => 0.2, 'modify_all_price' => true]
     *
     * This indicates the target of this modification (the prefix can be configured). Depending on the target, the
     * accessor will choose to fill either the single shop command OR the all shops command. Finally, it returns an
     * array of either one, two or zero commands depending on the modifications that have been detected or not (the
     * single shop command is always returned before the all shops one though).
     *
     * @param array $data
     * @param object $singleShopCommand
     * @param object|null $allShopsCommand
     *
     * @return array Returns prepared commands (if no updated field was detected an empty array is returned)
     */
    public function buildCommands(
        array $data,
        object $singleShopCommand,
        object $allShopsCommand = null
    ): array {
        $updatedCommands = [];

        foreach ($this->config->getFields() as $commandField) {
            $command = $this->getAppropriateCommand(
                $commandField,
                $data,
                $singleShopCommand,
                $allShopsCommand
            );
            if ($this->updateCommand($command, $commandField, $data)) {
                $updatedCommands[] = $command;
            }
        }
        // Make sure the order of returned commands is always consistent (single shop comes first)
        $commands = [];
        if (in_array($singleShopCommand, $updatedCommands, true)) {
            $commands[] = $singleShopCommand;
        }
        if (in_array($allShopsCommand, $updatedCommands, true)) {
            $commands[] = $allShopsCommand;
        }

        return $commands;
    }

    /**
     * Updates the provided command with data selected by field
     *
     * @param object $command
     * @param CommandField $commandField
     * @param array $data
     *
     * @return bool Returns true if command has been updated, or false otherwise
     */
    private function updateCommand(object $command, CommandField $commandField, array $data): bool
    {
        try {
            $setterArguments = $this->fetchDataValues($commandField, $data);
        } catch (NoSuchIndexException $exception) {
            // Data has no value for this field, this is acceptable since partial data can be submitted
            return false;
        }
        $setterMethod = $commandField->getCommandSetter();

        if (!method_exists($command, $setterMethod)) {
            throw new InvalidArgumentException(
                sprintf('Setter method "%s" not found in command "%s"', $setterMethod, get_class($command))
            );
        }
        $command->$setterMethod(...$setterArguments);

        return true;
    }

    /**
     * Check if the data has a mapping checkbox to modify all shops for the tested field, if so use the allShopsCommand
     *
     * @param CommandField $commandField
     * @param array $data
     * @param object $singleShopCommand
     * @param object|null $allShopsCommand
     *
     * @return object
     */
    private function getAppropriateCommand(
        CommandField $commandField,
        array $data,
        object $singleShopCommand,
        ?object $allShopsCommand
    ): object {
        if (null === $allShopsCommand || !$commandField->isMultiShopField()) {
            return $singleShopCommand;
        }
        // Search multi-shop checkbox in data fields
        foreach ($commandField->getDataFields() as $dataField) {
            $propertyPath = $dataField->getPropertyPath();
            $lastElement = $propertyPath->getElement($propertyPath->getLength() - 1);
            $modifyAllElement = $this->config->getModifyAllNamePrefix() . $lastElement;
            $stringPath = (string) $propertyPath;
            // Replace last element of property path to guess path of multi-shop checkbox
            $stringPath = substr_replace(
                $stringPath,
                $modifyAllElement,
                strrpos($stringPath, $lastElement),
                strlen($lastElement)
            );
            // Return multi-shop command if any of the fields is enabled by checkbox
            try {
                if ($this->propertyAccessor->getValue($data, $stringPath)) {
                    return $allShopsCommand;
                }
            } catch (NoSuchIndexException $exception) {
                // No checkbox value found in data
            }
        }

        return $singleShopCommand;
    }

    /**
     * Extracts field values from data
     *
     * @param array $data
     * @param CommandField $commandField
     *
     * @return array<int, mixed>
     *
     * @throws DataFieldException
     * @throws NoSuchIndexException
     */
    private function fetchDataValues(CommandField $commandField, array $data): array
    {
        $dataValues = [];

        foreach ($commandField->getDataFields() as $dataField) {
            try {
                $value = $this->castValue(
                    $this->propertyAccessor->getValue($data, $dataField->getPropertyPath()),
                    $dataField->getType()
                );
            } catch (NoSuchIndexException $exception) {
                if ($dataField->hasDefaultValue()) {
                    $value = $dataField->getDefaultValue();
                } else {
                    throw $exception;
                }
            }
            $dataValues[] = $value;
        }

        return $dataValues;
    }

    /**
     * Casts the provided value
     *
     * @param mixed $value
     * @param string $type
     *
     * @return mixed
     */
    private function castValue($value, string $type)
    {
        switch ($type) {
            case DataField::TYPE_STRING:
                return (string) $value;
            case DataField::TYPE_BOOL:
                return (bool) $value;
            case DataField::TYPE_INT:
                return (int) $value;
            case DataField::TYPE_ARRAY:
                return (array) $value;
            case DataField::TYPE_DATETIME:
                return DateTime::buildNullableDateTime($value);
            default:
                return $value;
        }
    }
}
