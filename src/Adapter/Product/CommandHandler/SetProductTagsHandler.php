<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\CommandHandler;

use Language;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Adapter\Product\Update\ProductTagUpdater;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\SetProductTagsCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\CommandHandler\UpdateProductTagsHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\LocalizedTags;
use Tag;
use Validate;

/**
 * Handles UpdateProductTagsCommand using legacy object model
 */
#[AsCommandHandler]
final class SetProductTagsHandler implements UpdateProductTagsHandlerInterface
{
    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var ProductTagUpdater
     */
    private $productTagUpdater;

    /**
     * @param ProductRepository $productRepository
     * @param ProductTagUpdater $productTagUpdater
     */
    public function __construct(
        ProductRepository $productRepository,
        ProductTagUpdater $productTagUpdater
    ) {
        $this->productRepository = $productRepository;
        $this->productTagUpdater = $productTagUpdater;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(SetProductTagsCommand $command): void
    {
        // Tags already on the product are resubmitted untouched by the form: only new ones are checked,
        // so a product carrying a tag saved before this check still saves
        $storedTags = Tag::getProductTags($command->getProductId()->getValue()) ?: [];
        foreach ($command->getLocalizedTagsList() as $localizedTags) {
            $this->assertNewTagsAreSearchable($localizedTags, $storedTags[$localizedTags->getLanguageId()->getValue()] ?? []);
        }

        $product = $this->productRepository->getProductByDefaultShop($command->getProductId());
        $this->productTagUpdater->setProductTags($product, $command->getLocalizedTagsList());
    }

    /**
     * Rejects new tags that the search engine strips to nothing at indexation.
     * Kept here (not in the LocalizedTags value object) because the check delegates
     * to the indexer, which needs a booted shop - the value object stays pure.
     *
     * @param string[] $storedTags tags of the product in this language before the update
     *
     * @throws ProductConstraintException
     */
    private function assertNewTagsAreSearchable(LocalizedTags $localizedTags, array $storedTags): void
    {
        $idLang = $localizedTags->getLanguageId()->getValue();
        $isoCode = (string) Language::getIsoById($idLang);
        $storedTags = array_map(static fn (string $tag): string => mb_strtolower(trim($tag)), $storedTags);

        foreach ($localizedTags->getTags() as $tag) {
            if (in_array(mb_strtolower(trim($tag)), $storedTags, true)) {
                continue;
            }

            if (!Validate::isSearchableName($tag, $idLang, $isoCode)) {
                throw new ProductConstraintException(
                    sprintf(
                        'Product tag "%s" in language with id "%s" cannot be found by the search engine',
                        $tag,
                        $idLang
                    ),
                    ProductConstraintException::UNSEARCHABLE_TAG
                );
            }
        }
    }
}
