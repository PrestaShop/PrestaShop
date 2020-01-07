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

namespace PrestaShopBundle\Controller\Admin\Configure\AdvancedParameters;

use Exception;
use PrestaShop\PrestaShop\Core\Domain\Profile\Command\BulkDeleteProfileCommand;
use PrestaShop\PrestaShop\Core\Domain\Profile\Command\DeleteProfileCommand;
use PrestaShop\PrestaShop\Core\Domain\Profile\Exception\CannotDeleteSuperAdminProfileException;
use PrestaShop\PrestaShop\Core\Domain\Profile\Exception\FailedToDeleteProfileException;
use PrestaShop\PrestaShop\Core\Domain\Profile\Exception\ProfileConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Profile\Exception\ProfileException;
use PrestaShop\PrestaShop\Core\Domain\Profile\Exception\ProfileNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Profile\ProfileSettings;
use PrestaShop\PrestaShop\Core\Domain\Profile\Query\GetProfileForEditing;
use PrestaShop\PrestaShop\Core\Domain\Profile\QueryResult\EditableProfile;
use PrestaShop\PrestaShop\Core\Search\Filters\ProfileFilters;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Security\Annotation\DemoRestricted;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ProfilesController is responsible for displaying the
 * "Configure > Advanced parameters > Team > Profiles" page.
 */
class ProfileController extends FrameworkBundleAdminController
{
    /**
     * Show profiles listing page.
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     *
     * @param ProfileFilters $filters
     *
     * @return Response
     */
    public function indexAction(ProfileFilters $filters)
    {
        $profilesGridFactory = $this->get('prestashop.core.grid.factory.profiles');

        return $this->render(
            '@PrestaShop/Admin/Configure/AdvancedParameters/Profiles/index.html.twig',
            [
                'layoutHeaderToolbarBtn' => [
                    'add' => [
                        'href' => $this->generateUrl('admin_profiles_create'),
                        'desc' => $this->trans('Add new profile', 'Admin.Advparameters.Feature'),
                        'icon' => 'add_circle_outline',
                  ],
                ],
                'help_link' => $this->generateSidebarLink('AdminProfiles'),
                'enableSidebar' => true,
                'layoutTitle' => $this->trans('Profiles', 'Admin.Navigation.Menu'),
                'grid' => $this->presentGrid($profilesGridFactory->getGrid($filters)),
            ]
        );
    }

    /**
     * Used for applying filtering actions.
     *
     * @AdminSecurity("is_granted(['read'], request.get('_legacy_controller'))")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function searchAction(Request $request)
    {
        $definitionFactory = $this->get('prestashop.core.grid.definition.factory.profile');
        $definitionFactory = $definitionFactory->getDefinition();

        $gridFilterFormFactory = $this->get('prestashop.core.grid.filter.form_factory');
        $searchParametersForm = $gridFilterFormFactory->create($definitionFactory);
        $searchParametersForm->handleRequest($request);

        $filters = [];

        if ($searchParametersForm->isSubmitted()) {
            $filters = $searchParametersForm->getData();
        }

        return $this->redirectToRoute('admin_profiles_index', ['filters' => $filters]);
    }

    /**
     * Show profile's create page
     *
     * @AdminSecurity("is_granted('create', request.get('_legacy_controller'))")
     * @DemoRestricted(redirectRoute="admin_profiles_index")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function createAction(Request $request)
    {
        $form = $this->get('prestashop.core.form.identifiable_object.builder.profile_form_builder')->getForm();
        $form->handleRequest($request);

        try {
            $formHandler = $this->get('prestashop.core.form.identifiable_object.handler.profile_form_handler');
            $handlerResult = $formHandler->handle($form);

            if (null !== $handlerResult->getIdentifiableObjectId()) {
                $this->addFlash('success', $this->trans('Successful creation.', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_profiles_index');
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->render('@PrestaShop/Admin/Configure/AdvancedParameters/Profiles/create.html.twig', [
            'profileForm' => $form->createView(),
            'layoutTitle' => $this->trans('Add new profile', 'Admin.Advparameters.Feature'),
            'help_link' => $this->generateSidebarLink('AdminProfiles'),
            'enableSidebar' => true,
        ]);
    }

    /**
     * Shows profile edit form.
     *
     * @AdminSecurity(
     *     "is_granted('update', request.get('_legacy_controller'))",
     *     message="You do not have permission to edit this."
     * )
     * @DemoRestricted(redirectRoute="admin_profiles_index")
     *
     * @param int $profileId
     * @param Request $request
     *
     * @return Response
     */
    public function editAction($profileId, Request $request)
    {
        $formHandler = $this->get('prestashop.core.form.identifiable_object.handler.profile_form_handler');
        $formBuilder = $this->get('prestashop.core.form.identifiable_object.builder.profile_form_builder');

        try {
            $form = $formBuilder->getFormFor((int) $profileId);
            $form->handleRequest($request);

            $handlerResult = $formHandler->handleFor((int) $profileId, $form);

            if ($handlerResult->isSubmitted() && $handlerResult->isValid()) {
                $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_profiles_index');
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));

            if ($e instanceof ProfileNotFoundException) {
                return $this->redirectToRoute('admin_profiles_index');
            }
        }

