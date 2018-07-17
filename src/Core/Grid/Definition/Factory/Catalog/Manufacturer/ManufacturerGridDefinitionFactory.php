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

namespace PrestaShop\PrestaShop\Core\Grid\Definition\Factory\Catalog\Manufacturer;

use PrestaShop\PrestaShop\Adapter\ImageManager;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Adapter\Manufacturer\ManufacturerListingThumbnailGenerator;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnFilterOption;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\BulkActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ContentColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ImageColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\AbstractGridDefinitionFactory;
use PrestaShopBundle\Form\Admin\Type\SearchAndResetFormType;

/**
 * Class ManufacturerGridDefinitionFactory is responsible for creating Manufacturers grid definition
 */
final class ManufacturerGridDefinitionFactory extends AbstractGridDefinitionFactory
{
    /**
     * @var string
     */
    private $searchResetUrl;

    /**
     * @var string
     */
    private $redirectUrl;

    /**
     * @var ImageManager
     */
    private $imageManager;

    /**
     * @param ImageManager $imageManager
     * @param string       $searchResetUrl
     * @param string       $redirectUrl
     */
    public function __construct(ImageManager $imageManager, $searchResetUrl, $redirectUrl)
    {
        $this->searchResetUrl = $searchResetUrl;
        $this->redirectUrl = $redirectUrl;
        $this->imageManager = $imageManager;
    }

    /**
     * {@inheritdoc}
     */
    protected function getId()
    {
        return 'manufacturers';
    }

    /**
     * {@inheritdoc}
     */
    protected function getName()
    {
        return $this->trans('Brands', [], 'Admin.Catalog.Feature');
    }

    /**
     * {@inheritdoc}
     */
    protected function getColumns()
    {
        $imageManager = $this->imageManager;

        return (new ColumnCollection())
            ->add((new BulkActionColumn('bulk'))
                ->setOptions([
                    'bulk_field' => 'id_manufacturer',
                ])
            )
            ->add((new DataColumn('id_manufacturer'))
                ->setName($this->trans('ID', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'id_manufacturer',
                ])
            )
            ->add((new ContentColumn('logo'))
                ->setName($this->trans('Logo', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'logo',
                    'modifier' => function (array $row) use ($imageManager) {
                        $row['logo'] = $imageManager->getThumbnailForListing(
                            $row['id_manufacturer'],
                            'jpg',
                            'manufacturer',
                            'm'
                        );

                        return $row;
                    }
                ])
            )
            ->add((new DataColumn('name'))
                ->setName($this->trans('Name', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'name',
                ])
            )
            ->add((new DataColumn('addresses'))
                ->setName($this->trans('Addresses', [], 'Admin.Catalog.Feature'))
                ->setOptions([
                    'field' => 'addresses_count',
                    'align' => 'center',
                    'modifier' => function (array $row) {
                        $row['addresses_count'] = $row['addresses_count'] ?: '--';

                        return $row;
                    },
                ])
            )
            ->add((new DataColumn('products'))
                ->setName($this->trans('Products', [], 'Admin.Catalog.Feature'))
                ->setOptions([
                    'field' => 'products_count',
                    'align' => 'center',
                ])
            )
            ->add((new DataColumn('status'))
                ->setName($this->trans('Enabled', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'active',
                    'align' => 'center',
                ])
            )
            ->add((new ActionColumn('actions'))
                ->setName($this->trans('Actions', [], 'Admin.Global'))
                ->setOptions([
                    'filter' => new ColumnFilterOption(SearchAndResetFormType::class, [
                        'attr' => [
                            'data-url' => $this->searchResetUrl,
                            'data-redirect' => $this->redirectUrl,
                        ],
                    ]),
                ])
            )
        ;
    }
}
