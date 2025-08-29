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

namespace PrestaShopBundle\Controller\Admin\Improve\International;

use Exception;
use PrestaShop\PrestaShop\Core\Configuration\IniConfiguration;
use PrestaShop\PrestaShop\Core\Domain\Language\Command\BulkDeleteLanguagesCommand;
use PrestaShop\PrestaShop\Core\Domain\Language\Command\BulkToggleLanguagesStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Language\Command\DeleteLanguageCommand;
use PrestaShop\PrestaShop\Core\Domain\Language\Command\ToggleLanguageStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Language\Exception\CannotDisableDefaultLanguageException;
use PrestaShop\PrestaShop\Core\Domain\Language\Exception\CopyingNoPictureException;
use PrestaShop\PrestaShop\Core\Domain\Language\Exception\DefaultLanguageException;
use PrestaShop\PrestaShop\Core\Domain\Language\Exception\LanguageConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Language\Exception\LanguageException;
use PrestaShop\PrestaShop\Core\Domain\Language\Exception\LanguageImageUploadingException;
use PrestaShop\PrestaShop\Core\Domain\Language\Exception\LanguageNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Language\Query\GetLanguageForEditing;
use PrestaShop\PrestaShop\Core\Domain\Language\QueryResult\EditableLanguage;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Builder\FormBuilderInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Handler\FormHandlerInterface;
use PrestaShop\PrestaShop\Core\Grid\GridFactoryInterface;
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\UploadedImageConstraintException;
use PrestaShop\PrestaShop\Core\Search\Filters\LanguageFilters;
use PrestaShop\PrestaShop\Core\Util\Url\UrlFileCheckerInterface;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use PrestaShopBundle\Controller\Attribute\AllShopContext;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use PrestaShopBundle\Security\Attribute\DemoRestricted;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class LanguageController manages "Improve > International > Localization > Languages".
 */
