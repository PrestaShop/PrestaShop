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

namespace PrestaShopBundle\Controller\Admin\Improve\Design;

use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Command\DeleteCmsPageCategoryCommand;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Command\ToggleCmsPageCategoryStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Exception\CannotDeleteCmsPageCategoryException;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Exception\CannotToggleCmsPageCategoryStatusException;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Exception\CmsPageCategoryException;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Exception\CmsPageCategoryNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\ValueObject\CmsPageCategoryId;
use PrestaShop\PrestaShop\Core\Search\Filters\CmsCategoryFilters;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CmsPageController is responsible for handling the logic in "Improve -> Design -> pages" page.
 */
class CmsPageController extends FrameworkBundleAdminController
{
    /**
     * @Template("@PrestaShop/Admin/Improve/Design/Cms/index.html.twig")
     *
     * @param CmsCategoryFilters $filters
     *
     * @return array
     */
    public function indexAction(CmsCategoryFilters $filters)
    {
        $cmsCategoryGridFactory = $this->get('prestashop.core.grid.factory.cms_page_category');
        $cmsCategoryGrid = $cmsCategoryGridFactory->getGrid($filters);

        $gridPresenter = $this->get('prestashop.core.grid.presenter.grid_presenter');

        return [
            'cmsCategoryGrid' => $gridPresenter->present($cmsCategoryGrid),
        ];
    }

    /**
     * Implements filtering for the cms page category list.
     *
     * @param int $cmsCategoryParentId
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function searchCategoryAction($cmsCategoryParentId, Request $request)
    {
        $definitionFactory = $this->get('prestashop.core.grid.definition.factory.cms_page_category');
        $definitionFactory = $definitionFactory->getDefinition();

        $gridFilterFormFactory = $this->get('prestashop.core.grid.filter.form_factory');
        $searchParametersForm = $gridFilterFormFactory->create($definitionFactory);
        $searchParametersForm->handleRequest($request);

        $filters = [];
        if ($searchParametersForm->isSubmitted()) {
            $filters = $searchParametersForm->getData();
        }

        return $this->redirectToRoute('admin_cms_page_index', compact('cmsCategoryParentId', 'filters'));
    }

    public function editCmsCategoryAction($cmsCategoryParentId, $cmsCategoryId)
    {
//        todo: remove legacy parts once form is ready
        $legacyLink = $this->getAdminLink('AdminMeta', [
            'id_cms_category' => $cmsCategoryId,
            'updatecms_category' => 1,
        ]);

        return $this->redirect($legacyLink);
    }

    public function deleteCmsCategoryAction($cmsCategoryParentId, $cmsCategoryId)
    {
        $redirectTo = $this->redirectToRoute('admin_cms_page_index', compact('cmsCategoryParentId'));

        try {
            $cmsPageCategoryId = new CmsPageCategoryId($cmsCategoryId);
            $this->getCommandBus()->handle(new DeleteCmsPageCategoryCommand($cmsPageCategoryId));
        } catch (CmsPageCategoryException $exception) {
            $this->addFlash('error', $this->getCmsPageCategoryErrorByExceptionType($exception));

            return $redirectTo;
        }

        $this->addFlash(
            'success',
            $this->trans('Successful deletion.', 'Admin.Notifications.Success')
        );

        return $redirectTo;
    }

    public function toggleCmsCategoryAction($cmsCategoryParentId, $cmsCategoryId)
    {
        $redirectTo = $this->redirectToRoute('admin_cms_page_index', compact('cmsCategoryParentId'));

        try {
            $cmsPageCategoryId = new CmsPageCategoryId($cmsCategoryId);
            $this->getCommandBus()->handle(new ToggleCmsPageCategoryStatusCommand($cmsPageCategoryId));
        } catch (CmsPageCategoryException $exception) {
            $this->addFlash('error', $this->getCmsPageCategoryErrorByExceptionType($exception));

            return $redirectTo;
        }

        $this->addFlash(
            'success',
            $this->trans('The status has been successfully updated.', 'Admin.Notifications.Success')
        );

        return $redirectTo;
    }

    public function updateCmsCategoryPositionAction($cmsCategoryParentId, $cmsCategoryId)
    {
        // todo : implement
    }

    /**
     * Gets error by exception type.
     *
     * @param CmsPageCategoryException $exception
     *
     * @return string
     */
    private function getCmsPageCategoryErrorByExceptionType(CmsPageCategoryException $exception)
    {
        $exceptionTypeDictionary = [
            CmsPageCategoryNotFoundException::class => $this->trans(
                'The object cannot be loaded (or found)',
                'Admin.Notifications.Error'
            ),
            CannotDeleteCmsPageCategoryException::class => $this->trans(
                'You cannot delete this item.',
                'Admin.Notifications.Error'
            ),
            CannotToggleCmsPageCategoryStatusException::class => $this->trans(
                'An error occurred while updating the status.',
                'Admin.Notifications.Error'
            )
        ];

        $exceptionType = get_class($exception);
        if (isset($exceptionTypeDictionary[$exceptionType])) {
            return $exceptionTypeDictionary[$exceptionType];
        }
        return $this->trans('Unexpected error occurred.', 'Admin.Notifications.Error');
    }
}
