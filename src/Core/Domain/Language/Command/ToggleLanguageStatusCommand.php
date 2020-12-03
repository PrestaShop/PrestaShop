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

namespace PrestaShop\PrestaShop\Core\Domain\Language\Command;

use PrestaShop\PrestaShop\Core\Domain\Language\Exception\LanguageConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;

/**
 * Enables or disables language based in given status
 */
class ToggleLanguageStatusCommand
{
    /**
     * @var bool
     */
    private $expectedStatus;

    /**
     * @var int
     */
    private $languageId;

    /**
     * @param int $languageId
     * @param bool $expectedStatus
     *
     * @throws LanguageConstraintException Is thrown when invalid data is provided
     */
    public function __construct($languageId, $expectedStatus)
    {
        $this->assertStatusIsBool($expectedStatus);

        $this->expectedStatus = $expectedStatus;
        $this->languageId = new LanguageId($languageId);
    }

    /**
     * @return int|LanguageId
     */
    public function getLanguageId()
    {
        return $this->languageId;
    }

    /**
     * @return bool
     */
    public function getStatus()
    {
        return $this->expectedStatus;
    }

    /**
     * @param bool $status
     *
     * @throws LanguageConstraintException
     */
    private function assertStatusIsBool($status)
    {
        if (!is_bool($status)) {
            throw new LanguageConstraintException('Invalid status provided, language status must be type of "bool"');
        }
    }
}
