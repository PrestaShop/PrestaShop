<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Integration\Behaviour\Features\Context;

use Behat\Behat\Context\Context as BehatContext;
use Behat\Gherkin\Node\TableNode;
use Language;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use RuntimeException;

/**
 * PrestaShopFeatureContext provides behat hooks to perform necessary operations for testing:
 * - shop setup
 * - database reset between scenario
 * - cache clear between steps
 * - ...
 */
abstract class AbstractPrestaShopFeatureContext implements BehatContext
{
    use SharedStorageTrait;

    protected function checkFixtureExists(array $fixtures, $fixtureName, $fixtureIndex)
    {
        $searchLength = 10;

        if (!isset($fixtures[$fixtureIndex])) {
            $fixtureNames = array_keys($fixtures);
            $firstFixtureNames = array_splice($fixtureNames, 0, $searchLength);
            $firstFixtureNamesStr = implode(',', $firstFixtureNames);
            throw new RuntimeException(sprintf(
                '%s named "%s" was not added in fixtures. First %d added are: %s',
                $fixtureName,
                $fixtureIndex,
                $searchLength,
                $firstFixtureNamesStr
            ));
        }
    }

    /**
     * @param string $reference
     *
     * @return int
     */
    protected function referenceToId(string $reference): int
    {
        if (!$this->getSharedStorage()->exists($reference)) {
            throw new RuntimeException(sprintf('Reference %s does not exist in shared storage', $reference));
        }

        return $this->getSharedStorage()->get($reference);
    }

    /**
     * @param string $references
     *
     * @return int[]
     */
    protected function referencesToIds(string $references): array
    {
        if (empty($references)) {
            return [];
        }

        $ids = [];
        foreach (explode(',', $references) as $reference) {
            $reference = trim($reference);

            if (!$this->getSharedStorage()->exists($reference)) {
                throw new RuntimeException(sprintf('Reference %s does not exist in shared storage', $reference));
            }

            $ids[] = $this->getSharedStorage()->get($reference);
        }

        return $ids;
    }

    /**
     * @param TableNode $tableNode
     *
     * @return array
     */
    protected function localizeByRows(TableNode $tableNode): array
    {
        return $this->parseLocalizedRow($tableNode->getRowsHash());
    }

    /**
     * @param TableNode $table
     *
     * @return array
     */
    protected function localizeByColumns(TableNode $table): array
    {
        $rows = [];
        foreach ($table->getColumnsHash() as $key => $column) {
            $row = [];
            foreach ($column as $columnName => $value) {
                $row[$columnName] = $value;
            }

            $rows[] = $this->parseLocalizedRow($row);
        }

        return $rows;
    }

    /**
     * @param array $row
     *
     * @return array
     */
    protected function parseLocalizedRow(array $row): array
    {
        $parsedRow = [];
        foreach ($row as $key => $value) {
            $localeMatch = preg_match('/\[.*?\]/', $key, $matches) ? reset($matches) : null;

            if (!$localeMatch) {
                $parsedRow[$key] = $value;
                continue;
            }

            $propertyName = str_replace($localeMatch, '', $key);
            $locale = str_replace(['[', ']'], '', $localeMatch);

            $langId = (int) Language::getIdByLocale($locale, true);

            if (!$langId) {
                throw new RuntimeException(sprintf('Language by locale "%s" was not found', $locale));
            }

            $parsedRow[$propertyName][$langId] = $value;
        }

        return $parsedRow;
    }

    /**
     * @param string $localizedValue
     *
     * @return array
     */
    protected function localizeByCell(string $localizedValue): array
    {
        $localizedValues = [];
        $valuesByLang = explode(';', $localizedValue);
        foreach ($valuesByLang as $valueByLang) {
            $value = explode(':', $valueByLang);
            $langId = (int) Language::getIdByLocale($value[0], true);
            $localizedValues[$langId] = $value[1];
        }

        return $localizedValues;
    }

    /**
     * @return int
     */
    protected function getDefaultLangId(): int
    {
        return (int) $this->getConfiguration()->get('PS_LANG_DEFAULT');
    }

    /**
     * @return int
     */
    protected function getDefaultShopId(): int
    {
        return (int) $this->getConfiguration()->get('PS_SHOP_DEFAULT');
    }

    protected function getConfiguration(): ConfigurationInterface
    {
        return CommonFeatureContext::getContainer()->get(ConfigurationInterface::class);
    }
}