#[AllShopContext]
class LanguageController extends PrestaShopAdminController
{
    /**
     * Show languages listing page.
     *
     * @param Request $request
     * @param LanguageFilters $filters
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function indexAction(
        Request $request,
        LanguageFilters $filters,
        #[Autowire(service: 'prestashop.core.grid.factory.language')]
        GridFactoryInterface $languageGridFactory,
        UrlFileCheckerInterface $urlFileChecker
    ): Response {
        $languageGrid = $languageGridFactory->getGrid($filters);

        return $this->render('@PrestaShop/Admin/Improve/International/Language/index.html.twig', [
            'languageGrid' => $this->presentGrid($languageGrid),
            'isHtaccessFileWriter' => $urlFileChecker->isHtaccessFileWritable(),
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'multistoreInfoTip' => $this->trans(
                'Note that this page is available in all shops context only, this is why your context has just switched.',
                [],
                'Admin.Notifications.Info'
            ),
            'multistoreIsUsed' => $this->getShopContext()->isMultiShopUsed(),
            'enableSidebar' => true,
        ]);
    }

    /**
     * Show language creation form page and handle its submit.
     *
     * @param Request $request
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('create', request.get('_legacy_controller'))")]
    public function createAction(
        Request $request,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.builder.language_form_builder')]
        FormBuilderInterface $languageFormBuilder,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.handler.language_form_handler')]
        FormHandlerInterface $languageFormHandler
    ): Response {
        $languageForm = $languageFormBuilder->getForm();
        $languageForm->handleRequest($request);

        try {
            $result = $languageFormHandler->handle($languageForm);

            if (null !== $result->getIdentifiableObjectId()) {
                $this->addFlash('success', $this->trans('Successful creation', [], 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_languages_index');
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->render('@PrestaShop/Admin/Improve/International/Language/create.html.twig', [
            'languageForm' => $languageForm->createView(),
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'enableSidebar' => true,
            'layoutTitle' => $this->trans('New language', [], 'Admin.Navigation.Menu'),
        ]);
    }

    /**
     * Show language edit form page and handle its submit.
     *
     * @param int $languageId
     * @param Request $request
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute: 'admin_languages_index')]
    public function editAction(
        int $languageId,
        Request $request,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.builder.language_form_builder')]
        FormBuilderInterface $languageFormBuilder,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.handler.language_form_handler')]
        FormHandlerInterface $languageFormHandler
    ): Response {
        try {
            $languageForm = $languageFormBuilder->getFormFor((int) $languageId, [], [
                'is_for_editing' => true,
            ]);
        } catch (Exception $exception) {
            $this->addFlash(
                'error',
                $this->getErrorMessageForException($exception, $this->getErrorMessages())
            );

            return $this->redirectToRoute('admin_languages_index');
        }

        try {
            $languageForm->handleRequest($request);
            $result = $languageFormHandler->handleFor((int) $languageId, $languageForm);

            if ($result->isSubmitted() && $result->isValid()) {
                $this->addFlash(
                    'success',
                    $this->trans('Successful update', [], 'Admin.Notifications.Success')
                );

                return $this->redirectToRoute('admin_languages_index');
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));

            if ($e instanceof LanguageNotFoundException) {
                return $this->redirectToRoute('admin_languages_index');
            }
        }

        return $this->render('@PrestaShop/Admin/Improve/International/Language/edit.html.twig', [
            'languageForm' => $languageForm->createView(),
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'enableSidebar' => true,
            'layoutTitle' => $this->trans(
                'Editing language %name%',
                [
                    '%name%' => $languageForm->get('name')->getData(),
                ],
                'Admin.Navigation.Menu'
            ),
        ]);
    }

    /**
     * Deletes language
     *
     * @param int $languageId
     *
     * @return RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_languages_index')]
    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", redirectRoute: 'admin_languages_index')]
    public function deleteAction(int $languageId): RedirectResponse
    {
        try {
            $this->dispatchCommand(new DeleteLanguageCommand((int) $languageId));

            $this->addFlash('success', $this->trans('Successful deletion', [], 'Admin.Notifications.Success'));
        } catch (LanguageException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_languages_index');
    }

    /**
     * Deletes languages in bulk action
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_languages_index')]
    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", redirectRoute: 'admin_languages_index')]
    public function bulkDeleteAction(Request $request): RedirectResponse
    {
        $languageIds = $this->getBulkLanguagesFromRequest($request);

        try {
            $this->dispatchCommand(new BulkDeleteLanguagesCommand($languageIds));

            $this->addFlash(
                'success',
                $this->trans('The selection has been successfully deleted.', [], 'Admin.Notifications.Success')
            );
        } catch (LanguageException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_languages_index');
    }

    /**
     * Toggles language status
     *
     * @param int $languageId
     *
     * @return RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_languages_index')]
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute: 'admin_languages_index')]
    public function toggleStatusAction(int $languageId): RedirectResponse
    {
        try {
            /** @var EditableLanguage $editableLanguage */
            $editableLanguage = $this->dispatchQuery(new GetLanguageForEditing((int) $languageId));

            $this->dispatchCommand(new ToggleLanguageStatusCommand(
                (int) $languageId,
                !$editableLanguage->isActive()
            ));

