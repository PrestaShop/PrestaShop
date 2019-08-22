<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Controller\Admin\Improve\International;

use Exception;
use PrestaShop\PrestaShop\Core\Domain\Country\Command\BulkDeleteCountriesCommand;
use PrestaShop\PrestaShop\Core\Domain\Country\Command\BulkToggleCountriesStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Country\Command\ToggleCountryStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CountryConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CountryException;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CountryNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\DeleteCountryException;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\UpdateCountryException;
use PrestaShop\PrestaShop\Core\Domain\Country\Query\GetCountryStatus;
use PrestaShop\PrestaShop\Core\Domain\Country\QueryResult\IsActiveCountry;
use PrestaShop\PrestaShop\Core\Form\FormHandlerInterface;
use PrestaShop\PrestaShop\Core\Search\Filters\CountryFilters;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Security\Annotation\DemoRestricted;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Responsible for International > Locations > Countries data
 */
class CountryController extends FrameworkBundleAdminController
{
    /**
     * Show countries listing page
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     *
     * @param Request $request
     * @param CountryFilters $filters
     *
     * @return Response
     */
    public function indexAction(Request $request, CountryFilters $filters): Response
    {
        $countryGridFactory = $this->get('prestashop.core.grid.grid_factory.country');
        $countryGrid = $countryGridFactory->getGrid($filters);
        $countryOptionsForm = $this->getCountryOptionsFormHandler()->getForm();

        return $this->render('@PrestaShop/Admin/Improve/International/Locations/Country/index.html.twig', [
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'countryGrid' => $this->presentGrid($countryGrid),
            'enableSidebar' => true,
            'layoutHeaderToolbarBtn' => $this->getCountryToolbarButtons(),
            'countryOptionsForm' => $countryOptionsForm->createView(),
        ]);
    }

