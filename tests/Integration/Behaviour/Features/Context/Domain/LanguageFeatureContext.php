<?php

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Behat\Gherkin\Node\TableNode;
use PrestaShop\PrestaShop\Core\Domain\Language\Command\AddLanguageCommand;
use PrestaShop\PrestaShop\Core\Domain\Language\Query\GetLanguageForEditing;
use PrestaShop\PrestaShop\Core\Domain\Language\QueryResult\EditableLanguage;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Tests\Integration\Behaviour\Features\Context\SharedStorage;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;
use Webmozart\Assert\Assert;

/**
 * Class LanguageFeatureContext
 */
class LanguageFeatureContext extends AbstractDomainFeatureContext
{
    private const SHOP_ASSOCIATION = [];

    /** @var ContainerInterface */
    private $container;

    public function __construct()
    {
        $this->container = $this->getContainer();
    }

    /**
     * @When I add new language :languageReference with following details:
     *
     * @param string $languageReference
     * @param TableNode $table
     */
    public function addNewLanguageWithFollowingDetails(string $languageReference, TableNode $table)
    {
        $testCaseData = $table->getRowsHash();

        /** @var LanguageId $languageId */
        $languageId = $this->getCommandBus()->handle(new AddLanguageCommand(
            $testCaseData['Name'],
            $testCaseData['ISO code'],
            $testCaseData['Language code'],
            $testCaseData['Date format'],
            $testCaseData['Date format (full)'],
            $testCaseData['Flag'],
            $testCaseData['"No-picture" image'],
            PrimitiveUtils::castElementInType($testCaseData['Is RTL language'], PrimitiveUtils::TYPE_BOOLEAN),
            PrimitiveUtils::castElementInType($testCaseData['Status'], PrimitiveUtils::TYPE_BOOLEAN),
            self::SHOP_ASSOCIATION
        ));

        SharedStorage::getStorage()->set($languageReference, $languageId->getValue());
    }

    /**
     * @Then I should be able to see :languageReference language edit form with following details:
     *
     * @param $languageReference
     * @param TableNode $table
     */
    public function thereIsLanguageWithFollowingDetails($languageReference, TableNode $table)
    {
        $testCaseData = $table->getRowsHash();

        /** @var EditableLanguage $editableLanguage */
        $editableLanguage = $this->getQueryBus()->handle(
            new GetLanguageForEditing(SharedStorage::getStorage()->get($languageReference))
        );
        Assert::same($testCaseData['Name'], $editableLanguage->getName());
        Assert::same($testCaseData['ISO code'], $editableLanguage->getIsoCode()->getValue());
        Assert::same($testCaseData['Language code'], $editableLanguage->getTagIETF()->getValue());
        Assert::same($testCaseData['Date format'], $editableLanguage->getShortDateFormat());
        Assert::same($testCaseData['Date format (full)'], $editableLanguage->getFullDateFormat());
        Assert::same(
            PrimitiveUtils::castElementInType($testCaseData['Is RTL language'], PrimitiveUtils::TYPE_BOOLEAN),
            $editableLanguage->isRtl()
        );
        Assert::same(
            PrimitiveUtils::castElementInType($testCaseData['Status'], PrimitiveUtils::TYPE_BOOLEAN),
            $editableLanguage->isActive()
        );
    }
}
