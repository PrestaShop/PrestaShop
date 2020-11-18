<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Domain\Language\Command;

use PrestaShop\PrestaShop\Core\Domain\Language\Exception\LanguageConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;

/**
 * Deletes given languages
 */
class BulkDeleteLanguagesCommand
{
    /**
     * @var LanguageId[]
     */
    private $languageIds = [];

    /**
     * @param int[] $languageIds
     */
    public function __construct(array $languageIds)
    {
        $this->setLanguageIds($languageIds);
    }

    /**
     * @return LanguageId[]
     */
    public function getLanguageIds()
    {
        return $this->languageIds;
    }

    /**
     * @param int[] $languageIds
     */
    private function setLanguageIds(array $languageIds)
    {
        if (empty($languageIds)) {
            throw new LanguageConstraintException(
                'At least one language must be provided for deleting',
                LanguageConstraintException::EMPTY_BULK_DELETE
            );
        }

        foreach ($languageIds as $languageId) {
            $this->languageIds[] = new LanguageId($languageId);
        }
    }
}