        /** @var EditableProfile $editableProfiler */
        $editableProfiler = $this->getQueryBus()->handle(new GetProfileForEditing((int) $profileId));

        return $this->render('@PrestaShop/Admin/Configure/AdvancedParameters/Profiles/edit.html.twig', [
            'profileForm' => $form->createView(),
            'layoutTitle' => $this->trans(
                'Edit: %value%',
                'Admin.Catalog.Feature',
                [
                    '%value%' => $editableProfiler->getLocalizedNames()[$this->getContextLangId()],
                ]
            ),
            'help_link' => $this->generateSidebarLink('AdminProfiles'),
            'enableSidebar' => true,
        ]);
    }

    /**
     * Delete a profile.
     *
     * @AdminSecurity(
     *     "is_granted('delete', request.get('_legacy_controller')~'_')",
     *     message="You do not have permission to edit this."
     * )
     * @DemoRestricted(redirectRoute="admin_profiles_index")
     *
     * @param int $profileId
     *
     * @return RedirectResponse
     */
    public function deleteAction($profileId)
    {
        try {
            $deleteProfileCommand = new DeleteProfileCommand($profileId);

            $this->getCommandBus()->handle($deleteProfileCommand);

            $this->addFlash('success', $this->trans('Successful deletion', 'Admin.Notifications.Success'));
        } catch (ProfileException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_profiles_index');
    }

    /**
     * Bulk delete profiles.
     *
     * @AdminSecurity(
     *     "is_granted('delete', request.get('_legacy_controller')~'_')",
     *     message="You do not have permission to edit this."
     * )
     * @DemoRestricted(redirectRoute="admin_profiles_index")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function bulkDeleteAction(Request $request)
    {
        $profileIds = $request->request->get('profile_bulk');

        try {
            $deleteProfilesCommand = new BulkDeleteProfileCommand($profileIds);

            $this->getCommandBus()->handle($deleteProfilesCommand);

            $this->addFlash('success', $this->trans('Successful deletion', 'Admin.Notifications.Success'));
        } catch (ProfileException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_profiles_index');
    }

    /**
     * Get human readable error for exception.
     *
     * @return array
     */
    protected function getErrorMessages()
    {
        return [
            ProfileConstraintException::class => [
                ProfileConstraintException::INVALID_NAME => $this->trans(
                    'This field cannot be longer than %limit% characters (incl. HTML tags)',
                    'Admin.Notifications.Error',
                    ['%limit%' => ProfileSettings::NAME_MAX_LENGTH]
                ),
            ],
            ProfileNotFoundException::class => $this->trans(
                'The object cannot be loaded (or found)',
                'Admin.Notifications.Error'
            ),
            CannotDeleteSuperAdminProfileException::class => $this->trans(
                'For security reasons, you cannot delete the Administrator\'s profile.',
                'Admin.Advparameters.Notification'
            ),
            FailedToDeleteProfileException::class => [
                FailedToDeleteProfileException::UNEXPECTED_ERROR => $this->trans(
                    'An error occurred while deleting the object.',
                    'Admin.Notifications.Error'
                ),
                FailedToDeleteProfileException::PROFILE_IS_ASSIGNED_TO_EMPLOYEE => $this->trans(
                    'Profile(s) assigned to employee cannot be deleted',
                    'Admin.Notifications.Error'
                ),
                FailedToDeleteProfileException::PROFILE_IS_ASSIGNED_TO_CONTEXT_EMPLOYEE => $this->trans(
                    'You cannot delete your own profile',
                    'Admin.Notifications.Error'
                ),
            ],
        ];
    }
}
