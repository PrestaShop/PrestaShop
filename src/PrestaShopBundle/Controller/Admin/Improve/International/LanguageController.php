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

use PrestaShop\PrestaShop\Core\Domain\Language\Command\BulkDeleteLanguagesCommand;
use PrestaShop\PrestaShop\Core\Domain\Language\Command\DeleteLanguageCommand;
use PrestaShop\PrestaShop\Core\Domain\Language\Exception\CannotDisableDefaultLanguageException;
use PrestaShop\PrestaShop\Core\Domain\Language\Exception\CopyingNoPictureException;
use PrestaShop\PrestaShop\Core\Domain\Language\Exception\DefaultLanguageException;
use PrestaShop\PrestaShop\Core\Domain\Language\Exception\LanguageConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Language\Exception\LanguageException;
use PrestaShop\PrestaShop\Core\Domain\Language\Exception\LanguageImageUploadingException;
use PrestaShop\PrestaShop\Core\Domain\Language\Exception\LanguageNotFoundException;
use PrestaShop\PrestaShop\Core\Search\Filters\LanguageFilters;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController as AbstractAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Security\Annotation\DemoRestricted;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class LanguageController manages "Improve > International > Localization > Languages".
 */
class LanguageController extends AbstractAdminController
{
    /**
     * Show languages listing page.
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     *
     * @param LanguageFilters $filters
     *
     * @return Response
     */
    public function indexAction(LanguageFilters $filters)
    {
        $languageGridFactory = $this->get('prestashop.core.grid.factory.language');
        $languageGrid = $languageGridFactory->getGrid($filters);

        return $this->render('@PrestaShop/Admin/Improve/International/Language/index.html.twig', [
            'languageGrid' => $this->presentGrid($languageGrid),
            'isHtaccessFileWriter' => $this->get('prestashop.core.util.url.url_file_checker')->isHtaccessFileWritable(),
        ]);
    }

    /**
     * Show language creation form page and handle its submit.
     *
     * @AdminSecurity("is_granted('create', request.get('_legacy_controller'))")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function createAction(Request $request)
    {
        $languageFormHandler = $this->get('prestashop.core.form.identifiable_object.handler.language_form_handler');
        $languageFormBuilder = $this->get('prestashop.core.form.identifiable_object.builder.language_form_builder');

        $languageForm = $languageFormBuilder->getForm();
        $languageForm->handleRequest($request);

        try {
            $result = $languageFormHandler->handle($languageForm);

            if (null !== $result->getIdentifiableObjectId()) {
                $this->addFlash('success', $this->trans('Successful creation.', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_languages_index');
            }
        } catch (LanguageException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->render('@PrestaShop/Admin/Improve/International/Language/create.html.twig', [
            'languageForm' => $languageForm->createView(),
        ]);
    }

    /**
     * Show language edit form page and handle its submit.
     *
     * @AdminSecurity("is_granted('update', request.get('_legacy_controller'))")
     *
     * @param int $languageId
     * @param Request $request
     *
     * @return Response
     */
    public function editAction($languageId, Request $request)
    {
        $languageFormHandler = $this->get('prestashop.core.form.identifiable_object.handler.language_form_handler');
        $languageFormBuilder = $this->get('prestashop.core.form.identifiable_object.builder.language_form_builder');

        try {
            $languageForm = $languageFormBuilder->getFormFor((int) $languageId, [], [
                'is_for_editing' => true,
            ]);
            $languageForm->handleRequest($request);

            $result = $languageFormHandler->handleFor((int) $languageId, $languageForm);

            if (null !== $result->getIdentifiableObjectId()) {
                $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_languages_index');
            }
        } catch (LanguageException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));

