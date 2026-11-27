<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Behat\Gherkin\Node\TableNode;
use Configuration;
use Language;
use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Core\Context\ShopContextBuilder;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Form\Handler;
use PrestaShopBundle\Service\Form\MultistoreCheckboxEnabler;
use ReflectionProperty;
use RuntimeException;
use Shop;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\VarExporter\LazyObjectInterface;
use Tests\Integration\Behaviour\Features\Context\CommonFeatureContext;
use Tests\Resources\Resetter\ConfigurationResetter;

/**
 * Saves the blocks of the Shop Parameters > Search page the same way its controller does: the block's form is
 * submitted (so types, constraints and multistore checkboxes are involved) and then saved by its form handler.
 */
class SearchConfigurationFeatureContext extends AbstractDomainFeatureContext
{
    private const CSRF_TOKEN = 'searchConfigurationToken';

    /**
     * @BeforeFeature @restore-search-configuration-before-feature
     *
     * @AfterFeature @restore-search-configuration-after-feature
     */
    public static function restoreSearchConfiguration(): void
    {
        ConfigurationResetter::resetConfiguration();
        Configuration::resetStaticCache();
        self::loadShopContext(ShopConstraint::allShops());
    }

    /**
     * @When I save the search :block settings in all shops context with the following values:
     */
    public function saveInAllShopsContext(string $block, TableNode $table): void
    {
        self::loadShopContext(ShopConstraint::allShops());
        Assert::assertSame([], $this->submitSettings($block, $table->getRowsHash(), false));
    }

    /**
     * @When I save the search :block settings in shop :shopReference context overriding the following values:
     */
    public function saveInShopContextWithOverride(string $block, string $shopReference, TableNode $table): void
    {
        self::loadShopContext(ShopConstraint::shop($this->referenceToId($shopReference)));
        Assert::assertSame([], $this->submitSettings($block, $table->getRowsHash(), true));
    }

    /**
     * @When I save the search :block settings in shop :shopReference context without overriding the following values:
     */
    public function saveInShopContextWithoutOverride(string $block, string $shopReference, TableNode $table): void
    {
        self::loadShopContext(ShopConstraint::shop($this->referenceToId($shopReference)));
        Assert::assertSame([], $this->submitSettings($block, $table->getRowsHash(), false));
    }

    /**
     * @When saving the search :block settings in all shops context with the following values should be rejected because of :field:
     */
    public function assertSaveIsRejected(string $block, string $field, TableNode $table): void
    {
        self::loadShopContext(ShopConstraint::allShops());

        Assert::assertContains($field, $this->submitSettings($block, $table->getRowsHash(), false));
    }

    /**
     * @Then /^the search configuration "(.+)" should be (enabled|disabled) (?:in all shops context|for shop "(.+)")$/
     *
     * Status transformation handled by @see StringToBoolTransformContext
     */
    public function assertConfigurationStatus(string $key, bool $enabled, ?string $shopReference = null): void
    {
        Assert::assertSame($enabled, (bool) $this->getConfigurationValue($key, $shopReference));
    }

    /**
     * @Then /^the search configuration "(.+)" should be "(.*)" (?:in all shops context|for shop "(.+)")$/
     */
    public function assertConfigurationValue(string $key, string $value, ?string $shopReference = null): void
    {
        Assert::assertSame($value, (string) $this->getConfigurationValue($key, $shopReference));
    }

    /**
     * @Then /^shop "(.+)" should (override|not override) the search configuration "(.+)"$/
     */
    public function assertConfigurationOverride(string $shopReference, string $expectation, string $key): void
    {
        Assert::assertSame(
            $expectation === 'override',
            Configuration::hasKey($key, null, null, $this->referenceToId($shopReference))
        );
    }

    /**
     * @Then the search blacklist in language :locale should be :value in all shops context
     */
    public function assertBlacklist(string $locale, string $value): void
    {
        Assert::assertSame($value, Configuration::getGlobalValue('PS_SEARCH_BLACKLIST', $this->getLanguageId($locale)));
    }

