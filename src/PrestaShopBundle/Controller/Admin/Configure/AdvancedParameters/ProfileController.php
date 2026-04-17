<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Controller\Admin\Configure\AdvancedParameters;

use Exception;
use ImageManager;
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
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Builder\FormBuilderInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Handler\FormHandlerInterface;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\GridDefinitionFactoryInterface;
use PrestaShop\PrestaShop\Core\Grid\Filter\GridFilterFormFactory;
use PrestaShop\PrestaShop\Core\Grid\GridFactoryInterface;
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\UploadedImageConstraintException;
use PrestaShop\PrestaShop\Core\Search\Filters\ProfileFilters;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use PrestaShopBundle\Controller\Attribute\AllShopContext;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use PrestaShopBundle\Security\Attribute\DemoRestricted;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ProfileController is responsible for displaying the
 * "Configure > Advanced parameters > Team > Roles" page.
 */
#[AllShopContext]
class ProfileController extends PrestaShopAdminController
{
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function indexAction(
        ProfileFilters $filters,
        #[Autowire(service: 'prestashop.core.grid.factory.profiles')]
        GridFactoryInterface $profilesGridFactory,
    ): Response {
        return $this->render(
            '@PrestaShop/Admin/Configure/AdvancedParameters/Profiles/index.html.twig',
            [
                'layoutHeaderToolbarBtn' => [
                    'add' => [
                        'href' => $this->generateUrl('admin_profiles_create'),
                        'desc' => $this->trans('Add new role', [], 'Admin.Advparameters.Feature'),
                        'icon' => 'add_circle_outline',
                    ],
                ],
                'help_link' => $this->generateSidebarLink('AdminProfiles'),
                'enableSidebar' => true,
                'layoutTitle' => $this->trans('Roles', [], 'Admin.Navigation.Menu'),
                'grid' => $this->presentGrid($profilesGridFactory->getGrid($filters)),
                'multistoreInfoTip' => $this->trans(
                    'Note that this page is available in all shops context only, this is why your context has just switched.',
                    [],
                    'Admin.Notifications.Info'
                ),
                'multistoreIsUsed' => $this->getShopContext()->isMultiShopUsed(),
            ]
        );
    }

