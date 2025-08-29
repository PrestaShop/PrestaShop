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
use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Core\Domain\Language\Command\AddLanguageCommand;
use PrestaShop\PrestaShop\Core\Domain\Language\Command\BulkDeleteLanguagesCommand;
use PrestaShop\PrestaShop\Core\Domain\Language\Command\BulkToggleLanguagesStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Language\Command\DeleteLanguageCommand;
use PrestaShop\PrestaShop\Core\Domain\Language\Command\EditLanguageCommand;
use PrestaShop\PrestaShop\Core\Domain\Language\Command\ToggleLanguageStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Language\Exception\DefaultLanguageException;
use PrestaShop\PrestaShop\Core\Domain\Language\Exception\LanguageConstraintException;
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
     * @Given I add a new language :languageReference with the following details:
     *
     * @param string $languageReference
     * @param TableNode $table
     */
    public function addNewLanguage(string $languageReference, TableNode $table): void
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

            SharedStorage::getStorage()->set($languageReference, $languageId->getValue());
        } catch (LanguageException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @Given I update the language :languageReference with the following details:
     *
     * @param string $languageReference
     * @param TableNode $table
     */
    public function updateLanguage(string $languageReference, TableNode $table): void
    {
        $editableLanguage = new EditLanguageCommand($this->referenceToId($languageReference));
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
     * @When I delete the language :languageReference
     *
     * @param string $languageReference
     */
    public function deleteLanguage(string $languageReference): void
    {
        try {
            $languageId = $this->referenceToId($languageReference);
            $this->getCommandBus()->handle(new DeleteLanguageCommand($languageId));
            SharedStorage::getStorage()->clear($languageId);

            // Important to clean this cache or Language::getIdByIso still returns stored value for next adding
            Cache::clean('Language::getIdByIso_*');
        } catch (LanguageException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I bulk delete languages :languageReferences
     *
     * @param string $languageReferences
     */
    public function bulkDeleteLanguage(string $languageReferences): void
    {
        try {
            $languageIds = $this->referencesToIds($languageReferences);
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
     * @When I toggle the language :languageReference status to :status
     *
     * @param string $status
     * @param string $languageReference
     */
    public function setStatusLanguage(string $status, string $languageReference): void
    {
        if (!in_array($status, ['enabled', 'disabled'])) {
            throw new RuntimeException(sprintf('A status should be "enabled" or "disabled", not "%s"', $status));
        }

        try {
            $this->getCommandBus()->handle(
                new ToggleLanguageStatusCommand(
                    $this->referenceToId($languageReference),
                    $status === 'enabled'
                )
            );
        } catch (LanguageException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I bulk toggle languages :languageReferences status to :status
     *
     * @param string $status
     * @param string $languageReferences
     */
    public function bulkSetStatusLanguage(string $status, string $languageReferences): void
    {
        if (!in_array($status, ['enabled', 'disabled'])) {
            throw new RuntimeException(sprintf('A status should be "enabled" or "disabled", not "%s"', $status));
        }

        try {
            $this->getCommandBus()->handle(
                new BulkToggleLanguagesStatusCommand($this->referencesToIds($languageReferences), $status === 'enabled')
            );
        } catch (LanguageException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @Then the language :languageReference should have the following details:
     *
     * @param string $languageReference
     * @param TableNode $table
     */
    public function checkLanguageDetails(string $languageReference, TableNode $table): void
    {
        $editableLanguage = $this->getLanguage($languageReference);

        $this->assertLastErrorIsNull();

        $data = $table->getRowsHash();
        Assert::assertEquals($data['name'], $editableLanguage->getName());
        Assert::assertEquals($data['isoCode'], $editableLanguage->getIsoCode());
        Assert::assertEquals($data['tagIETF'], $editableLanguage->getTagIETF());
        Assert::assertEquals($data['locale'], $editableLanguage->getLocale());
        Assert::assertEquals($data['shortDateFormat'], $editableLanguage->getShortDateFormat());
        Assert::assertEquals($data['fullDateFormat'], $editableLanguage->getFullDateFormat());
        Assert::assertEquals((bool) $data['isRtl'], $editableLanguage->isRtl());
        Assert::assertEquals((bool) $data['isActive'], $editableLanguage->isActive());
        Assert::assertEquals([
            SharedStorage::getStorage()->get($data['shop']),
        ], $editableLanguage->getShopAssociation());
    }

    /**
     * @Then the language :languageReference should exist
     *
     * @param string $languageReference
     */
    public function checkLanguageExists(string $languageReference): void
    {
        $this->getLanguage($languageReference);

        $this->assertLastErrorIsNull();
    }

    /**
     * @Then the language :languageReference shouldn't exist
     *
     * @param string $languageReference
     */
    public function checkLanguageNotExists(string $languageReference): void
    {
        $this->getLanguage($languageReference);

        $this->assertLastErrorIs(LanguageNotFoundException::class);
    }

    /**
     * @Then the language :languageReference should be :status
     *
     * @param string $languageReference
     */
    public function checkLanguageStatus(string $languageReference, string $status): void
    {
        if (!in_array($status, ['enabled', 'disabled'])) {
            throw new RuntimeException(sprintf('A status should be "enabled" or "disabled", not "%s"', $status));
        }

        $editableLanguage = $this->getLanguage($languageReference);

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
     * @Then I should get an error that a language with this ISO code already exists
     */
    public function checkDuplicateIsoCode(): void
    {
        $this->assertLastErrorIs(
            LanguageConstraintException::class,
            LanguageConstraintException::DUPLICATE_ISO_CODE
        );
    }

    /**
     * @param string $languageReference
     *
     * @return EditableLanguage|null
     */
    private function getLanguage(string $languageReference): ?EditableLanguage
    {
        try {
            return $this->getQueryBus()->handle(
                new GetLanguageForEditing($this->referenceToId($languageReference))
            );
        } catch (LanguageException $e) {
            $this->setLastException($e);
        }

        return null;
    }
}
