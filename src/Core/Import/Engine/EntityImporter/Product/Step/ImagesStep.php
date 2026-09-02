<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\Product\Step;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Command\AddProductImageCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Command\DeleteProductImageCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Command\UpdateProductImageCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Query\GetProductImages;
use PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\LocalizedValueTrait;
use PrestaShop\PrestaShop\Core\Import\Engine\Exception\FileDownloadException;
use PrestaShop\PrestaShop\Core\Import\Engine\FileDownloader;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportMessage;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportPhaseDefinition;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportRunContext;
use PrestaShop\PrestaShop\Core\Import\Engine\ValueParser;
use PrestaShop\PrestaShop\Core\Language\LanguageRepositoryInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Deletes the existing images when delete_existing_images requests it, then
 * fetches and attaches every image entry with its positionally-matched alt.
 */
class ImagesStep extends AbstractProductRowStep
{
    use LocalizedValueTrait;

    public function __construct(
        ValueParser $valueParser,
        protected readonly CommandBusInterface $commandBus,
        protected readonly FileDownloader $fileDownloader,
        protected readonly Filesystem $filesystem,
        protected readonly LanguageRepositoryInterface $languageRepository,
        protected readonly TranslatorInterface $translator,
    ) {
        parent::__construct($valueParser);
    }

    public function supports(array $row): bool
    {
        // the delete branch runs even when the image cell is empty, so the
        // delete flag alone is enough to make the step relevant
        return true === $this->valueParser->parseBoolean($row['delete_existing_images'] ?? '')
            || $this->hasValue($row, 'image');
    }

    public function apply(array $row, int $rowIndex, int $productId, bool $isCreation, int $languageId, ImportRunContext $context): array
    {
        $messages = [];

        // parseBoolean('') is false, so an unmapped or empty cell falls through
        // without needing a hasValue() guard (same shape as isVirtual())
        if (true === $this->valueParser->parseBoolean($row['delete_existing_images'] ?? '')) {
            /** @var array<int, \PrestaShop\PrestaShop\Core\Domain\Product\Image\QueryResult\ProductImage> $existingImages */
            $existingImages = $this->commandBus->handle(new GetProductImages($productId, $context->getShopConstraint()));
            foreach ($existingImages as $existingImage) {
                $this->commandBus->handle(new DeleteProductImageCommand($existingImage->getImageId()));
            }
        }

        $imagesCell = $row['image'] ?? '';
        if ('' === $imagesCell) {
            return $messages;
        }

        // BOTH cells are split positionally (alt N belongs to image N): empty
        // entries must be kept on either side, otherwise a hole shifts the
        // following alts onto the wrong image
        $imageUrls = $this->valueParser->splitPreservingEmpty($imagesCell, $context->getMultipleValueSeparator());
        $imageAlts = $this->valueParser->splitPreservingEmpty($row['image_alt'] ?? '', $context->getMultipleValueSeparator());

        foreach ($imageUrls as $index => $imageUrl) {
            if ('' === $imageUrl) {
                // a hole in the image cell: nothing to fetch, but the index
                // still belongs to this position so the alts stay aligned
                continue;
            }

            try {
                $temporaryFile = $this->fileDownloader->download($imageUrl);
            } catch (FileDownloadException $e) {
                $messages[] = new ImportMessage(
                    ImportMessage::SEVERITY_WARNING,
                    ImportPhaseDefinition::PHASE_DATABASE,
                    $this->translator->trans('Image "%url%" could not be fetched and was skipped: %error%', ['%url%' => $imageUrl, '%error%' => $e->getMessage()], 'Admin.Advparameters.Notification'),
                    [$rowIndex],
                    'image'
                );
                continue;
            }

            try {
                $imageId = $this->commandBus->handle(
                    new AddProductImageCommand($productId, $temporaryFile, $context->getShopConstraint())
                )->getValue();

                $alt = $imageAlts[$index] ?? '';
                if ('' !== $alt) {
                    $legendCommand = new UpdateProductImageCommand($imageId, $context->getShopConstraint());
                    $legendCommand->setLocalizedLegends($isCreation ? $this->localizeForCreation($alt) : [$languageId => $alt]);
                    $this->commandBus->handle($legendCommand);
                }
            } finally {
                $this->filesystem->remove($temporaryFile);
            }
        }

        return $messages;
    }
}
