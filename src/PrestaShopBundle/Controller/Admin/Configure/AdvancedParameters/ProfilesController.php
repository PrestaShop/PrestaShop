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

namespace PrestaShopBundle\Controller\Admin\Configure\AdvancedParameters;

use PrestaShop\PrestaShop\Core\Search\Filters\ProfilesFilters;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Security\Annotation\DemoRestricted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ProfilesController is responsible for displaying the
 * "Configure > Advanced parameters > Team > Profiles" page.
 */
class ProfilesController extends FrameworkBundleAdminController
{
    /**
     * Show profiles listing page.
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     *
     * @Template("@PrestaShop/Admin/Configure/AdvancedParameters/Profiles/profiles.html.twig")
     *
     * @param ProfilesFilters $filters
     *
     * @return array
     */
    public function indexAction(ProfilesFilters $filters)
    {
        $profilesGridFactory = $this->get('prestashop.core.grid.factory.profiles');
        $gridPresenter = $this->get('prestashop.core.grid.presenter.grid_presenter');

        return [
            'layoutHeaderToolbarBtn' => [
                'add' => [
                    'href' => $this->getAdminLink('AdminProfiles', ['addprofile' => '']),
                    'desc' => $this->trans('Add new profile', 'Admin.Advparameters.Feature'),
                    'icon' => 'add_circle_outline',
                ],
            ],
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink('AdminProfiles'),
            'grid' => $gridPresenter->present($profilesGridFactory->getGrid($filters)),
        ];
    }

    /**
     * Used for applying filtering actions.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function searchAction(Request $request)
    {
        $definitionFactory = $this->get('prestashop.core.grid.definition.factory.profiles');
        $definitionFactory = $definitionFactory->getDefinition();

        $gridFilterFormFactory = $this->get('prestashop.core.grid.filter.form_factory');
        $searchParametersForm = $gridFilterFormFactory->create($definitionFactory);
        $searchParametersForm->handleRequest($request);

        $filters = [];

        if ($searchParametersForm->isSubmitted()) {
            $filters = $searchParametersForm->getData();
        }

        return $this->redirectToRoute('admin_profiles', ['filters' => $filters]);
    }

    /**
     * Shows profile edit form.
     *
     * @AdminSecurity("is_granted(
     *     'update',
     *     request.get('_legacy_controller'))",
     *     message="You do not have permission to edit this."
     * )
     *
     * @param int $profileId
     *
     * @return RedirectResponse
     */
    public function editAction($profileId)
    {
        $legacyLink = $this->getAdminLink('AdminProfiles', [
            'id_profile' => $profileId,
            'updateprofile' => 1,
        ]);

        return $this->redirect($legacyLink);
    }

    /**
     * Delete a profile.
     *
     * @AdminSecurity(
     *     "is_granted(['delete'], request.get('_legacy_controller')~'_')",
     *     message="You do not have permission to edit this."
     * )
     * @DemoRestricted(redirectRoute="admin_profiles")
     *
     * @param int $profileId
     *
     * @return RedirectResponse
     */
    public function deleteAction($profileId)
    {
        //@todo implement
        $this->flashErrors(['not implemented']);
        return $this->redirectToRoute('admin_profiles');
    }

    /**
     * Bulk delete profiles.
     *
     * @AdminSecurity(
     *     "is_granted(['delete'], request.get('_legacy_controller')~'_')",
     *     message="You do not have permission to edit this."
     * )
     * @DemoRestricted(redirectRoute="admin_profiles")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function bulkDeleteAction(Request $request)
    {
        $profileIds = $request->request->get('profiles_bulk');

        //@todo implement
        $this->flashErrors(['not implemented']);
        return $this->redirectToRoute('admin_profiles');
    }
}