            $this->addFlash(
                'success',
                $this->trans('The status has been successfully updated.', [], 'Admin.Notifications.Success')
            );
        } catch (LanguageException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_languages_index');
    }

    /**
     * Toggles languages status in bulk action
     *
     * @param Request $request
     * @param string $status
     *
     * @return RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_languages_index')]
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute: 'admin_languages_index')]
    public function bulkToggleStatusAction(Request $request, string $status): RedirectResponse
    {
        $languageIds = $this->getBulkLanguagesFromRequest($request);
        $expectedStatus = 'enable' === $status;

        try {
            $this->dispatchCommand(new BulkToggleLanguagesStatusCommand(
                $languageIds,
                $expectedStatus
            ));

            $this->addFlash(
                'success',
                $this->trans('The status has been successfully updated.', [], 'Admin.Notifications.Success')
            );
        } catch (LanguageException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_languages_index');
    }

    /**
     * @return array
     */
    private function getErrorMessages()
    {
        $iniConfig = $this->container->get(IniConfiguration::class);

        return [
            LanguageNotFoundException::class => $this->trans(
                'The object cannot be loaded (or found).',
                [],
                'Admin.Notifications.Error'
            ),
            CannotDisableDefaultLanguageException::class => $this->trans(
                'You cannot change the status of the default language.',
                [],
                'Admin.International.Notification'
            ),
            UploadedImageConstraintException::class => [
                UploadedImageConstraintException::EXCEEDED_SIZE => $this->trans(
                    'Max file size allowed is "%s" bytes.',
                    [
                        $iniConfig->getUploadMaxSizeInBytes(),
                    ],
                    'Admin.Notifications.Error'
                ),
                UploadedImageConstraintException::UNRECOGNIZED_FORMAT => $this->trans(
                    'Image format not recognized, allowed formats are: .gif, .jpg, .png',
                    [],
                    'Admin.Notifications.Error'
                ),
            ],
            CopyingNoPictureException::class => [
                CopyingNoPictureException::PRODUCT_IMAGE_COPY_ERROR => $this->trans(
                    'An error occurred while copying "No Picture" image to your product folder.',
                    [],
                    'Admin.International.Notification'
                ),
                CopyingNoPictureException::CATEGORY_IMAGE_COPY_ERROR => $this->trans(
                    'An error occurred while copying "No picture" image to your category folder.',
                    [],
                    'Admin.International.Notification'
                ),
                CopyingNoPictureException::BRAND_IMAGE_COPY_ERROR => $this->trans(
                    'An error occurred while copying "No picture" image to your brand folder.',
                    [],
                    'Admin.International.Notification'
                ),
            ],
            LanguageImageUploadingException::class => [
                LanguageImageUploadingException::MEMORY_LIMIT_RESTRICTION => $this->trans(
                    'Due to memory limit restrictions, this image cannot be loaded. Please increase your memory_limit value via your server\'s configuration settings.',
                    [],
                    'Admin.Notifications.Error'
                ),
                LanguageImageUploadingException::UNEXPECTED_ERROR => $this->trans(
                    'An error occurred while uploading the image.',
                    [],
                    'Admin.Notifications.Error'
                ),
            ],
            LanguageConstraintException::class => [
                LanguageConstraintException::INVALID_ISO_CODE => $this->trans(
                    'The %s field is invalid.',
                    [sprintf('"%s"', $this->trans('ISO code', [], 'Admin.International.Feature'))],
                    'Admin.Notifications.Error'
                ),
                LanguageConstraintException::INVALID_IETF_TAG => $this->trans(
                    'The %s field is invalid.',
                    [sprintf('"%s"', $this->trans('Language code', [], 'Admin.International.Feature'))],
                    'Admin.Notifications.Error'
                ),
                LanguageConstraintException::DUPLICATE_ISO_CODE => $this->trans(
                    'This ISO code is already linked to another language.',
                    [],
                    'Admin.International.Notification'
                ),
                LanguageConstraintException::EMPTY_BULK_DELETE => $this->trans(
                    'You must select at least one element to delete.',
                    [],
                    'Admin.Notifications.Error'
                ),
            ],
            DefaultLanguageException::class => [
                DefaultLanguageException::CANNOT_DELETE_ERROR => $this->trans(
                    'You cannot delete the default language.',
                    [],
                    'Admin.International.Notification'
                ),
                DefaultLanguageException::CANNOT_DISABLE_ERROR => $this->trans(
                    'You cannot change the status of the default language.',
                    [],
                    'Admin.International.Notification'
                ),
                DefaultLanguageException::CANNOT_DELETE_DEFAULT_ERROR => $this->trans(
                    'You cannot delete the default language.',
                    [],
                    'Admin.International.Notification'
                ),
                DefaultLanguageException::CANNOT_DELETE_IN_USE_ERROR => $this->trans(
                    'You cannot delete the language currently in use. Please select a different language.',
                    [],
                    'Admin.International.Notification'
                ),
            ],
        ];
    }

    /**
     * Get language ids from request for bulk action
     *
     * @param Request $request
     *
     * @return int[]
     */
    private function getBulkLanguagesFromRequest(Request $request)
    {
        $languageIds = $request->request->all('language_language_bulk');

        foreach ($languageIds as $i => $languageId) {
            $languageIds[$i] = (int) $languageId;
        }

        return $languageIds;
    }
}
