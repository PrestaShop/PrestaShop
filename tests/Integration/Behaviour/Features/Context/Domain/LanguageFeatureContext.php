<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

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
