<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Grid\Data\Factory;

use PrestaShop\PrestaShop\Core\Grid\Data\GridData;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollection;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

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
     * @var int
     */
    private $contextShopId;

    /**
     * @param GridDataFactoryInterface $doctrineLanguageDataFactory
     * @param int $contextShopId
     */
    public function __construct(GridDataFactoryInterface $doctrineLanguageDataFactory, $contextShopId)
    {
        $this->doctrineLanguageDataFactory = $doctrineLanguageDataFactory;
        $this->contextShopId = $contextShopId;
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
            $languages[$i]['flag'] = $this->getFlagImagePath($language['id_lang']);
        }

        return $languages;
    }

    /**
     * @param int $languageId
     *
     * @return string
     */
    private function getFlagImagePath($languageId)
    {
        //@todo: to be refactored into adapter

        $pathToImage = _PS_IMG_DIR_ . 'l' . '/' . $languageId. '.jpg';

        $image = \ImageManager::thumbnail(
            $pathToImage,
            'lang_mini_' . $languageId . '_' . $this->contextShopId . '.jpg',
            45,
            'jpg'
        );

        return $image;
    }
}
