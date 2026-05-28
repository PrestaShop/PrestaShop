<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Controller\Admin\Configure\ShopParameters;

use Exception;
use PrestaShop\PrestaShop\Core\Domain\Search\Command\SearchIndexationCommand;
use PrestaShop\PrestaShop\Core\Domain\Search\Query\GetIndexedProductsCount;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Form\FormHandlerInterface;
use PrestaShop\PrestaShop\Core\Util\Url\UrlCleaner;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Controller responsible for "Configure > Shop Parameters > Search > Preferences" page.
 */
class SearchConfigurationController extends PrestaShopAdminController
{
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function preferencesIndexAction(
        #[Autowire(service: 'prestashop.admin.search_preferences.indexation.form_handler')]
        FormHandlerInterface $indexationFormHandler,
        #[Autowire(service: 'prestashop.admin.search_preferences.search_options.form_handler')]
        FormHandlerInterface $searchOptionsFormHandler,
        #[Autowire(service: 'prestashop.admin.search_preferences.weight.form_handler')]
        FormHandlerInterface $weightFormHandler,
        #[Autowire(param: 'cookie_key')]
        string $cookieKey,
    ): Response {
        $indexedCount = $this->dispatchQuery(new GetIndexedProductsCount());
        $cronToken = substr($cookieKey, 34, 8);
        $cronUrl = UrlCleaner::cleanUrl(
            $this->generateUrl('admin_search_indexation_cron', ['token' => $cronToken], UrlGeneratorInterface::ABSOLUTE_URL),
            ['_token']
        );

        return $this->render('@PrestaShop/Admin/Configure/ShopParameters/Search/preferences.html.twig', [
            'help_link' => $this->generateSidebarLink('AdminSearchConf'),
            'indexationForm' => $indexationFormHandler->getForm()->createView(),
            'searchOptionsForm' => $searchOptionsFormHandler->getForm()->createView(),
            'weightForm' => $weightFormHandler->getForm()->createView(),
            'indexedProductsCount' => $indexedCount->getIndexed(),
            'totalProductsCount' => $indexedCount->getTotal(),
            'cronUrl' => $cronUrl,
        ]);
    }

    #[AdminSecurity("is_granted('update', request.get('_legacy_controller')) && is_granted('create', request.get('_legacy_controller')) && is_granted('delete', request.get('_legacy_controller'))", message: 'You do not have permission to update this.', redirectRoute: 'admin_search_preferences_index')]
    public function processIndexationFormAction(
        Request $request,
        #[Autowire(service: 'prestashop.admin.search_preferences.indexation.form_handler')]
        FormHandlerInterface $formHandler,
    ): RedirectResponse {
        return $this->processForm($request, $formHandler, 'Indexation');
    }

    #[AdminSecurity("is_granted('update', request.get('_legacy_controller')) && is_granted('create', request.get('_legacy_controller')) && is_granted('delete', request.get('_legacy_controller'))", message: 'You do not have permission to update this.', redirectRoute: 'admin_search_preferences_index')]
    public function processSearchOptionsFormAction(
        Request $request,
        #[Autowire(service: 'prestashop.admin.search_preferences.search_options.form_handler')]
        FormHandlerInterface $formHandler,
    ): RedirectResponse {
        return $this->processForm($request, $formHandler, 'SearchOptions');
    }

    #[AdminSecurity("is_granted('update', request.get('_legacy_controller')) && is_granted('create', request.get('_legacy_controller')) && is_granted('delete', request.get('_legacy_controller'))", message: 'You do not have permission to update this.', redirectRoute: 'admin_search_preferences_index')]
    public function processWeightFormAction(
        Request $request,
        #[Autowire(service: 'prestashop.admin.search_preferences.weight.form_handler')]
        FormHandlerInterface $formHandler,
    ): RedirectResponse {
        return $this->processForm($request, $formHandler, 'Weight');
    }

    #[AdminSecurity("is_granted('update', request.get('_legacy_controller')) && is_granted('create', request.get('_legacy_controller')) && is_granted('delete', request.get('_legacy_controller'))", message: 'You do not have permission to update this.', redirectRoute: 'admin_search_preferences_index')]
    public function addMissingToIndexAction(): RedirectResponse
    {
        try {
            $this->dispatchCommand(new SearchIndexationCommand(false));
            $this->addFlash('success', $this->trans('The index was successfully updated.', [], 'Admin.Shopparameters.Notification'));
        } catch (Exception) {
            $this->addFlash('error', $this->trans('An error occurred while indexing products.', [], 'Admin.Shopparameters.Notification'));
        }

        return $this->redirectToRoute('admin_search_preferences_index');
    }

    #[AdminSecurity("is_granted('update', request.get('_legacy_controller')) && is_granted('create', request.get('_legacy_controller')) && is_granted('delete', request.get('_legacy_controller'))", message: 'You do not have permission to update this.', redirectRoute: 'admin_search_preferences_index')]
    public function rebuildIndexAction(): RedirectResponse
    {
        try {
            $this->dispatchCommand(new SearchIndexationCommand(true));
            $this->addFlash('success', $this->trans('The index was successfully rebuilt.', [], 'Admin.Shopparameters.Notification'));
        } catch (Exception) {
            $this->addFlash('error', $this->trans('An error occurred while rebuilding the index.', [], 'Admin.Shopparameters.Notification'));
        }

        return $this->redirectToRoute('admin_search_preferences_index');
    }

    public function cronIndexationAction(
        Request $request,
        #[Autowire(param: 'cookie_key')]
        string $cookieKey,
    ): Response {
        $expectedToken = substr($cookieKey, 34, 8);
        if ($request->query->get('token') !== $expectedToken) {
            return new Response('Forbidden', Response::HTTP_FORBIDDEN);
        }

        $idShop = $request->query->getInt('id_shop');
        $shopConstraint = $idShop > 0 ? ShopConstraint::shop($idShop) : ShopConstraint::allShops();
        $full = (bool) $request->query->get('full', false);

        ini_set('max_execution_time', '7200');

        try {
            $this->dispatchCommand(new SearchIndexationCommand($full, $shopConstraint));
        } catch (Exception $e) {
            return new Response('Indexation failed: ' . $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new Response('OK');
    }

    private function processForm(Request $request, FormHandlerInterface $formHandler, string $hookName): RedirectResponse
    {
        $this->dispatchHookWithParameters(
            'actionAdminShopParametersSearchPreferencesControllerPostProcess' . $hookName . 'Before',
            ['controller' => $this]
        );

        $form = $formHandler->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $data = $form->getData();
            $saveErrors = $formHandler->save($data);

            if (0 === count($saveErrors)) {
                $this->addFlash('success', $this->trans('Update successful', [], 'Admin.Notifications.Success'));
            } else {
                $this->addFlashErrors($saveErrors);
            }
        }

        return $this->redirectToRoute('admin_search_preferences_index');
    }
}
