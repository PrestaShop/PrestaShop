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
        foreach ($command->getLocalizedTagsList() as $localizedTags) {
            $this->assertTagsAreSearchable($localizedTags);
        }

        $product = $this->productRepository->getProductByDefaultShop($command->getProductId());
        $this->productTagUpdater->setProductTags($product, $command->getLocalizedTagsList());
    }

    /**
     * Rejects tags that the search engine strips to nothing at indexation.
     * Kept here (not in the LocalizedTags value object) because the check delegates
     * to the indexer, which needs a booted shop - the value object stays pure.
     *
     * @throws ProductConstraintException
     */
    private function assertTagsAreSearchable(LocalizedTags $localizedTags): void
    {
        $idLang = $localizedTags->getLanguageId()->getValue();
        $isoCode = (string) Language::getIsoById($idLang);

        foreach ($localizedTags->getTags() as $tag) {
            if (!Validate::isSearchableName($tag, $idLang, $isoCode)) {
                throw new ProductConstraintException(
                    sprintf(
                        'Product tag "%s" in language with id "%s" cannot be found by the search engine',
                        $tag,
                        $idLang
                    ),
                    ProductConstraintException::INVALID_TAG
                );
            }
        }
    }
}
