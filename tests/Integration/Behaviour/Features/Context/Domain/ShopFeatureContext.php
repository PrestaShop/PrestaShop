<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Shop;

function move_uploaded_file($from, $to)
{
    return true;
}

function unlink($filename, $context = null)
{
    return true;
}

function tempnam($directory, $prefix)
{
    global $shopFeatureLogoPath;

    return $shopFeatureLogoPath;
}

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Behat\Gherkin\Node\TableNode;
use Configuration;
use Customer;
use Db;
use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShop\PrestaShop\Core\Context\ShopContextBuilder;
use PrestaShop\PrestaShop\Core\Domain\Shop\Command\AddShopCommand;
use PrestaShop\PrestaShop\Core\Domain\Shop\Command\DeleteShopCommand;
use PrestaShop\PrestaShop\Core\Domain\Shop\Command\EditShopCommand;
use PrestaShop\PrestaShop\Core\Domain\Shop\Command\UploadLogosCommand;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\CannotDeleteShopException;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\CannotUpdateShopException;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\ShopAssociationNotFound;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\ShopConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\ShopException;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\ShopNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Shop\Query\GetShopForEditing;
use PrestaShop\PrestaShop\Core\Domain\Shop\QueryResult\EditableShop;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;
use RuntimeException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Tests\Integration\Behaviour\Features\Context\CommonFeatureContext;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;

class ShopFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * Random integer that represents shop id which should never exist in test database
     */
    private const NON_EXISTING_SHOP_ID = 77701;

    /**
     * @When I upload :path as new Header Logo
     */
    public function uploadHeaderLogo(string $path): void
    {
        global $shopFeatureLogoPath;
        $shopFeatureLogoPath = __DIR__ . '/../../../../../../' . $path;
        $uploadCommand = new UploadLogosCommand();
        $uploadCommand->setUploadedHeaderLogo(new UploadedFile($shopFeatureLogoPath, 'logo.jpg', null, null, true));
        $this->getCommandBus()->handle($uploadCommand);
    }

    /**
     * @Then the logo size configuration should be :width x :height
     *
     * @param int $width
     * @param int $height
     */
    public function logoSizeConfigurationShouldbe(int $width, int $height): void
    {
        $confWidth = (int) Configuration::get('SHOP_LOGO_WIDTH');
        $confHeight = (int) Configuration::get('SHOP_LOGO_HEIGHT');

        if ($confWidth !== $width) {
            throw new RuntimeException('Width does not match');
        }
        if ($confHeight !== $height) {
            throw new RuntimeException('Height does not match');
        }
    }

    /**
     * @Given shop :reference does not exist
     *
     * @param string $reference
     */
    public function setNonExistingShopReference(string $reference): void
    {
        if ($this->getSharedStorage()->exists($reference) && $this->getSharedStorage()->get($reference)) {
            throw new RuntimeException(sprintf('Expected that shop "%s" should not exist', $reference));
        }

        $this->getSharedStorage()->set($reference, self::NON_EXISTING_SHOP_ID);
    }

    /**
     * @Then I should get error that shop was not found
     */
    public function assertShopNotFound(): void
    {
        $this->assertLastErrorIs(ShopNotFoundException::class);
    }

    /**
     * @Then I should get error that shop association was not found
     */
    public function assertLastErrorIsShopAssociationNotFound(): void
    {
        $this->assertLastErrorIs(ShopAssociationNotFound::class);
    }

    /**
     * @When I add a shop :reference with the following properties:
     */
    public function addShop(string $reference, TableNode $table): void
    {
        $data = $table->getRowsHash();

        try {
            /** @var ShopId $shopId */
            $shopId = $this->getCommandBus()->handle(new AddShopCommand(
                $data['name'],
                $data['color'] ?? '',
                $this->referenceToId($data['shop group']),
                $this->referenceToId($data['root category']),
                $this->referencesToIds($data['categories']),
                $data['theme'] ?? 'classic',
                isset($data['source shop']) ? $this->referenceToId($data['source shop']) : null,
                PrimitiveUtils::castStringArrayIntoArray($data['imported data'] ?? ''),
            ));
            $this->getSharedStorage()->set($reference, $shopId->getValue());
        } catch (ShopException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I edit shop :reference with the following properties:
     */
    public function editShop(string $reference, TableNode $table): void
    {
        $data = $table->getRowsHash();
        $command = new EditShopCommand($this->referenceToId($reference));

        if (isset($data['name'])) {
            $command->setName($data['name']);
        }
        if (isset($data['color'])) {
            $command->setColor($data['color']);
        }
        if (isset($data['shop group'])) {
            $command->setShopGroupId($this->referenceToId($data['shop group']));
        }
        if (isset($data['root category'])) {
            $command->setRootCategoryId($this->referenceToId($data['root category']));
        }
        if (isset($data['categories'])) {
            $command->setCategoryIds($this->referencesToIds($data['categories']));
        }
        if (isset($data['theme'])) {
            $command->setThemeName($data['theme']);
        }

        try {
            $this->getCommandBus()->handle($command);
        } catch (ShopException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I delete shop :reference
     */
    public function deleteShop(string $reference): void
    {
        try {
            $this->getCommandBus()->handle(new DeleteShopCommand($this->referenceToId($reference)));
        } catch (ShopException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @Given shop :reference has a customer
     */
    public function addCustomerToShop(string $reference): void
    {
        $shopId = $this->referenceToId($reference);
        $customer = new Customer();
        $customer->firstname = 'John';
        $customer->lastname = 'Doe';
        $customer->email = sprintf('john.doe.shop%d@prestashop.com', $shopId);
        $customer->passwd = password_hash('prestashop', PASSWORD_BCRYPT);
        $customer->id_shop = $shopId;
        $customer->id_shop_group = $this->getEditableShop($reference)->getShopGroupId();

        if (!$customer->add()) {
            throw new RuntimeException(sprintf('Could not add a customer to shop "%s"', $reference));
        }
    }

    /**
     * @Then shop :reference should have the following properties:
     */
    public function assertShopProperties(string $reference, TableNode $table): void
    {
        $data = $table->getRowsHash();
        $shop = $this->getEditableShop($reference);

        if (isset($data['name'])) {
            Assert::assertSame($data['name'], $shop->getName());
        }
        if (isset($data['color'])) {
            Assert::assertSame($data['color'], $shop->getColor());
        }
        if (isset($data['shop group'])) {
            Assert::assertSame($this->referenceToId($data['shop group']), $shop->getShopGroupId());
        }
        if (isset($data['root category'])) {
            Assert::assertSame($this->referenceToId($data['root category']), $shop->getRootCategoryId());
            Assert::assertSame(
                $this->referenceToId($data['root category']),
                (int) Configuration::get('PS_HOME_CATEGORY', null, null, $shop->getShopId())
            );
        }
        if (isset($data['categories'])) {
            $expectedCategoryIds = $this->referencesToIds($data['categories']);
            $expectedCategoryIds[] = (int) Configuration::get('PS_ROOT_CATEGORY');
            sort($expectedCategoryIds);
            Assert::assertSame($expectedCategoryIds, $shop->getCategoryIds());
        }
        if (isset($data['theme'])) {
            Assert::assertSame($data['theme'], $shop->getThemeName());
        }
        if (isset($data['shop group locked'])) {
            Assert::assertSame(PrimitiveUtils::castStringBooleanIntoBoolean($data['shop group locked']), $shop->isShopGroupLocked());
        }
    }

    /**
     * @Then shop :reference should not exist
     */
    public function assertShopDoesNotExist(string $reference): void
    {
        try {
            $this->getEditableShop($reference);
        } catch (ShopNotFoundException) {
            return;
        }

        throw new RuntimeException(sprintf('Shop "%s" still exists', $reference));
    }

    /**
     * @Then shop :reference should have the same :association associations as shop :sourceReference
     */
    public function assertSameAssociations(string $reference, string $association, string $sourceReference): void
    {
        $sourceIds = $this->getAssociatedIds($sourceReference, $association);

        Assert::assertNotEmpty($sourceIds, sprintf('Shop "%s" has no %s associations to compare with', $sourceReference, $association));
        Assert::assertSame($sourceIds, $this->getAssociatedIds($reference, $association));
    }

    /**
     * @Then shop :reference should have no :association associations
     */
    public function assertNoAssociations(string $reference, string $association): void
    {
        Assert::assertSame([], $this->getAssociatedIds($reference, $association));
    }

    /**
     * @Then the products of shop :reference should have :categoryReference as default category
     */
    public function assertProductsDefaultCategory(string $reference, string $categoryReference): void
    {
        $defaultCategoryIds = Db::getInstance()->executeS(sprintf(
            'SELECT DISTINCT `id_category_default` FROM `%sproduct_shop` WHERE `id_shop` = %d',
            _DB_PREFIX_,
            $this->referenceToId($reference)
        ));

        Assert::assertSame([$this->referenceToId($categoryReference)], array_map('intval', array_column($defaultCategoryIds, 'id_category_default')));
    }

    /**
     * @Then shop :reference should be associated to the default customer groups
     */
    public function assertDefaultCustomerGroups(string $reference): void
    {
        $expectedGroupIds = array_map(
            static fn (string $key): int => (int) Configuration::get($key),
            ['PS_UNIDENTIFIED_GROUP', 'PS_GUEST_GROUP', 'PS_CUSTOMER_GROUP']
        );
        sort($expectedGroupIds);

        Assert::assertSame($expectedGroupIds, $this->getAssociatedIds($reference, 'group'));
    }

    /**
     * @Then I should get error that shop name is invalid
     */
    public function assertLastErrorIsInvalidName(): void
    {
        $this->assertLastErrorIs(ShopConstraintException::class, ShopConstraintException::INVALID_NAME);
    }

    /**
     * @Then I should get error that shop color is invalid
     */
    public function assertLastErrorIsInvalidColor(): void
    {
        $this->assertLastErrorIs(ShopConstraintException::class, ShopConstraintException::INVALID_COLOR);
    }

    /**
     * @Then I should get error that shop theme is invalid
     */
    public function assertLastErrorIsInvalidTheme(): void
    {
        $this->assertLastErrorIs(ShopConstraintException::class, ShopConstraintException::INVALID_THEME);
    }

    /**
     * @Then I should get error that shop name is already used in the group
     */
    public function assertLastErrorIsNameAlreadyUsed(): void
    {
        $this->assertLastErrorIs(ShopConstraintException::class, ShopConstraintException::NAME_ALREADY_USED_IN_GROUP);
    }

    /**
     * @Then I should get error that shop root category is invalid
     */
    public function assertLastErrorIsInvalidRootCategory(): void
    {
        $this->assertLastErrorIs(ShopConstraintException::class, ShopConstraintException::INVALID_ROOT_CATEGORY);
    }

    /**
     * @Then I should get error that shop root category must be associated
     */
    public function assertLastErrorIsRootCategoryNotAssociated(): void
    {
        $this->assertLastErrorIs(ShopConstraintException::class, ShopConstraintException::ROOT_CATEGORY_NOT_ASSOCIATED);
    }

    /**
     * @Then I should get error that shop group of the shop cannot be changed
     */
    public function assertLastErrorIsShopGroupLocked(): void
    {
        $this->assertLastErrorIs(CannotUpdateShopException::class, CannotUpdateShopException::SHOP_GROUP_LOCKED);
    }

    /**
     * @Then I should get error that shop cannot be moved to this shop group
     */
    public function assertLastErrorIsInvalidTargetShopGroup(): void
    {
        $this->assertLastErrorIs(CannotUpdateShopException::class, CannotUpdateShopException::INVALID_TARGET_SHOP_GROUP);
    }

    /**
     * @Then I should get error that default shop cannot be deleted
     */
    public function assertLastErrorIsDefaultShop(): void
    {
        $this->assertLastErrorIs(CannotDeleteShopException::class, CannotDeleteShopException::DEFAULT_SHOP);
    }

    /**
     * @Then I should get error that shop with customers or orders cannot be deleted
     */
    public function assertLastErrorIsShopWithDependencies(): void
    {
        $this->assertLastErrorIs(CannotDeleteShopException::class, CannotDeleteShopException::HAS_DEPENDENCIES);
    }

    private function getEditableShop(string $reference): EditableShop
    {
        /** @var EditableShop $shop */
        $shop = $this->getQueryBus()->handle(new GetShopForEditing($this->referenceToId($reference)));

        return $shop;
    }

    /**
     * @return int[]
     */
    private function getAssociatedIds(string $shopReference, string $association): array
    {
        $associatedIds = array_map('intval', array_column(Db::getInstance()->executeS(sprintf(
            'SELECT `id_%1$s` FROM `%2$s%1$s_shop` WHERE `id_shop` = %3$d',
            bqSQL($association),
            _DB_PREFIX_,
            $this->referenceToId($shopReference)
        )), 'id_' . $association));
        sort($associatedIds);

        return $associatedIds;
    }

    /**
     * @Given I set up shop context to single shop :shopReference
     */
    public function setupShopContext(string $shopReference)
    {
        // We only need to update the builder settings, ShopContext service was defined as NOT shared
        // so each time it is accessed a new instance is created and the builder is called again
        /** @var ShopContextBuilder $shopContextBuilder */
        $shopContextBuilder = CommonFeatureContext::getContainer()->get('test_shop_context_builder');
        $shopContextBuilder->setShopConstraint(ShopConstraint::shop($this->referenceToId($shopReference)));
        $shopContextBuilder->setShopId($this->referenceToId($shopReference));
    }
}
