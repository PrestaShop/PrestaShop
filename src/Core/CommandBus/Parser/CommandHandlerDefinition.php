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

namespace PrestaShop\PrestaShop\Core\CommandBus\Parser;

class CommandHandlerDefinition
{
    public const TYPE_COMMAND = 'command';
    public const TYPE_QUERY = 'query';

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $handlerClass;

    /**
     * @var string
     */
    private $commandClass;

    /**
     * @var array
     */
    private $commandConstructorParams;

    /**
     * @var string
     */
    private $description;

    /**
     * @var string|null
     */
    private $returnType;

    /**
     * @var array
     */
    private $handlerInterfaces;

    /**
     * @param string $type command or query
     * @param string $handlerClass
     * @param string $commandClass
     * @param array $commandConstructorParams
     * @param string $description
     * @param string|null $returnType
     * @param array $handlerInterfaces
     */
    public function __construct(
        string $type,
        string $handlerClass,
        string $commandClass,
        array $commandConstructorParams,
        string $description,
        ?string $returnType,
        array $handlerInterfaces
    ) {
        $this->type = $type;
        $this->handlerClass = $handlerClass;
        $this->commandClass = $commandClass;
        $this->commandConstructorParams = $commandConstructorParams;
        $this->returnType = $returnType;
        $this->description = $description;
        $this->handlerInterfaces = $handlerInterfaces;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getHandlerClass(): string
    {
        return $this->handlerClass;
    }

    /**
     * @return string
     */
    public function getCommandClass(): string
    {
        return $this->commandClass;
    }

    /**
     * @return array
     */
    public function getCommandConstructorParams(): array
    {
        return $this->commandConstructorParams;
    }

    /**
     * @return string|null
     */
    public function getReturnType(): ?string
    {
        return $this->returnType;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return array
     */
    public function getHandlerInterfaces(): array
    {
        return $this->handlerInterfaces;
    }
}