            if ($e instanceof LanguageNotFoundException) {
                return $this->redirectToRoute('admin_languages_index');
            }
        }

        return $this->render('@PrestaShop/Admin/Improve/International/Language/edit.html.twig', [
            'languageForm' => $languageForm->createView(),
        ]);
    }

    /**
     * Deletes language
     *
     * @AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", redirectRoute="admin_languages_index")
     * @DemoRestricted(redirectRoute="admin_languages_index")
     *
     * @param int $languageId
     *
     * @return RedirectResponse
     */
    public function deleteAction($languageId)
    {
        try {
            $this->getCommandBus()->handle(new DeleteLanguageCommand((int) $languageId));

            $this->addFlash('success', $this->trans('Successful deletion.', 'Admin.Notifications.Success'));
        } catch (LanguageException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
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
    public function bulkDeleteAction(Request $request)
    {
        $languageIds = $this->getBulkLanguagesFromRequest($request);

        try {
            $this->getCommandBus()->handle(new BulkDeleteLanguagesCommand($languageIds));

            $this->addFlash(
                'success',
                $this->trans('The selection has been successfully deleted.', 'Admin.Notifications.Success')
            );
        } catch (LanguageException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->redirectToRoute('admin_languages_index');
    }

    /**
     * @param LanguageException $e
     *
     * @return array
     */
    private function getErrorMessages(LanguageException $e)
    {
        return [
            LanguageNotFoundException::class => $this->trans(
                'The object cannot be loaded (or found)',
                'Admin.Notifications.Error'
            ),
            CannotDisableDefaultLanguageException::class => $this->trans(
                'You cannot change the status of the default language.',
                'Admin.International.Notification'
            ),
            CopyingNoPictureException::class => [
                CopyingNoPictureException::PRODUCT_IMAGE_COPY_ERROR => $this->trans(
                    'An error occurred while copying "No Picture" image to your product folder.',
                    'Admin.International.Notification'
                ),
                CopyingNoPictureException::CATEGORY_IMAGE_COPY_ERROR => $this->trans(
                    'An error occurred while copying "No picture" image to your category folder.',
                    'Admin.International.Notification'
                ),
                CopyingNoPictureException::BRAND_IMAGE_COPY_ERROR => $this->trans(
                    'An error occurred while copying "No picture" image to your brand folder.',
                    'Admin.International.Notification'
                ),
            ],
            LanguageImageUploadingException::class => [
                LanguageImageUploadingException::MEMORY_LIMIT_RESTRICTION => $this->trans(
                    'Due to memory limit restrictions, this image cannot be loaded. Please increase your memory_limit value via your server\'s configuration settings. ',
                    'Admin.Notifications.Error'
                ),
                LanguageImageUploadingException::UNEXPECTED_ERROR => $this->trans(
                    'An error occurred while uploading the image.',
                    'Admin.Notifications.Error'
                ),
            ],
            LanguageConstraintException::class => [
                LanguageConstraintException::INVALID_ISO_CODE => $this->trans(
                    'The %s field is invalid.',
                    'Admin.Notifications.Error',
                    [sprintf('"%s"', $this->trans('ISO code', 'Admin.International.Feature'))]
                ),
                LanguageConstraintException::INVALID_IETF_TAG => $this->trans(
                    'The %s field is invalid.',
                    'Admin.Notifications.Error',
                    [sprintf('"%s"', $this->trans('Language code', 'Admin.International.Feature'))]
                ),
                LanguageConstraintException::DUPLICATE_ISO_CODE => $this->trans(
                    'This ISO code is already linked to another language.',
                    'Admin.International.Notification'
                ),
                LanguageConstraintException::EMPTY_BULK_DELETE =>  $this->trans(
                    'You must select at least one element to delete.',
                    'Admin.Notifications.Error'
                ),
            ],
            DefaultLanguageException::class => [
                DefaultLanguageException::CANNOT_DELETE_ERROR => $this->trans(
                    'You cannot delete the default language.',
                    'Admin.International.Notification'
                ),
                DefaultLanguageException::CANNOT_DISABLE_ERROR => $this->trans(
                    'You cannot change the status of the default language.',
                    'Admin.International.Notification'
                ),
                DefaultLanguageException::CANNOT_DELETE_IN_USE_ERROR => $this->trans(
                    'You cannot delete the language currently in use. Please select a different language.',
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
        $languageIds = $request->request->get('language_language_bulk');

        if (!is_array($languageIds)) {
            return [];
        }

        foreach ($languageIds as $i => $languageId) {
            $languageIds[$i] = (int) $languageId;
        }

        return $languageIds;
    }
}
