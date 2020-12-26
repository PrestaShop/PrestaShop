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

namespace PrestaShop\PrestaShop\Core\Domain\Hook\Command;

use PrestaShop\PrestaShop\Core\Domain\Hook\ValueObject\HookId;

/**
 * Class UpdateHookModuleStatusCommand update a given hook module status
 */
class UpdateHookModuleStatusCommand
{
    /**
     * @var hookId
     */
    private $hookId;

    /**
     * @var int
     */
    private $moduleId;

    /**
     * @var bool|null
     */
    private $status;

    /**
     * UpdateHookModuleStatusCommand constructor.
     *
     * @param int $hookId
     * @param int $moduleId
     * @param bool|null $status
     */
    public function __construct(
        int $hookId,
        int $moduleId,
        ?bool $status = null
    ) {
        $this->hookId = new HookId($hookId);
        $this->moduleId = $moduleId;
        $this->status = $status;
    }

    /**
     * @return HookId
     */
    public function getHookId(): HookId
    {
        return $this->hookId;
    }

    /**
     * @return int
     */
    public function getModuleId(): int
    {
        return $this->moduleId;
    }

    /**
     * @return bool|null
     */
    public function getStatus(): ?bool
    {
        return $this->status;
    }
}
