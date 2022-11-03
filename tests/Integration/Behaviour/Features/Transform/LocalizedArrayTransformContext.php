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

namespace Tests\Integration\Behaviour\Features\Transform;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Language;
use RuntimeException;

/**
 * Contains methods to transform string array into localized array
 */
class LocalizedArrayTransformContext implements Context
{
    /**
     * @Transform table:locale,value
     *
     * @param TableNode $tableNode
     *
     * @return array<int, string> [langId => value]
     */
    public function transformTableToLocalizedArray(TableNode $tableNode): array
    {
        $tableRows = $tableNode->getColumnsHash();

        $localizedValues = [];
        foreach ($tableRows as $row) {
            $localizedValues[$this->getIdByLocale($row['locale'])] = $row['value'];
        }

        return $localizedValues;
    }

    /**
     * @param string $locale
     *
     * @return int
     */
    private function getIdByLocale(string $locale): int
    {
        $id = (int) Language::getIdByLocale($locale, true);

        if (!$id) {
            throw new RuntimeException(sprintf('Language by locale "%s" does not exist', $locale));
        }

        return $id;
    }
}
