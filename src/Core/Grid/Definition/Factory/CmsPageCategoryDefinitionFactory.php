<?php
/**
 * 2007-2018 PrestaShop.
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

namespace PrestaShop\PrestaShop\Core\Grid\Definition\Factory;

use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\LinkRowAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\SubmitRowAction;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\BulkActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollection;
use PrestaShopBundle\Form\Admin\Type\SearchAndResetType;
use PrestaShopBundle\Form\Admin\Type\YesAndNoChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class CmsPageCategoryDefinitionFactory
 */
final class CmsPageCategoryDefinitionFactory extends AbstractGridDefinitionFactory
{
    /**
     * @var string
     */
    private $resetActionUrl;

    /**
     * @var string
     */
    private $redirectionUrl;

    /**
     * @var int
     */
    private $cmsCategoryParentId;

    /**
     * @param UrlGeneratorInterface $urlGenerator
     * @param RequestStack $requestStack
     */
    public function __construct(UrlGeneratorInterface $urlGenerator, RequestStack $requestStack)
    {
        $this->resetActionUrl =  $urlGenerator->generate('admin_common_reset_search', [
            'controller' => 'CmsPage',
            'action' => 'index',
        ]);

        $request = $requestStack->getCurrentRequest();

        if (null !== $request) {
            $this->cmsCategoryParentId = $request->attributes->get('cmsCategoryParentId');
        }

        $this->redirectionUrl = $urlGenerator->generate('admin_cms_page_index', [
            'cmsCategoryParentId' => $this->cmsCategoryParentId,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function getId()
    {
        return 'Cms_page_category';
    }

    /**
     * {@inheritdoc}
     */
    protected function getName()
    {
        return $this->trans('Categories', [], 'Admin.Navigation.Menu');
    }

    /**
     * {@inheritdoc}
     */
    protected function getColumns()
    {
        return (new ColumnCollection())
            ->add((new BulkActionColumn('bulk'))
                ->setOptions([
                    'bulk_field' => 'id_cms_category',
                ])
            )
            ->add((new DataColumn('id_cms_category'))
                ->setName($this->trans('ID', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'id_cms_category',
                ])
            )
            ->add((new DataColumn('name'))
                ->setName($this->trans('Name', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'name',
                ])
            )
            ->add((new DataColumn('description'))
                ->setName($this->trans('Description', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'description',
                    'sortable' => false,
                ])
            )
            ->add((new DataColumn('position'))
                ->setName($this->trans('Position', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'position',
                ])
            )
            ->add((new DataColumn('active'))
                ->setName($this->trans('Displayed', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'active',
                    'sortable' => false,
                ])
            )
            ->add((new ActionColumn('actions'))
                ->setName($this->trans('Actions', [], 'Admin.Global'))
                ->setOptions([
                    'actions' => (new RowActionCollection())
                        ->add((new LinkRowAction('edit'))
                            ->setName($this->trans('Edit', [], 'Admin.Actions'))
                            ->setIcon('edit')
                            ->setOptions([
                                'route' => 'admin_cms_page_edit_cms_category',
                                'route_param_name' => 'cmsCategoryId',
                                'route_param_field' => 'id_cms_category',
                                'route_param_extra_fields' => [
                                    'cmsCategoryParentId' => $this->cmsCategoryParentId,
                                ],
                            ])
                        )
                        ->add((new SubmitRowAction('delete'))
                            ->setName($this->trans('Delete', [], 'Admin.Actions'))
                            ->setIcon('delete')
                            ->setOptions([
                                'method' => 'DELETE',
                                'route' => 'admin_cms_page_delete_cms_category',
                                'route_param_name' => 'cmsCategoryId',
                                'route_param_field' => 'id_cms_category',
                                'route_param_extra_fields' => [
                                    'cmsCategoryParentId' => $this->cmsCategoryParentId,
                                ],
                                'confirm_message' => $this->trans(
                                    'Delete selected item?',
                                    [],
                                    'Admin.Notifications.Warning'
                                ),
                            ])
                        ),
                ])
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function getFilters()
    {
        return (new FilterCollection())
            ->add((new Filter('id_cms_category', TextType::class))
                ->setTypeOptions([
                    'required' => false,
                ])
                ->setAssociatedColumn('id_cms_category')
            )
            ->add((new Filter('name', TextType::class))
                ->setTypeOptions([
                    'required' => false,
                ])
                ->setAssociatedColumn('name')
            )
            ->add((new Filter('description', TextType::class))
                ->setTypeOptions([
                    'required' => false,
                ])
                ->setAssociatedColumn('description')
            )
            ->add((new Filter('position', TextType::class))
                ->setTypeOptions([
                    'required' => false,
                ])
                ->setAssociatedColumn('position')
            )
            ->add((new Filter('active', YesAndNoChoiceType::class))
                ->setAssociatedColumn('active')
            )
            ->add((new Filter('actions', SearchAndResetType::class))
                ->setTypeOptions([
                    'attr' => [
                        'data-url' => $this->resetActionUrl,
                        'data-redirect' => $this->redirectionUrl,
                    ],
                ])
                ->setAssociatedColumn('actions')
            )
        ;
    }
}