    /**
     * Delete countries in bulk action.
     *
     * @AdminSecurity(
     *     "is_granted('delete', request.get('_legacy_controller'))",
     *     redirectRoute="admin_countries_index",
     *     message="You do not have permission to delete this."
     * )
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function deleteBulkAction(Request $request): RedirectResponse
    {
        $countryIds = $this->getBulkCountriesFromRequest($request);

        try {
            $this->getCommandBus()->handle(new BulkDeleteCountriesCommand($countryIds));
            $this->addFlash(
                'success',
                $this->trans('Successful deletion.', 'Admin.Notifications.Success')
            );
        } catch (CountryException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_countries_index');
    }

    /**
     * Toggles country status
     *
     * @AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute="admin_countries_index")
     *
     * @param int $countryId
     *
     * @return RedirectResponse
     */
    public function toggleStatusAction(int $countryId): RedirectResponse
    {
        try {
            /** @var IsActiveCountry $activeCountryResult */
            $activeCountryResult = $this->getQueryBus()->handle(new GetCountryStatus($countryId));
            $this->getCommandBus()->handle(
                new ToggleCountryStatusCommand((int) $countryId, !$activeCountryResult->isActive())
            );
            $this->addFlash(
                'success',
                $this->trans('The status has been successfully updated.', 'Admin.Notifications.Success')
            );
        } catch (CountryException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_countries_index');
    }

    /**
     * Enables states on bulk action
     *
     * @AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute="admin_countries_index")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function bulkEnableAction(Request $request): RedirectResponse
    {
        $countryIds = $this->getBulkCountriesFromRequest($request);

        try {
            $this->getCommandBus()->handle(new BulkToggleCountriesStatusCommand($countryIds, true));
            $this->addFlash(
                'success',
                $this->trans('The status has been successfully updated.', 'Admin.Notifications.Success')
            );
        } catch (CountryException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_countries_index');
    }

    /**
     * Disables countries on bulk action
     *
     * @AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute="admin_countries_index")
     * @DemoRestricted(redirectRoute="admin_countries_index")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function bulkDisableAction(Request $request): RedirectResponse
    {
        $countryIds = $this->getBulkCountriesFromRequest($request);

        try {
            $this->getCommandBus()->handle(new BulkToggleCountriesStatusCommand($countryIds, false));
            $this->addFlash(
                'success',
                $this->trans('The status has been successfully updated.', 'Admin.Notifications.Success')
            );
        } catch (CountryException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_countries_index');
    }

    /**
     * Process country options configuration form.
     *
     * @AdminSecurity(
     *     "is_granted(['update', 'create', 'delete'], request.get('_legacy_controller'))",
     *     redirectRoute="admin_countries_index"
     * )
     *
     * @param Request $request
     *
     * @return RedirectResponse
     *
     * @throws Exception
     */
    public function saveOptionsAction(Request $request)
    {
        $countryOptionsFormHandler = $this->getCountryOptionsFormHandler();

        $countryOptionsForm = $countryOptionsFormHandler->getForm();
        $countryOptionsForm->handleRequest($request);

        if ($countryOptionsForm->isSubmitted()) {
            $errors = $countryOptionsFormHandler->save($countryOptionsForm->getData());

            if (empty($errors)) {
                $this->addFlash('success', $this->trans('Update successful', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_countries_index');
            }

            $this->flashErrors($errors);
        }

        return $this->redirectToRoute('admin_countries_index');
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    private function getBulkCountriesFromRequest(Request $request): array
    {
        $stateIds = $request->request->get('country_countries_bulk');

        if (!is_array($stateIds)) {
            return [];
        }

        foreach ($stateIds as $i => $stateId) {
            $stateIds[$i] = (int) $stateId;
        }

        return $stateIds;
    }

    /**
     * @return array
     */
    private function getErrorMessages(): array
    {
        return [
            DeleteCountryException::class => $this->trans(
                'An error occurred while deleting the object.',
                'Admin.Notifications.Error'
            ),
            CountryNotFoundException::class => $this->trans(
                'The object cannot be loaded (or found)',
                'Admin.Notifications.Error'
            ),
            UpdateCountryException::class => [
                UpdateCountryException::FAILED_BULK_UPDATE_STATUS => $this->trans(
                    'An error occurred while updating the status.',
                    'Admin.Notifications.Error'
                ),
                UpdateCountryException::FAILED_UPDATE_STATUS => $this->trans(
                    'An error occurred while updating the status.',
                    'Admin.Notifications.Error'
                ),
            ],
            CountryConstraintException::class => [
                CountryConstraintException::INVALID_ID => $this->trans(
                    'The object cannot be loaded (the identifier is missing or invalid)',
                    'Admin.Notifications.Error'
                ),
            ],
        ];
    }

    /**
     * @return array
     */
    private function getCountryToolbarButtons(): array
    {
        $toolbarButtons = [];

        $toolbarButtons['add'] = [
            'href' => $this->generateUrl('admin_countries_create'),
            'desc' => $this->trans('Add new country', 'Admin.International.Feature'),
            'icon' => 'add_circle_outline',
        ];

        return $toolbarButtons;
    }

    /**
     * @return FormHandlerInterface
     */
    private function getCountryOptionsFormHandler(): FormHandlerInterface
    {
        return $this->get('prestashop.admin.country_options.form_handler');
    }

    /**
     * Show "Add new" form and handle form submit.
     *
     * @AdminSecurity(
     *     "is_granted(['create'], request.get('_legacy_controller'))",
     *     redirectRoute="admin_countries_index",
     *     message="You do not have permission to create this."
     * )
     *
     * @return Response
     */
    public function createAction(): Response
    {
        $link = $this->getAdminLink('AdminCountries', ['addcountry' => 1]);

        return $this->redirect($link);
    }

    /**
     * Handles edit form rendering and submission
     *
     * @AdminSecurity(
     *     "is_granted('update', request.get('_legacy_controller'))",
     *     redirectRoute="admin_countries_index"
     * )
     *
     * @param int $countryId
     *
     * @return Response
     */
    public function editAction(int $countryId): Response
    {
        $link = $this->getAdminLink('AdminCountries', ['id_country' => $countryId, 'updatecountry' => 1]);

        return $this->redirect($link);
    }
}