    /**
     * @param array<string, string> $values Field name (or name[locale] for localized fields) => submitted value
     *
     * @return string[] Fields in error, empty when the settings were saved
     */
    private function submitSettings(string $block, array $values, bool $override): array
    {
        $blockId = str_replace(' ', '_', $block);
        /** @var Handler $formHandler */
        $formHandler = $this->getContainer()->get(sprintf('prestashop.admin.search_preferences.%s.form_handler', $blockId));
        $dataProvider = $this->getContainer()->get(sprintf('prestashop.admin.search_preferences.%s.data_provider', $blockId));

        // The multistore dropdowns of the form build their links from the current request
        $requestStack = $this->getContainer()->get('request_stack');
        $requestStack->push(Request::create('/'));
        $form = $formHandler->getForm();
        $requestStack->pop();

        // Like a browser: every field of the block is sent, except with the multistore checkboxes (PATCH form)
        // where the fields which are not overridden are disabled, thus missing from the request
        $isPartial = $form->getConfig()->getMethod() === Request::METHOD_PATCH;
        $submittedData = $isPartial ? [] : array_map([$this, 'toSubmittedValue'], $dataProvider->getData());
        foreach ($values as $field => $value) {
            if (preg_match('/^(.+)\[(.+)\]$/', $field, $matches)) {
                $field = $matches[1];
                $submittedData[$field][$this->getLanguageId($matches[2])] = $value;
            } else {
                $submittedData[$field] = $this->toSubmittedValue($value);
            }
            if ($override) {
                $submittedData[MultistoreCheckboxEnabler::MULTISTORE_FIELD_PREFIX . $field] = '1';
            }
        }

        // No session here: the token expected by the form is registered in the test token storage instead
        $this->getContainer()->get('security.csrf.token_storage')->setToken($form->getName(), self::CSRF_TOKEN);
        $submittedData['_token'] = self::CSRF_TOKEN;
        $form->submit($submittedData, !$isPartial);

        if (!$form->isValid()) {
            $invalidFields = [];
            /** @var FormError $error */
            foreach ($form->getErrors(true) as $error) {
                $invalidFields[] = $error->getOrigin()->getName();
            }

            return $invalidFields;
        }

        $errors = $formHandler->save($form->getData());
        if (!empty($errors)) {
            throw new RuntimeException(sprintf('Search settings could not be saved: %s', var_export($errors, true)));
        }

        return [];
    }

    /**
     * @return mixed A value as a browser would submit it
     */
    private function toSubmittedValue(mixed $value): mixed
    {
        return match (true) {
            is_array($value) => $value,
            is_bool($value) => $value ? '1' : '0',
            $value === 'true' => '1',
            $value === 'false' => '0',
            default => (string) $value,
        };
    }

    private function getLanguageId(string $locale): int
    {
        $languageId = (int) Language::getIdByLocale($locale, true);
        if (!$languageId) {
            throw new RuntimeException(sprintf('Language with locale "%s" does not exist', $locale));
        }

        return $languageId;
    }

    /**
     * The value is read for an explicit shop (or for all shops), whatever the shop context left by the last save.
     */
    private function getConfigurationValue(string $key, ?string $shopReference): mixed
    {
        if (null === $shopReference) {
            return Configuration::getGlobalValue($key);
        }
        $shopId = $this->referenceToId($shopReference);

        return Configuration::get($key, null, (int) Shop::getGroupIdFromShopId($shopId), $shopId);
    }

    /**
     * The page relies on both the shop context service (multistore checkboxes) and the legacy one (configuration
     * storage), so both are switched.
     */
    private static function loadShopContext(ShopConstraint $shopConstraint): void
    {
        /** @var ShopContextBuilder $shopContextBuilder */
        $shopContextBuilder = CommonFeatureContext::getContainer()->get('test_shop_context_builder');
        $shopContextBuilder->setShopConstraint($shopConstraint);
        $shopContextBuilder->setShopId($shopConstraint->getShopId()?->getValue() ?? (int) Configuration::get('PS_SHOP_DEFAULT'));
        $shopContextBuilder->buildLegacyContext();

        // The multistore checkboxes rely on a shop context built once, on first use: it is rebuilt from the builder
        $checkboxEnabler = CommonFeatureContext::getContainer()->get('test_multistore_checkbox_enabler');
        $shopContext = (new ReflectionProperty($checkboxEnabler, 'shopContext'))->getValue($checkboxEnabler);
        if ($shopContext instanceof LazyObjectInterface) {
            $shopContext->resetLazyObject();
        }
    }
}
