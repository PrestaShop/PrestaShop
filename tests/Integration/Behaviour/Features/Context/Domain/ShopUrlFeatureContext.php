<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Behat\Gherkin\Node\TableNode;
use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Core\Domain\Shop\Command\AddShopUrlCommand;
use PrestaShop\PrestaShop\Core\Domain\Shop\Command\DeleteShopUrlCommand;
use PrestaShop\PrestaShop\Core\Domain\Shop\Command\EditShopUrlCommand;
use PrestaShop\PrestaShop\Core\Domain\Shop\Command\ToggleShopUrlMainCommand;
use PrestaShop\PrestaShop\Core\Domain\Shop\Command\ToggleShopUrlStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\CannotDeleteShopUrlException;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\ShopException;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\ShopUrlConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\ShopUrlNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Shop\Query\GetShopUrlForEditing;
use PrestaShop\PrestaShop\Core\Domain\Shop\QueryResult\EditableShopUrl;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopUrlId;
use RuntimeException;
use ShopUrl;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;

class ShopUrlFeatureContext extends AbstractDomainFeatureContext
{
    private const NON_EXISTING_SHOP_URL_ID = 74099901;

    /**
     * @Given the main url of shop :shopReference is :reference
     */
    public function setMainShopUrlReference(string $shopReference, string $reference): void
    {
        /** @var ShopUrl|false $shopUrl */
        $shopUrl = ShopUrl::getShopUrls($this->referenceToId($shopReference))->where('main', '=', 1)->getFirst();
        if (false === $shopUrl) {
            throw new RuntimeException(sprintf('Shop "%s" has no main url', $shopReference));
        }

        $this->getSharedStorage()->set($reference, (int) $shopUrl->id);
    }

    /**
     * @Given shop url :reference does not exist
     */
    public function setNonExistingShopUrlReference(string $reference): void
    {
        $this->getSharedStorage()->set($reference, self::NON_EXISTING_SHOP_URL_ID);
    }

