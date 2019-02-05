<?php
/**
 * 2007-2018 PrestaShop
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Domain\Language\Command;

use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CategoryConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Language\Exception\LanguageConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;

/**
 * Enables/disables languages status
 */
class BulkToggleLanguagesStatusCommand
{
    /**
     * @var LanguageId[]
     */
    private $languageIds = [];

    /**
     * @var bool
     */
    private $expectedStatus;

    /**
     * @param int[] $languageIds
     * @param bool $expectedStatus
     */
    public function __construct(array $languageIds, $expectedStatus)
    {
        $this->assertStatusIsBool($expectedStatus);

        $this->setLanguages($languageIds);
        $this->expectedStatus = $expectedStatus;
    }

    /**
     * @return LanguageId[]
     */
    public function getLanguageIds()
    {
        return $this->languageIds;
    }

    /**
     * @return bool
     */
    public function getStatus()
    {
        return $this->expectedStatus;
    }

    /**
     * @param int[] $languageIds
     */
    private function setLanguages(array $languageIds)
    {
        if (empty($languageIds)) {
            throw new CategoryConstraintException('Languages must be provided in order to toggle their status');
        }

        foreach ($languageIds as $languageId) {
            $this->languageIds[] = new LanguageId($languageId);
        }
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
