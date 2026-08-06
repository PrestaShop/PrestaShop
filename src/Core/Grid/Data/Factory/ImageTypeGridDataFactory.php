<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Data\Factory;

use PrestaShop\PrestaShop\Core\Domain\ImageSettings\ValueObject\ImageFitment;
use PrestaShop\PrestaShop\Core\Grid\Data\GridData;
use PrestaShop\PrestaShop\Core\Grid\Data\GridDataInterface;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollection;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Decorates image type grid data with translated image fitment labels.
 */
final class ImageTypeGridDataFactory implements GridDataFactoryInterface
{
    public function __construct(
        private readonly GridDataFactoryInterface $imageTypeGridDataFactory,
        private readonly TranslatorInterface $translator,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function getData(SearchCriteriaInterface $searchCriteria): GridDataInterface
    {
        $data = $this->imageTypeGridDataFactory->getData($searchCriteria);
        $records = $data->getRecords()->all();

        foreach ($records as &$record) {
            $record['image_fitment_name'] = $this->getImageFitmentName($record['image_fitment']);
        }

        return new GridData(
            new RecordCollection($records),
            $data->getRecordsTotal(),
            $data->getQuery()
        );
    }

    /**
     * Gets translated image fitment name for the grid.
     */
    private function getImageFitmentName(string $imageFitment): string
    {
        $imageFitmentNames = [
            ImageFitment::FIT => $this->translator->trans('Fit to size', [], 'Admin.Design.Feature'),
            ImageFitment::CROP => $this->translator->trans('Fill and crop', [], 'Admin.Design.Feature'),
            ImageFitment::BOUND => $this->translator->trans('Keep ratio', [], 'Admin.Design.Feature'),
        ];

        return $imageFitmentNames[$imageFitment] ?? $imageFitment;
    }
}
