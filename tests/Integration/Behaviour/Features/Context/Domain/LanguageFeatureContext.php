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

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Behat\Gherkin\Node\TableNode;
use Cache;
use PHPUnit\Framework\Assert as Assert;
use PrestaShop\PrestaShop\Core\Domain\Language\Command\AddLanguageCommand;
use PrestaShop\PrestaShop\Core\Domain\Language\Command\BulkDeleteLanguagesCommand;
use PrestaShop\PrestaShop\Core\Domain\Language\Command\BulkToggleLanguagesStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Language\Command\DeleteLanguageCommand;
use PrestaShop\PrestaShop\Core\Domain\Language\Command\EditLanguageCommand;
use PrestaShop\PrestaShop\Core\Domain\Language\Command\ToggleLanguageStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Language\Exception\DefaultLanguageException;
use PrestaShop\PrestaShop\Core\Domain\Language\Exception\LanguageException;
use PrestaShop\PrestaShop\Core\Domain\Language\Exception\LanguageNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Language\Query\GetLanguageForEditing;
use PrestaShop\PrestaShop\Core\Domain\Language\QueryResult\EditableLanguage;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use RuntimeException;
use Tests\Integration\Behaviour\Features\Context\SharedStorage;

class LanguageFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @Given I add a new language with the following details:
     *
     * @param TableNode $table
     */
    public function addNewLanguage(TableNode $table): void
    {
        $data = $table->getRowsHash();

        copy(
            dirname(__DIR__, 5) . '/Resources/assets/new_logo.jpg',
            _PS_TMP_IMG_DIR_ . $data['isoCode'] . '.jpg'
        );
        copy(
            dirname(__DIR__, 5) . '/Resources/assets/new_logo.jpg',
            _PS_TMP_IMG_DIR_ . $data['isoCode'] . '_no.jpg'
        );

        try {
            /** @var LanguageId $languageId */
            $languageId = $this->getCommandBus()->handle(new AddLanguageCommand(
                $data['name'],
                $data['isoCode'],
                $data['tagIETF'],
                $data['shortDateFormat'],
                $data['fullDateFormat'],
                _PS_TMP_IMG_DIR_ . $data['isoCode'] . '.jpg',
                _PS_TMP_IMG_DIR_ . $data['isoCode'] . '_no.jpg',
                (bool) $data['isRtl'],
                (bool) $data['isActive'],
                [
                    SharedStorage::getStorage()->get($data['shop']),
                ]
            ));

            SharedStorage::getStorage()->set($data['isoCode'], $languageId->getValue());
        } catch (LanguageException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @Given I update the language with ISOCode :isoCode with the following details:
     *
     * @param string $isoCode
     * @param TableNode $table
     */
    public function updateLanguage(string $isoCode, TableNode $table): void
    {
        $languageId = SharedStorage::getStorage()->get($isoCode);

        $editableLanguage = new EditLanguageCommand((int) $languageId);
        $editableLanguage->setIsoCode($isoCode);

        $data = $table->getRowsHash();
        if (isset($data['name'])) {
            $editableLanguage->setName($data['name']);
        }
        if (isset($data['isoCode'])) {
            $editableLanguage->setIsoCode($data['isoCode']);
        }
        if (isset($data['tagIETF'])) {
            $editableLanguage->setTagIETF($data['tagIETF']);
        }
        if (isset($data['shortDateFormat'])) {
            $editableLanguage->setShortDateFormat($data['shortDateFormat']);
        }
        if (isset($data['fullDateFormat'])) {
            $editableLanguage->setFullDateFormat($data['fullDateFormat']);
        }
        if (isset($data['isRtl'])) {
            $editableLanguage->setIsRtl((bool) $data['isRtl']);
        }
        if (isset($data['isActive'])) {
            $editableLanguage->setIsActive((bool) $data['isActive']);
        }

        try {
            $this->getCommandBus()->handle($editableLanguage);
        } catch (LanguageException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I delete the language with ISOCode :isoCode
     *
     * @param string $isoCode
     */
    public function deleteLanguage(string $isoCode): void
    {
        $languageId = SharedStorage::getStorage()->get($isoCode);

        try {
            $this->getCommandBus()->handle(new DeleteLanguageCommand($languageId));
            SharedStorage::getStorage()->clear($languageId);

            // Important to clean this cache or Language::getIdByIso still returns stored value for next adding
            Cache::clean('Language::getIdByIso_*');
        } catch (LanguageException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I bulk delete languages with ISOCode :isoCode
     *
     * @param string $isoCodes
     */
    public function bulkDeleteLanguage(string $isoCodes): void
    {
        $isoCodes = explode(',', $isoCodes);
        $languageIds = [];
        foreach ($isoCodes as $isoCode) {
            $languageIds[] = SharedStorage::getStorage()->get($isoCode);
        }

        try {
            $this->getCommandBus()->handle(new BulkDeleteLanguagesCommand($languageIds));

            // Important to clean this cache or Language::getIdByIso still returns stored value for next adding
            Cache::clean('Language::getIdByIso_*');

            foreach ($languageIds as $languageId) {
                SharedStorage::getStorage()->clear($languageId);
            }
        } catch (LanguageException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When /^I (enable|disable) the language with ISOCode "([a-z]{2})"$/
     *
     * @param string $statusVerb
     * @param string $isoCode
     */
    public function setStatusLanguage(string $statusVerb, string $isoCode): void
    {
        try {
            $this->getCommandBus()->handle(
                new ToggleLanguageStatusCommand(
                    SharedStorage::getStorage()->get($isoCode),
                    $statusVerb === 'enable'
                )
            );
        } catch (LanguageException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When /^I bulk (enable|disable) languages with ISOCode "([a-z,]+)"$/
     *
     * @param string $statusVerb
     * @param string $isoCodes
     */
    public function bulkSetStatusLanguage(string $statusVerb, string $isoCodes): void
    {
        $isoCodes = explode(',', $isoCodes);
        $languageIds = [];
        foreach ($isoCodes as $isoCode) {
            $languageIds[] = SharedStorage::getStorage()->get($isoCode);
        }

        try {
            $this->getCommandBus()->handle(
                new BulkToggleLanguagesStatusCommand($languageIds, $statusVerb === 'enable')
            );
        } catch (LanguageException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @Then the language with ISOCode :isoCode should have the following details:
     *
     * @param string $isoCode
     * @param TableNode $table
     */
    public function checkLanguageDetails(string $isoCode, TableNode $table): void
    {
        $editableLanguage = $this->getLanguage($isoCode);

        $this->assertLastErrorIsNull();

        $data = $table->getRowsHash();
        Assert::assertEquals($data['name'], $editableLanguage->getName());
        Assert::assertEquals($data['isoCode'], $editableLanguage->getIsoCode()->getValue());
        Assert::assertEquals($data['tagIETF'], $editableLanguage->getTagIETF()->getValue());
        Assert::assertEquals($data['shortDateFormat'], $editableLanguage->getShortDateFormat());
        Assert::assertEquals($data['fullDateFormat'], $editableLanguage->getFullDateFormat());
        Assert::assertEquals((bool) $data['isRtl'], $editableLanguage->isRtl());
        Assert::assertEquals((bool) $data['isActive'], $editableLanguage->isActive());
        Assert::assertEquals([
            SharedStorage::getStorage()->get($data['shop']),
        ], $editableLanguage->getShopAssociation());
    }

    /**
     * @Then the language with ISOCode :isoCode should exist
     *
     * @param string $isoCode
     */
    public function checkLanguageExists(string $isoCode): void
    {
        $this->getLanguage($isoCode);

        $this->assertLastErrorIsNull();
    }

    /**
     * @Then the language with ISOCode :isoCode shouldn't exist
     *
     * @param string $isoCode
     */
    public function checkLanguageNotExists(string $isoCode): void
    {
        $this->getLanguage($isoCode);

        $this->assertLastErrorIs(LanguageNotFoundException::class);
    }

    /**
     * @Then the language with ISOCode :isoCode should be :status
     *
     * @param string $isoCode
     */
    public function checkLanguageStatus(string $isoCode, string $status): void
    {
        if (!in_array($status, ['enabled', 'disabled'])) {
            throw new RuntimeException(sprintf('A status should be "enabled" or "disabled", not "%s"', $status));
        }

        $editableLanguage = $this->getLanguage($isoCode);

        $this->assertLastErrorIsNull();
        Assert::assertEquals($status === 'enabled', $editableLanguage->isActive());
    }

    /**
     * @Then I should get an error that a default language can't be deleted
     */
    public function checkErrorDefaultLanguageCantBeDeleted(): void
    {
        $this->assertLastErrorIs(
            DefaultLanguageException::class,
            DefaultLanguageException::CANNOT_DELETE_DEFAULT_ERROR
        );
    }

    /**
     * @Then I should get an error that a default language can't be disabled
     */
    public function checkErrorDefaultLanguageCantBeDisabled(): void
    {
        $this->assertLastErrorIs(
            DefaultLanguageException::class,
            DefaultLanguageException::CANNOT_DISABLE_ERROR
        );
    }

    /**
     * @param string $isoCode
     *
     * @return EditableLanguage|null
     */
    private function getLanguage(string $isoCode): ?EditableLanguage
    {
        try {
            return $this->getQueryBus()->handle(
                new GetLanguageForEditing(SharedStorage::getStorage()->get($isoCode))
            );
        } catch (LanguageException $e) {
            $this->setLastException($e);
        }

        return null;
    }
}
