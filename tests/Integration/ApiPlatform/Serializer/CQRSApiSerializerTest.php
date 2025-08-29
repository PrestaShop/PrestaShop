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

namespace Tests\Integration\ApiPlatform\Serializer;

use ApiPlatform\Metadata\HttpOperation;
use DateTimeImmutable;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\Module\APIResources\ApiPlatform\Resources\ApiClient\ApiClientList;
use PrestaShop\Module\APIResources\ApiPlatform\Resources\Hook;
use PrestaShop\Module\APIResources\ApiPlatform\Resources\Product\Product;
use PrestaShop\PrestaShop\Core\Context\CurrencyContextBuilder;
use PrestaShop\PrestaShop\Core\Context\LanguageContextBuilder;
use PrestaShop\PrestaShop\Core\Context\ShopContextBuilder;
use PrestaShop\PrestaShop\Core\Domain\ApiClient\ValueObject\CreatedApiClient;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Command\EditCartRuleCommand;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\CartRuleAction;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Command\AddCustomerGroupCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Command\EditCustomerGroupCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Query\GetCustomerGroupForEditing;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\QueryResult\EditableCustomerGroup;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\ValueObject\GroupId;
use PrestaShop\PrestaShop\Core\Domain\Module\Command\UploadModuleCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\AddProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Query\GetProductForEditing;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductType;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\RedirectType;
use PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\QueryResult\VirtualProductFileForEditing;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopCollection;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Util\DateTime\DateTime as DateTimeUtil;
use PrestaShopBundle\ApiPlatform\Metadata\LocalizedValue;
use PrestaShopBundle\ApiPlatform\NormalizationMapper;
use PrestaShopBundle\ApiPlatform\Serializer\CQRSApiSerializer;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\File\File;
use Tests\Integration\Utility\LanguageTrait;
use Tests\Resources\ApiPlatform\Resources\LocalizedResource;
use Tests\Resources\Resetter\LanguageResetter;
use Tests\Resources\ResourceResetter;

class CQRSApiSerializerTest extends KernelTestCase
{
    use LanguageTrait;

    protected const EN_LANG_ID = 1;

    protected static ?int $frenchLangId = null;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        // BAckup modules because language installation modifies them
        (new ResourceResetter())->backupTestModules();