    /**
     * Used for applying filtering actions.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function searchAction(
        Request $request,
        #[Autowire(service: 'prestashop.core.grid.definition.factory.profile')]
        GridDefinitionFactoryInterface $definitionFactory,
        GridFilterFormFactory $gridFilterFormFactory,
    ): RedirectResponse {
        $definitionFactory = $definitionFactory->getDefinition();
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
     * @param Request $request
     *
     * @return Response
     */
    #[DemoRestricted(redirectRoute: 'admin_profiles_index')]
    #[AdminSecurity("is_granted('create', request.get('_legacy_controller'))")]
    public function createAction(
        Request $request,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.builder.profile_form_builder')]
        FormBuilderInterface $formBuilder,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.handler.profile_form_handler')]
        FormHandlerInterface $formHandler,
    ): Response {
        $form = $formBuilder->getForm();
        $form->handleRequest($request);

        try {
            $handlerResult = $formHandler->handle($form);

            if (null !== $handlerResult->getIdentifiableObjectId()) {
                $this->addFlash('success', $this->trans('Successful creation', [], 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_profiles_index');
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->render('@PrestaShop/Admin/Configure/AdvancedParameters/Profiles/create.html.twig', [
            'profileForm' => $form->createView(),
            'layoutTitle' => $this->trans('New role', [], 'Admin.Navigation.Menu'),
            'help_link' => $this->generateSidebarLink('AdminProfiles'),
            'enableSidebar' => true,
            'multistoreInfoTip' => $this->trans(
                'Note that this feature is only available in the "all stores" context. It will be added to all your stores.',
                [],
                'Admin.Notifications.Info'
            ),
            'multistoreIsUsed' => $this->getShopContext()->isMultiShopUsed(),
        ]);
    }

    /**
     * Shows profile edit form.
     *
     * @param int $profileId
     * @param Request $request
     *
     * @return Response
     */
    #[DemoRestricted(redirectRoute: 'admin_profiles_index')]
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", message: 'You do not have permission to edit this.')]
    public function editAction(
        int $profileId,
        Request $request,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.builder.profile_form_builder')]
        FormBuilderInterface $formBuilder,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.handler.profile_form_handler')]
        FormHandlerInterface $formHandler,
    ): Response {
        try {
            $form = $formBuilder->getFormFor((int) $profileId);
        } catch (Exception $exception) {
            $this->addFlash(
                'error',
                $this->getErrorMessageForException($exception, $this->getErrorMessages())
            );

            return $this->redirectToRoute('admin_profiles_index');
        }

        try {
            $form->handleRequest($request);
            $handlerResult = $formHandler->handleFor((int) $profileId, $form);

            if ($handlerResult->isSubmitted() && $handlerResult->isValid()) {
                $this->addFlash('success', $this->trans('Successful update', [], 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_profiles_index');
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));

            if ($e instanceof ProfileNotFoundException) {
                return $this->redirectToRoute('admin_profiles_index');
            }
        }

        /** @var EditableProfile $editableProfile */
        $editableProfile = $this->dispatchQuery(new GetProfileForEditing($profileId));

        return $this->render('@PrestaShop/Admin/Configure/AdvancedParameters/Profiles/edit.html.twig', [
            'profileForm' => $form->createView(),
            'layoutTitle' => $this->trans(
                'Editing %role_name% role',
                [
                    '%role_name%' => $editableProfile->getLocalizedNames()[$this->getLanguageContext()->getId()],
                ],
                'Admin.Navigation.Menu',
            ),
            'help_link' => $this->generateSidebarLink('AdminProfiles'),
            'enableSidebar' => true,
        ]);
    }

    /**
     * Delete a profile.
     *
     * @param int $profileId
     *
     * @return RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_profiles_index')]
    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", message: 'You do not have permission to edit this.')]
    public function deleteAction(int $profileId): RedirectResponse
    {
        try {
            $deleteProfileCommand = new DeleteProfileCommand($profileId);

            $this->dispatchCommand($deleteProfileCommand);

            $this->addFlash('success', $this->trans('Successful deletion', [], 'Admin.Notifications.Success'));
        } catch (ProfileException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_profiles_index');
    }

    /**
     * Bulk delete profiles.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_profiles_index')]
    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", message: 'You do not have permission to edit this.')]
    public function bulkDeleteAction(Request $request): RedirectResponse
    {
        $profileIds = $request->request->all('profile_bulk');

        try {
            $this->dispatchCommand(new BulkDeleteProfileCommand($profileIds));

            $this->addFlash('success', $this->trans('Successful deletion', [], 'Admin.Notifications.Success'));
        } catch (ProfileException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_profiles_index');
    }

    /**
     * Get human-readable error for exception.
     *
     * @return array
     */
    protected function getErrorMessages(): array
    {
        return [
            UploadedImageConstraintException::class => $this->trans(
                'Image format not recognized, allowed formats are: %s',
                [implode(', ', ImageManager::EXTENSIONS_SUPPORTED)],
                'Admin.Notifications.Error',
            ),
            ProfileConstraintException::class => [
                ProfileConstraintException::INVALID_NAME => $this->trans(
                    'This field cannot be longer than %limit% characters (incl. HTML tags)',
                    ['%limit%' => ProfileSettings::NAME_MAX_LENGTH],
                    'Admin.Notifications.Error',
                ),
            ],
            ProfileNotFoundException::class => $this->trans(
                'The object cannot be loaded (or found).',
                [],
                'Admin.Notifications.Error'
            ),
            CannotDeleteSuperAdminProfileException::class => $this->trans(
                'For security reasons, you cannot delete the Administrator\'s role.',
                [],
                'Admin.Advparameters.Notification'
            ),
            FailedToDeleteProfileException::class => [
                FailedToDeleteProfileException::UNEXPECTED_ERROR => $this->trans(
                    'An error occurred while deleting the object.',
                    [],
                    'Admin.Notifications.Error'
                ),
                FailedToDeleteProfileException::PROFILE_IS_ASSIGNED_TO_EMPLOYEE => $this->trans(
                    'Role(s) assigned to employee cannot be deleted',
                    [],
                    'Admin.Notifications.Error'
                ),
                FailedToDeleteProfileException::PROFILE_IS_ASSIGNED_TO_CONTEXT_EMPLOYEE => $this->trans(
                    'You cannot delete your own role',
                    [],
                    'Admin.Notifications.Error'
                ),
            ],
        ];
    }
}
