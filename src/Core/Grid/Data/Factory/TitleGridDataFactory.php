<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Data\Factory;

use PrestaShop\PrestaShop\Core\Domain\Title\ValueObject\Gender;
use PrestaShop\PrestaShop\Core\Grid\Data\GridData;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollection;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use PrestaShop\PrestaShop\Core\Image\ImageProviderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class TitleGridDataFactory gets data for title grid.
 */
class TitleGridDataFactory implements GridDataFactoryInterface
{
    /**
     * @var GridDataFactoryInterface
     */
    private $doctrineTitleDataFactory;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var ImageProviderInterface
     */
    private $titleImageThumbnailProvider;

    /**
     * @param GridDataFactoryInterface $doctrineTitleDataFactory
     * @param TranslatorInterface $translator
     * @param ImageProviderInterface $titleImageThumbnailProvider
     */
    public function __construct(
        GridDataFactoryInterface $doctrineTitleDataFactory,
        TranslatorInterface $translator,
        ImageProviderInterface $titleImageThumbnailProvider
    ) {
        $this->doctrineTitleDataFactory = $doctrineTitleDataFactory;
        $this->translator = $translator;
        $this->titleImageThumbnailProvider = $titleImageThumbnailProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function getData(SearchCriteriaInterface $searchCriteria)
    {
        $titleData = $this->doctrineTitleDataFactory->getData($searchCriteria);

        $modifiedRecords = $this->applyModification(
            $titleData->getRecords()->all()
        );

        return new GridData(
            new RecordCollection($modifiedRecords),
            $titleData->getRecordsTotal(),
            $titleData->getQuery()
        );
    }

    /**
     * @param array $titles
     *
     * @return array
     */
    private function applyModification(array $titles): array
    {
        foreach ($titles as $i => $title) {
            switch ($title['type']) {
                case Gender::TYPE_MALE:
                    $titles[$i]['type'] = $this->translator->trans('Male', [], 'Admin.Shopparameters.Feature');
                    break;
                case Gender::TYPE_FEMALE:
                    $titles[$i]['type'] = $this->translator->trans('Female', [], 'Admin.Shopparameters.Feature');
                    break;
                default:
                    $titles[$i]['type'] = $this->translator->trans('Other', [], 'Admin.Shopparameters.Feature');
                    break;
            }

            $titles[$i]['image'] = $this->titleImageThumbnailProvider->getPath($title['id_gender']);
        }

        return $titles;
    }
}
