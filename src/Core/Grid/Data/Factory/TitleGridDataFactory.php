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
