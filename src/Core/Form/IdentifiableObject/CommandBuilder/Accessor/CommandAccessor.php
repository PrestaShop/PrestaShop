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
 *
 *     return $modificationDetected ? [$command] : [];
 *
 * You can do instead:
 *
 *     $config = new CommandAccessorConfig();
 *     $config
 *         ->addField('[number]', 'setNumber', CommandField::TYPE_INT)
 *         ->addField('[author][name]', 'setNumber', CommandField::TYPE_STRING)
 *     ;
 *     $accessor = new CommandAccessor($config);
 *
 *     return $accessor->prepareCommands($data, new CQRSCommand());
 *
 * The code is cleaner, and you can focus on your data format instead of some trivial checking. When dealing with multi
 * store commands it becomes even more interesting.
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

    /**
     * @param CommandAccessorConfig $config contains all the configuration of the fields that need to be handled by the
     *                                      accessor along with the multistore prefix, if needed
     */
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
     * This method prepares CQRS commands, based on the configuration (set in the constructor) and the provided data
     * (which is likely coming from a form or any other input).
     *
     * It can be used for two use cases:
     *
     * - Fill a single command: this is the most common use case, in this case only the single store command parameter
     * is required, the accessor will search for matching values in the flattened array input data, and fill them using
     * the appropriate setters of the command. The command is fully filled and returned via an array (which will contain
     * only one command naturally), only when a modification has been detected though.
     *
     * - Fill a single command AND an all stores command: in this particular use case you need to specify two input
     * commands as parameters. The accessor performs the same research of command fields in the input array. However, in
     * this case it will check if the data was set to be modified for ALL the stores (not just a single one). To do this
     * it checks the data for a boolean value which key matches the command field:
     *
     *      ex:
     *          The price is modified for all stored
     *          ['price' => 15.45, 'modify_all_price' => true]
     *
     *          The price is modified for all stored, but the tax for a single store only
     *          ['price' => 15.45, 'tax_rate' => 0.2, 'modify_all_price' => true]
     *
     * This indicates the target of this modification (the prefix can be configured). Depending on the target, the
     * accessor will choose to fill either the single store command OR the all stores command. Finally, it returns an
     * array of either one, two or zero commands depending on the modifications that have been detected or not (the
     * single store command is always returned before the all stores one though).
     *
     * @param array $data
     * @param mixed $singleStoreCommand
     * @param mixed|null $allStoresCommand
     *
     * @return array Returns prepared commands (if no updated field was detected an empty array is returned)
     */
    public function prepareCommands(
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
                // Data has no value for this field, this is acceptable since partial data can be sent
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
            // The checkbox parameter for all stores is not even detected, regardless of its value only the single store
            // command can be used
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
