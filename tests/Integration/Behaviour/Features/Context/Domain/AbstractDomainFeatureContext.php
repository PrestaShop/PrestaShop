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

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Configuration;
use Currency;
use Language;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use RuntimeException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Tests\Integration\Behaviour\Features\Context\CommonFeatureContext;
use Tests\Integration\Behaviour\Features\Context\LastExceptionTrait;
use Tests\Integration\Behaviour\Features\Context\SharedStorageTrait;

abstract class AbstractDomainFeatureContext implements Context
{
    use SharedStorageTrait;
    use LastExceptionTrait;

    protected const JPG_IMAGE_TYPE = '.jpg';
    protected const JPG_IMAGE_STRING = 'iVBORw0KGgoAAAANSUhEUgAAABwAAAASCAMAAAB/2U7WAAAABl'
    . 'BMVEUAAAD///+l2Z/dAAAASUlEQVR4XqWQUQoAIAxC2/0vXZDr'
    . 'EX4IJTRkb7lobNUStXsB0jIXIAMSsQnWlsV+wULF4Avk9fLq2r'
    . '8a5HSE35Q3eO2XP1A1wQkZSgETvDtKdQAAAABJRU5ErkJggg==';

    /**
     * @return CommandBusInterface
     */
    protected function getCommandBus()
    {
        return CommonFeatureContext::getContainer()->get('prestashop.core.command_bus');
    }

    /**
     * @return CommandBusInterface
     */
    protected function getQueryBus()
    {
        return CommonFeatureContext::getContainer()->get('prestashop.core.query_bus');
    }

    protected function getContainer(): ContainerInterface
    {
        return CommonFeatureContext::getContainer();
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
        return (int) Configuration::get('PS_LANG_DEFAULT');
    }

    protected function getDefaultCurrencyId(): int
    {
        return Currency::getDefaultCurrencyId();
    }

    protected function getDefaultCurrencyIsoCode(): string
    {
        return Currency::getIsoCodeById($this->getDefaultCurrencyId());
    }

    /**
     * @return int
     */
    protected function getDefaultShopId(): int
    {
        return (int) Configuration::get('PS_SHOP_DEFAULT');
    }

    /**
     * @param array $row
     *
     * @return array
     */
    private function parseLocalizedRow(array $row): array
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
     * @param string $dirImage
     * @param string $imageName
     * @param int $objectId
     *
     * @return string
     */
    protected function pretendImageUploaded(string $dirImage, string $imageName, int $objectId): string
    {
        //@todo: refactor CategoryCoverUploader. Move uploaded file in Form handler instead of Uploader and use the uploader here in tests
        $im = imagecreatefromstring(base64_decode(self::JPG_IMAGE_STRING));
        if ($im !== false) {
            header('Content-Type: image/jpg');
            imagejpeg(
                $im,
                $dirImage . $objectId . self::JPG_IMAGE_TYPE,
                0
            );
            imagedestroy($im);
        }

        return $imageName;
    }
}
