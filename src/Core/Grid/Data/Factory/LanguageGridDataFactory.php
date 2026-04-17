<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Data\Factory;

use PrestaShop\PrestaShop\Core\Grid\Data\GridData;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollection;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use PrestaShop\PrestaShop\Core\Image\ImageProviderInterface;

/**
 * Class LanguageGridDataFactory gets data for languages grid.
 */
final class LanguageGridDataFactory implements GridDataFactoryInterface
{
    /**
     * @var GridDataFactoryInterface
     */
    private $doctrineLanguageDataFactory;

    /**
     * @var ImageProviderInterface
     */
    private $languageFlagThumbnailProvider;

    /**
     * @param GridDataFactoryInterface $doctrineLanguageDataFactory
     * @param ImageProviderInterface $languageFlagThumbnailProvider
     */
    public function __construct(
        GridDataFactoryInterface $doctrineLanguageDataFactory,
        ImageProviderInterface $languageFlagThumbnailProvider
    ) {
        $this->doctrineLanguageDataFactory = $doctrineLanguageDataFactory;
        $this->languageFlagThumbnailProvider = $languageFlagThumbnailProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function getData(SearchCriteriaInterface $searchCriteria)
    {
        $languageData = $this->doctrineLanguageDataFactory->getData($searchCriteria);

        $modifiedRecords = $this->applyModification(
            $languageData->getRecords()->all()
        );

        return new GridData(
            new RecordCollection($modifiedRecords),
            $languageData->getRecordsTotal(),
            $languageData->getQuery()
        );
    }

    /**
     * @param array $languages
     *
     * @return array
     */
    private function applyModification(array $languages)
    {
        foreach ($languages as $i => $language) {
            $languages[$i]['flag'] = $this->languageFlagThumbnailProvider->getPath($language['id_lang']);
        }

        return $languages;
    }
}
