<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Behat\Gherkin\Node\TableNode;
use Exception;
use PrestaShop\PrestaShop\Core\Domain\Tag\Command\AddTagCommand;
use PrestaShop\PrestaShop\Core\Domain\Tag\Command\BulkDeleteTagCommand;
use PrestaShop\PrestaShop\Core\Domain\Tag\Command\DeleteTagCommand;
use PrestaShop\PrestaShop\Core\Domain\Tag\Command\EditTagCommand;
use PrestaShop\PrestaShop\Core\Domain\Tag\Exception\DuplicateTagException;
use PrestaShop\PrestaShop\Core\Domain\Tag\Exception\TagNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Tag\Query\GetTagForEditing;
use PrestaShop\PrestaShop\Core\Domain\Tag\QueryResult\EditableTag;
use PrestaShop\PrestaShop\Core\Domain\Tag\ValueObject\TagId;
use RuntimeException;
use Tag;
use Tests\Integration\Behaviour\Features\Context\SharedStorage;
use Tests\Integration\Behaviour\Features\Context\Util\NoExceptionAlthoughExpectedException;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;

class TagFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @Given I add a tag :tagReference with specified properties:
     */
    public function addNewTag(string $tagReference, TableNode $node): void
    {
        $data = $node->getRowsHash();

        $language = SharedStorage::getStorage()->get($data['language']);

        $productIds = [];
        foreach (explode(',', $data['products']) as $dataProduct) {
            $productId = SharedStorage::getStorage()->get($dataProduct);
            $productIds[] = $productId;
        }

        $command = new AddTagCommand(
            $data['name'],
            (int) $language->id,
            $productIds
        );

        try {
            /** @var TagId $tagId */
            $tagId = $this->getCommandBus()->handle($command);

            SharedStorage::getStorage()->set($tagReference, $tagId->getValue());
        } catch (Exception $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I edit tag :reference with specified properties:
     */
    public function editTagFromSpecifiedProperties(string $reference, TableNode $node): void
    {
        $command = new EditTagCommand((int) SharedStorage::getStorage()->get($reference));

        $data = $node->getRowsHash();
        if (isset($data['name'])) {
            $command->setName($data['name']);
        }
        if (isset($data['language'])) {
            $language = SharedStorage::getStorage()->get($data['language']);

            $command->setLanguageId((int) $language->id);
        }
        if (isset($data['products'])) {
            $productIds = [];
            foreach (explode(',', $data['products']) as $dataProduct) {
                $productIds[] = (int) SharedStorage::getStorage()->get($dataProduct);
            }
            $command->setProductIds($productIds);
        }

        try {
            $this->getCommandBus()->handle($command);
        } catch (Exception $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @Then I should get error that tag is duplicate
     */
    public function assertLastErrorIsDuplicateTag(): void
    {
        $this->assertLastErrorIs(DuplicateTagException::class);
    }

    /**
     * @Then tag :reference name should be :value
     */
    public function assertTagNameMatches(string $reference, string $value): void
    {
        $editableTag = $this->getTagFromReference($reference);

        if ($editableTag->getName() !== $value) {
            throw new RuntimeException(sprintf('Tag name "%s" does not match "%s" name.', $editableTag->getName(), $value));
        }
    }

    /**
     * @Then tag :reference language should be :value
     */
    public function assertTagLanguageMatches(string $reference, string $value): void
    {
        $editableTag = $this->getTagFromReference($reference);

        $language = SharedStorage::getStorage()->get($value);

        if ($editableTag->getLanguageId() !== (int) $language->id) {
            throw new RuntimeException(sprintf('Tag language "%s" does not match "%s" language.', $editableTag->getLanguageId(), $language->id));
        }
    }

    /**
     * @Then tag :reference products should be :value
     */
    public function assertTagProductsMatches(string $reference, string $value): void
    {
        $editableTag = $this->getTagFromReference($reference);
        $valueProductsIds = [];
        foreach ($editableTag->getProducts() as $product) {
            $valueProductsIds[] = $product['id'];
        }
        sort($valueProductsIds);

        $expectedProductsId = [];
        foreach (explode(',', $value) as $expectedProductId) {
            $expectedProductsId[] = SharedStorage::getStorage()->get($expectedProductId);
        }
        sort($expectedProductsId);

        if ($valueProductsIds !== $expectedProductsId) {
            throw new RuntimeException(sprintf(
                'Tag products "%s" does not match "%s" products.',
                implode(',', $valueProductsIds),
                implode(',', $expectedProductsId)
            ));
        }
    }

    /**
     * @When I delete the tag :tagReference
     *
     * @param string $tagReference
     */
    public function deleteTag(string $tagReference): void
    {
        /** @var int $tagId */
        $tagId = SharedStorage::getStorage()->get($tagReference);

        try {
            $this->getCommandBus()->handle(new DeleteTagCommand((int) $tagId));
        } catch (TagNotFoundException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I delete tags :tagReferences using bulk action
     *
     * @param string $tagReferences
     */
    public function bulkDeleteTags(string $tagReferences): void
    {
        $tagIds = [];
        foreach (PrimitiveUtils::castStringArrayIntoArray($tagReferences) as $tagReference) {
            $tagIds[] = (int) SharedStorage::getStorage()->get($tagReference);
        }

        try {
            $this->getCommandBus()->handle(new BulkDeleteTagCommand($tagIds));
        } catch (TagNotFoundException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @Then I should get an error that the tag has not been found
     */
    public function assertLastErrorTagNotFound(): void
    {
        $this->assertLastErrorIs(TagNotFoundException::class);
    }

    /**
     * @Then /^the tag "(.+)" should be deleted$/
     *
     * @param string $tagReference
     */
    public function assertTagIsDeleted(string $tagReference): void
    {
        if ($this->isFoundTag($tagReference)) {
            throw new NoExceptionAlthoughExpectedException(sprintf('Tag %s exist, but it was expected to be deleted', $tagReference));
        }
    }

    /**
     * @Then /^the tag "(.+)" should not be deleted$/
     *
     * @param string $tagReference
     */
    public function assertTagIsNotDeleted(string $tagReference): void
    {
        if (!$this->isFoundTag($tagReference)) {
            throw new NoExceptionAlthoughExpectedException(sprintf('Tag %s doesn\'t exist, but it was expected to be existing', $tagReference));
        }
    }

    /**
     * @Then tags :tagReferences should be deleted
     *
     * @param string $tagReferences
     */
    public function assertTagsAreDeleted(string $tagReferences): void
    {
        foreach (PrimitiveUtils::castStringArrayIntoArray($tagReferences) as $tagReference) {
            $this->assertTagIsDeleted($tagReference);
        }
    }

    protected function getTagFromReference(string $reference): EditableTag
    {
        $idTag = (int) SharedStorage::getStorage()->get($reference);

        /** @var EditableTag $editableTag */
        $editableTag = $this->getQueryBus()->handle(new GetTagForEditing($idTag));

        return $editableTag;
    }

    protected function isFoundTag(string $tagReference): bool
    {
        try {
            $this->getTagFromReference($tagReference);

            return true;
        } catch (TagNotFoundException $e) {
            return false;
        }
    }
}
