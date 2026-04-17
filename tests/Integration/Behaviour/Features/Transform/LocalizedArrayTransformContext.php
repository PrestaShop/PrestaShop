<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