    /**
     * @When I add a shop url :reference with the following properties:
     */
    public function addShopUrl(string $reference, TableNode $table): void
    {
        $data = $table->getRowsHash();

        try {
            /** @var ShopUrlId $shopUrlId */
            $shopUrlId = $this->getCommandBus()->handle(new AddShopUrlCommand(
                $this->referenceToId($data['shop']),
                $data['domain'],
                $data['domain_ssl'] ?? $data['domain'],
                $data['physical_uri'] ?? '',
                $data['virtual_uri'] ?? '',
                PrimitiveUtils::castStringBooleanIntoBoolean($data['main'] ?? 'false'),
                PrimitiveUtils::castStringBooleanIntoBoolean($data['active'] ?? 'true'),
            ));
            $this->getSharedStorage()->set($reference, $shopUrlId->getValue());
        } catch (ShopException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I edit shop url :reference with the following properties:
     */
    public function editShopUrl(string $reference, TableNode $table): void
    {
        $data = $table->getRowsHash();
        $command = new EditShopUrlCommand($this->referenceToId($reference));

        if (isset($data['shop'])) {
            $command->setShopId($this->referenceToId($data['shop']));
        }
        if (isset($data['domain'])) {
            $command->setDomain($data['domain']);
        }
        if (isset($data['domain_ssl'])) {
            $command->setDomainSsl($data['domain_ssl']);
        }
        if (isset($data['physical_uri'])) {
            $command->setPhysicalUri($data['physical_uri']);
        }
        if (isset($data['virtual_uri'])) {
            $command->setVirtualUri($data['virtual_uri']);
        }
        if (isset($data['main'])) {
            $command->setMain(PrimitiveUtils::castStringBooleanIntoBoolean($data['main']));
        }
        if (isset($data['active'])) {
            $command->setActive(PrimitiveUtils::castStringBooleanIntoBoolean($data['active']));
        }

        try {
            $this->getCommandBus()->handle($command);
        } catch (ShopException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I toggle the status of shop url :reference
     */
    public function toggleShopUrlStatus(string $reference): void
    {
        try {
            $this->getCommandBus()->handle(new ToggleShopUrlStatusCommand($this->referenceToId($reference)));
        } catch (ShopException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I toggle the main flag of shop url :reference
     */
    public function toggleShopUrlMain(string $reference): void
    {
        try {
            $this->getCommandBus()->handle(new ToggleShopUrlMainCommand($this->referenceToId($reference)));
        } catch (ShopException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I delete shop url :reference
     */
    public function deleteShopUrl(string $reference): void
    {
        try {
            $this->getCommandBus()->handle(new DeleteShopUrlCommand($this->referenceToId($reference)));
        } catch (ShopException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @Then shop url :reference should have the following properties:
     */
    public function assertShopUrlProperties(string $reference, TableNode $table): void
    {
        $data = $table->getRowsHash();
        $shopUrl = $this->getShopUrl($reference);

        if (isset($data['shop'])) {
            Assert::assertSame($this->referenceToId($data['shop']), $shopUrl->getShopId());
        }
        if (isset($data['domain'])) {
            Assert::assertSame($data['domain'], $shopUrl->getDomain());
        }
        if (isset($data['domain_ssl'])) {
            Assert::assertSame($data['domain_ssl'], $shopUrl->getDomainSsl());
        }
        if (isset($data['physical_uri'])) {
            Assert::assertSame($data['physical_uri'], $shopUrl->getPhysicalUri());
        }
        if (isset($data['virtual_uri'])) {
            Assert::assertSame($data['virtual_uri'], $shopUrl->getVirtualUri());
        }
        if (isset($data['main'])) {
            Assert::assertSame(PrimitiveUtils::castStringBooleanIntoBoolean($data['main']), $shopUrl->isMain());
        }
        if (isset($data['active'])) {
            Assert::assertSame(PrimitiveUtils::castStringBooleanIntoBoolean($data['active']), $shopUrl->isActive());
        }
    }

    /**
     * @Then shop url :reference should not exist
     */
    public function assertShopUrlDoesNotExist(string $reference): void
    {
        try {
            $this->getShopUrl($reference);
        } catch (ShopUrlNotFoundException) {
            return;
        }

        throw new RuntimeException(sprintf('Shop url "%s" still exists', $reference));
    }

    /**
     * @Then I should get error that shop url was not found
     */
    public function assertLastErrorIsShopUrlNotFound(): void
    {
        $this->assertLastErrorIs(ShopUrlNotFoundException::class);
    }

    /**
     * @Then I should get error that shop url domain is invalid
     */
    public function assertLastErrorIsInvalidDomain(): void
    {
        $this->assertLastErrorIs(ShopUrlConstraintException::class, ShopUrlConstraintException::INVALID_DOMAIN);
    }

    /**
     * @Then I should get error that shop url virtual uri is invalid
     */
    public function assertLastErrorIsInvalidVirtualUri(): void
    {
        $this->assertLastErrorIs(ShopUrlConstraintException::class, ShopUrlConstraintException::INVALID_VIRTUAL_URI);
    }

    /**
     * @Then I should get error that shop url is already used
     */
    public function assertLastErrorIsUrlAlreadyUsed(): void
    {
        $this->assertLastErrorIs(ShopUrlConstraintException::class, ShopUrlConstraintException::URL_ALREADY_USED);
    }

    /**
     * @Then I should get error that main shop url must be active
     */
    public function assertLastErrorIsMainUrlMustBeActive(): void
    {
        $this->assertLastErrorIs(ShopUrlConstraintException::class, ShopUrlConstraintException::MAIN_URL_MUST_BE_ACTIVE);
    }

    /**
     * @Then I should get error that main shop url cannot be unset
     */
    public function assertLastErrorIsMainUrlCannotBeUnset(): void
    {
        $this->assertLastErrorIs(ShopUrlConstraintException::class, ShopUrlConstraintException::MAIN_URL_CANNOT_BE_UNSET);
    }

    /**
     * @Then I should get error that main shop url cannot be deleted
     */
    public function assertLastErrorIsMainUrlCannotBeDeleted(): void
    {
        $this->assertLastErrorIs(CannotDeleteShopUrlException::class, CannotDeleteShopUrlException::MAIN_URL);
    }

    private function getShopUrl(string $reference): EditableShopUrl
    {
        /** @var EditableShopUrl $shopUrl */
        $shopUrl = $this->getQueryBus()->handle(new GetShopUrlForEditing($this->referenceToId($reference)));

        return $shopUrl;
    }
}