        // Reset languages and reinstall the french one to make sure we have the correct data in DB
        // The self::$frenchLangId was initialized early with the data providers but in the meantime the DB may have been cleaned
        // by other tests, we reset it and create new fr language, since the DB increment value is reset the ID should be 2
        LanguageResetter::resetLanguages();
        self::$frenchLangId = self::addLanguageByLocale('fr-FR');
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        self::$frenchLangId = null;
        LanguageResetter::resetLanguages();
    }

    protected function setUp(): void
    {
        parent::setUp();

        /** @var ShopContextBuilder $shopContextBuilder */
        $shopContextBuilder = self::getContainer()->get('test_shop_context_builder');
        $shopContextBuilder->setShopConstraint(ShopConstraint::shop(1));
        $shopContextBuilder->setShopId(1);

        /** @var LanguageContextBuilder $languageContextBuilder */
        $languageContextBuilder = self::getContainer()->get('test_language_context_builder');
        $languageContextBuilder->setLanguageId(1);
        $languageContextBuilder->setDefaultLanguageId(1);

        /** @var CurrencyContextBuilder $currencyContextBuilder */
        $currencyContextBuilder = self::getContainer()->get('test_currency_context_builder');
        $currencyContextBuilder->setCurrencyId(1);
    }

    protected static function getFrenchId(): int
    {
        if (empty(self::$frenchLangId)) {
            LanguageResetter::resetLanguages();
            self::$frenchLangId = self::addLanguageByLocale('fr-FR');
        }

        return self::$frenchLangId;
    }

    public function testDecoration(): void
    {
        // Test that CQRSApiSerializer correctly decorates the API Platform serializer
        $apiPlatformSerializer = self::getContainer()->get('api_platform.serializer');
        $this->assertTrue(is_a($apiPlatformSerializer, CQRSApiSerializer::class));

        // But we don't want to impact the global serializer
        $globalSerializer = self::getContainer()->get('serializer');
        $this->assertFalse(is_a($globalSerializer, CQRSApiSerializer::class));
    }

    public function testDenormalize(): void
    {
        $serializer = self::getContainer()->get(CQRSApiSerializer::class);

        foreach ($this->getExpectedDenormalizedData() as $useCase => $denormalizationData) {
            list($dataToDenormalize, $denormalizedObject, $normalizationMapping, $type, $extraContext) = array_pad($denormalizationData, 5, null);
            $context = [NormalizationMapper::NORMALIZATION_MAPPING => $normalizationMapping ?? []];
            $type = $type ?: get_class($denormalizedObject);
            if (!empty($extraContext)) {
                $context = array_merge($context, $extraContext);
            }

            self::assertEquals($denormalizedObject, $serializer->denormalize($dataToDenormalize, $type, null, $context), $useCase);
        }
    }

    public function getExpectedDenormalizedData(): iterable
    {
        $addCustomerGroupCommand = new AddCustomerGroupCommand(
            [
                self::EN_LANG_ID => 'english name',
                self::getFrenchId() => 'nom français',
            ],
            new DecimalNumber('12.87'),
            false,
            true,
            [1, 3],
        );

        yield 'denormalize command with DecimalNumber in the constructor, and denormalized localized value' => [
            [
                'localizedNames' => [
                    'en-US' => 'english name',
                    'fr-FR' => 'nom français',
                ],
                'reductionPercent' => 12.87,
                'displayPriceTaxExcluded' => false,
                'showPrice' => true,
                'shopIds' => [1, 3],
            ],
            $addCustomerGroupCommand,
            [],
            null,
            // Extra context to handle localized values
            [
                LocalizedValue::LOCALIZED_VALUE_PARAMETERS => [
                    'localizedNames' => [
                        LocalizedValue::DENORMALIZED_KEY => LocalizedValue::ID_KEY,
                    ],
                ],
            ],
        ];

        $editCustomerGroupCommand = new EditCustomerGroupCommand(42);
        $editCustomerGroupCommand->setReductionPercent(new DecimalNumber('12.87'));

        yield 'denormalize command with setter based on DecimalNumber' => [
            [
                'customerGroupId' => 42,
                'reductionPercent' => 12.87,
            ],
            $editCustomerGroupCommand,
        ];

        $updateProductCommand = new UpdateProductCommand(42, ShopConstraint::shop(1));
        $updateProductCommand->setWholesalePrice('12.34');
        $updateProductCommand->setWeight('2.67');
        $updateProductCommand->setRedirectOption(
            RedirectType::TYPE_CATEGORY_PERMANENT,
            1
        );

        yield 'denormalize command with shop constraint, decimal number and multi param setter' => [
            [
                'productId' => 42,
                'wholeSalePrice' => 12.34,
                'weight' => 2.67,
                'redirectType' => RedirectType::TYPE_CATEGORY_PERMANENT,
                'redirectTargetId' => 1,
            ],
            $updateProductCommand,
            [
                '[_context][shopConstraint]' => '[shopConstraint]',
                '[redirectType]' => '[redirectOption][redirectType]',
                '[redirectTargetId]' => '[redirectOption][redirectTarget]',
            ],
        ];

        $localizedResource = new LocalizedResource([
            'en-US' => 'english link',
            'fr-FR' => 'lien français',
        ]);

        // This property has no context attributes, so it remains indexed by IDs
        $localizedResource->names = [
            self::EN_LANG_ID => 'english name',
            self::getFrenchId() => 'nom français',
        ];
        $localizedResource->descriptions = [
            'en-US' => 'english description',
            'fr-FR' => 'description française',
        ];
        $localizedResource->titles = [
            'en-US' => 'english title',
            'fr-FR' => 'titre français',
        ];

        yield 'api resource with localized properties should have indexes based on locale values instead of integers' => [
            [
                'localizedLinks' => [
                    self::EN_LANG_ID => 'english link',
                    self::getFrenchId() => 'lien français',
                ],
                'names' => [
                    self::EN_LANG_ID => 'english name',
                    self::getFrenchId() => 'nom français',
                ],
                'descriptions' => [
                    self::EN_LANG_ID => 'english description',
                    self::getFrenchId() => 'description française',
                ],
                'titles' => [
                    self::EN_LANG_ID => 'english title',
                    self::getFrenchId() => 'titre français',
                ],
            ],
            $localizedResource,
        ];

        yield 'command with various property types all in constructor' => [
            [
                'localizedNames' => [
                    self::EN_LANG_ID => 'test en',
                    self::getFrenchId() => 'test fr',
                ],
                'reductionPercent' => 10.3,
                'displayPriceTaxExcluded' => true,
                'showPrice' => true,
                'shopIds' => [1],
            ],
            new AddCustomerGroupCommand(
                [
                    self::EN_LANG_ID => 'test en',
                    self::getFrenchId() => 'test fr',
                ],
                new DecimalNumber('10.3'),
                true,
                true,
                [1]
            ),
        ];

        $editCartRuleCommand = new EditCartRuleCommand(1);
        $editCartRuleCommand->setDescription('test description');
        $editCartRuleCommand->setCode('test code');
        $editCartRuleCommand->setMinimumAmount('10', 1, true, true);
        $editCartRuleCommand->setCustomerId(1);
        $editCartRuleCommand->setLocalizedNames([self::EN_LANG_ID => 'test en', self::getFrenchId() => 'test fr']);
        $editCartRuleCommand->setHighlightInCart(true);
        $editCartRuleCommand->setAllowPartialUse(true);
        $editCartRuleCommand->setPriority(1);
        $editCartRuleCommand->setActive(true);
        $editCartRuleCommand->setValidityDateRange(new DateTimeImmutable('2023-08-23'), new DateTimeImmutable('2023-08-25'));
        $editCartRuleCommand->setTotalQuantity(100);
        $editCartRuleCommand->setQuantityPerUser(1);
        $editCartRuleCommand->setCartRuleAction(new CartRuleAction(true));
        yield 'object with complex setter methods based on multiple properties or sub types' => [
            [
                'cartRuleId' => 1,
                'description' => 'test description',
                'code' => 'test code',
                'minimumAmount' => ['minimumAmount' => '10', 'currencyId' => 1, 'taxIncluded' => true, 'shippingIncluded' => true],
                'customerId' => 1,
                'localizedNames' => [
                    self::EN_LANG_ID => 'test en',
                    self::getFrenchId() => 'test fr',
                ],
                'highlightInCart' => true,
                'allowPartialUse' => true,
                'priority' => 1,
                'active' => true,
                'validityDateRange' => ['validFrom' => '2023-08-23', 'validTo' => '2023-08-25'],
                'totalQuantity' => 100,
                'quantityPerUser' => 1,
                'cartRuleAction' => ['freeShipping' => true],
                // TODO: handle cartRuleAction with complex discount handle by business rules
                // 'cartRuleAction' => ['freeShipping' => true, 'giftProduct' => ['productId': 1], 'discount' => ['amountDiscount' => ['amount' => 10]]]...
            ],
            $editCartRuleCommand,
        ];

        $customerGroupQuery = new GetCustomerGroupForEditing(51);
        yield 'value object with wrong parameter name converted via mapping' => [
            [
                'groupId' => 51,
            ],
            $customerGroupQuery,
            [
                '[groupId]' => '[customerGroupId]',
            ],
        ];

        $customerGroupQuery = new GetCustomerGroupForEditing(51);
        yield 'value object with proper parameter, extra mapping for normalization should ignore absent data and not override it with null' => [
            [
                'customerGroupId' => 51,
            ],
            $customerGroupQuery,
            [
                '[id]' => '[customerGroupId]',
                '[reduction]' => '[reductionPercent]',
            ],
        ];

        $customerGroupQuery = new GetCustomerGroupForEditing(51);
        yield 'value object with wrong parameter plus extra mapping for normalization' => [
            [
                'groupId' => 51,
            ],
            $customerGroupQuery,
            [
                '[groupId]' => '[customerGroupId]',
                '[id]' => '[customerGroupId]',
                '[reduction]' => '[reductionPercent]',
            ],
        ];

        yield 'single shop constraint' => [
            [
                'shopId' => 42,
            ],
            ShopConstraint::shop(42),
        ];

        yield 'shop group constraint' => [
            [
                'shopGroupId' => 42,
            ],
            ShopConstraint::shopGroup(42),
        ];

        yield 'all shop constraint' => [
            [],
            ShopConstraint::allShops(),
        ];

        yield 'strict shop constraint' => [
            [
                'shopGroupId' => null,
                'shopId' => 51,
                'isStrict' => true,
            ],
            ShopConstraint::shop(51, true),
        ];

        yield 'shop collections' => [
            [
                'shopGroupId' => null,
                'shopId' => null,
                'shopIds' => [3, 4],
                'isStrict' => false,
            ],
            ShopCollection::shops([3, 4]),
        ];

        yield 'shop collections list only specified' => [
            [
                'shopIds' => [3, 4],
            ],
            ShopCollection::shops([3, 4]),
        ];

        yield 'add product command' => [
            [
                'productType' => ProductType::TYPE_STANDARD,
                'shopId' => 51,
            ],
            new AddProductCommand(ProductType::TYPE_STANDARD, 51),
        ];

        yield 'get product query' => [
            [
                'productId' => 42,
                'shopConstraint' => [
                    'shopId' => 2,
                ],
                'displayLanguageId' => 51,
            ],
            new GetProductForEditing(42, ShopConstraint::shop(2), 51),
        ];

        $hook = new Hook();
        $hook->hookId = 1;
        $hook->active = true;
        $hook->name = 'testHook';
        $hook->title = 'testHookTitle';
        $hook->description = '';
        yield 'denormalize an APIPlatform DTO with specified mapping' => [
            [
                'id_hook' => 1,
                'active' => true,
                'name' => 'testHook',
                'title' => 'testHookTitle',
                'description' => '',
            ],
            $hook,
            ['[id_hook]' => '[hookId]'],
        ];

        $command = new AddProductCommand(
            ProductType::TYPE_STANDARD,
            // Shop ID is passed via context
            1,
            // Localized values are indexed by ID
            [
                self::EN_LANG_ID => 'english name',
                self::getFrenchId() => 'nom français',
            ]
        );
        $operation = new HttpOperation(extraProperties: [
            'CQRSCommandMapping' => [
                '[_context][shopId]' => '[shopId]',
                '[type]' => '[productType]',
                '[names]' => '[localizedNames]',
            ]]
        );
        yield 'denormalize input with API resource type initially but input set to CQRS command, mapping in operation' => [
            [
                'names' => [
                    'en-US' => 'english name',
                    'fr-FR' => 'nom français',
                ],
                'type' => 'standard',
            ],
            $command,
            [],
            Product::class,
            [
                'input' => [
                    'class' => AddProductCommand::class,
                ],
                'operation' => $operation,
            ],
        ];

        $apiClientListItem = new ApiClientList();
        $apiClientListItem->apiClientId = 42;
        $apiClientListItem->clientId = 'clientId';
        $apiClientListItem->clientName = 'clientName';
        $apiClientListItem->description = 'description';
        $apiClientListItem->externalIssuer = null;
        $apiClientListItem->enabled = true;
        $apiClientListItem->lifetime = 3600;
        yield 'list item with tiny int that must be converted into boolean value' => [
            [
                'apiClientId' => 42,
                'clientId' => 'clientId',
                'clientName' => 'clientName',
                'description' => 'description',
                'externalIssuer' => null,
                'enabled' => 1,
                'lifetime' => 3600,
            ],
            $apiClientListItem,
            [],
            null,
            [
                // Required context option to enable the boolean casting
                CQRSApiSerializer::CAST_BOOL => true,
            ],
        ];

        $uploadModuleCommand = new UploadModuleCommand(__DIR__ . '/../../../Resources/assets/new_logo.jpg');
        yield 'command with uploaded file are injected thanks to command mapping' => [
            [
                'archive' => new File(__DIR__ . '/../../../Resources/assets/new_logo.jpg'),
            ],
            $uploadModuleCommand,
            [
                '[archive].pathName' => '[source]',
            ],
        ];
    }

    public function testNormalize(): void
    {
        $serializer = self::getContainer()->get(CQRSApiSerializer::class);
        foreach ($this->getNormalizationData() as $useCase => $normalizationData) {
            list($dataToNormalize, $expectedNormalizedData, $normalizationMapping, $extraContext) = array_pad($normalizationData, 4, null);
            $context = [NormalizationMapper::NORMALIZATION_MAPPING => ($normalizationMapping ?? [])] + ($extraContext ?? []);

            self::assertEquals($expectedNormalizedData, $serializer->normalize($dataToNormalize, null, $context), $useCase);
        }
    }

    public static function getNormalizationData(): iterable
    {
        $virtualProduct = new VirtualProductFileForEditing(
            42,
            'virtual file',
            'pretty virtual file',
            23,
            1,
            DateTimeImmutable::createFromFormat(DateTimeUtil::DEFAULT_DATETIME_FORMAT, '1969-07-11 00:00:00'),
        );
        yield 'object with datetime' => [
            $virtualProduct,
            [
                'id' => 42,
                'fileName' => 'virtual file',
                'displayName' => 'pretty virtual file',
                'accessDays' => 23,
                'downloadTimesLimit' => 1,
                'expirationDate' => '1969-07-11 00:00:00',
            ],
        ];

        $createdApiClient = new CreatedApiClient(42, 'my_secret');
        yield 'test' => [
            $createdApiClient,
            [
                'apiClientId' => 42,
                'secret' => 'my_secret',
            ],
        ];

        $localizedResource = new LocalizedResource([
            'fr-FR' => 'http://mylink.fr',
            'en-US' => 'http://mylink.com',
        ]);
        $localizedResource->names = [
            self::getFrenchId() => 'Nom français',
            self::EN_LANG_ID => 'English name',
        ];
        $localizedResource->descriptions = [
            self::getFrenchId() => 'Description française',
            'en-US' => 'French description',
        ];
        $localizedResource->titles = [
            'fr-FR' => 'Titre français',
            self::EN_LANG_ID => 'French title',
        ];

        yield 'normalize localized resource uses locale keys' => [
            $localizedResource,
            [
                'names' => [
                    self::getFrenchId() => 'Nom français',
                    self::EN_LANG_ID => 'English name',
                ],
                'descriptions' => [
                    'fr-FR' => 'Description française',
                    'en-US' => 'French description',
                ],
                'titles' => [
                    'fr-FR' => 'Titre français',
                    'en-US' => 'French title',
                ],
                'localizedLinks' => [
                    'fr-FR' => 'http://mylink.fr',
                    'en-US' => 'http://mylink.com',
                ],
            ],
        ];

        $createdApiClient = new CreatedApiClient(42, 'my_secret');
        yield 'normalize command result that contains a ValueObject, returned as an integer not an array' => [
            $createdApiClient,
            [
                'apiClientId' => 42,
                'secret' => 'my_secret',
            ],
        ];

        $groupId = new GroupId(42);
        yield 'normalize GroupId value object returned as array' => [
            $groupId,
            [
                'groupId' => 42,
            ],
        ];

        $productId = new ProductId(42);
        yield 'normalize ProductId value object returned as array' => [
            $productId,
            [
                'productId' => 42,
            ],
        ];

        $editableCustomerGroup = new EditableCustomerGroup(
            42,
            [
                self::EN_LANG_ID => 'Group',
                self::$frenchLangId => 'Groupe',
            ],
            new DecimalNumber('10.67'),
            false,
            true,
            [
                1,
            ],
        );
        yield 'normalize object with displayPriceTaxExcluded that is a getter not starting by get' => [
            $editableCustomerGroup,
            [
                'id' => 42,
                'localizedNames' => [
                    self::EN_LANG_ID => 'Group',
                    self::$frenchLangId => 'Groupe',
                ],
                'reduction' => 10.67,
                'displayPriceTaxExcluded' => false,
                'showPrice' => true,
                'shopIds' => [
                    1,
                ],
            ],
        ];

        yield 'normalize object with displayPriceTaxExcluded that is a getter not starting by get and with extra mapping' => [
            $editableCustomerGroup,
            [
                'id' => 42,
                'localizedNames' => [
                    'en-US' => 'Group',
                    'fr-FR' => 'Groupe',
                ],
                'reduction' => 10.67,
                'reductionPercent' => 10.67,
                'displayPriceTaxExcluded' => false,
                'showPrice' => true,
                'shopIds' => [
                    1,
                ],
            ],
            [
                '[reduction]' => '[reductionPercent]',
            ],
            // Extra context to handle localized values
            [
                LocalizedValue::LOCALIZED_VALUE_PARAMETERS => [
                    'localizedNames' => [
                        LocalizedValue::NORMALIZED_KEY => LocalizedValue::LOCALE_KEY,
                    ],
                ],
            ],
        ];

        yield 'normalize single shop constraint' => [
            ShopConstraint::shop(42),
            [
                'shopId' => 42,
                'shopGroupId' => null,
                'shopIds' => null,
                'isStrict' => false,
            ],
        ];

        yield 'normalize group shop constraint' => [
            ShopConstraint::shopGroup(42),
            [
                'shopId' => null,
                'shopGroupId' => 42,
                'shopIds' => null,
                'isStrict' => false,
            ],
        ];

        yield 'normalize all shop constraint' => [
            ShopConstraint::allShops(),
            [
                'shopId' => null,
                'shopGroupId' => null,
                'shopIds' => null,
                'isStrict' => false,
            ],
        ];

        yield 'normalize all shop constraint strict' => [
            ShopConstraint::allShops(true),
            [
                'shopId' => null,
                'shopGroupId' => null,
                'shopIds' => null,
                'isStrict' => true,
            ],
        ];

        yield 'normalize collection shops' => [
            ShopCollection::shops([3, 4]),
            [
                'shopId' => null,
                'shopGroupId' => null,
                'shopIds' => [3, 4],
                'isStrict' => false,
            ],
        ];
    }
}
