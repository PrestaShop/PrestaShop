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

namespace PrestaShopBundle\Controller\Admin\Configure\ShopParameters;

use Doctrine\ORM\EntityManager;
use PrestaShop\PrestaShop\Adapter\Tools;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Entity\Repository\TabRepository;
use PrestaShopBundle\Entity\Tab;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use PrestaShopBundle\Security\Voter\PageVoter;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Responsible of "Configure > Shop Parameters > General" page.
 */
class PreferencesController extends FrameworkBundleAdminController
{
    const CONTROLLER_NAME = 'AdminPreferences';

    /**
     * @param Request            $request
     * @param FormInterface|null $form
     *
     * @Template("@PrestaShop/Admin/Configure/ShopParameters/preferences.html.twig")
     *
     * @return array
     *
     * @throws \LogicException
     */
    public function indexAction(Request $request, FormInterface $form = null)
    {
        if (is_null($form)) {
            $form = $this->get('prestashop.adapter.preferences.form_handler')->getForm();
        }

        /** @var Tools $toolsAdapter */
        $toolsAdapter = $this->get('prestashop.adapter.tools');

        // SSL URI is used for the merchant to check if he has SSL enabled
        $sslUri = 'https://'.$toolsAdapter->getShopDomainSsl().$request->getRequestUri();

        return array(
            'layoutHeaderToolbarBtn' => array(),
            'layoutTitle' => $this->get('translator')->trans('Preferences', array(), 'Admin.Navigation.Menu'),
            'requireAddonsSearch' => true,
            'requireBulkActions' => false,
            'showContentHeader' => true,
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink('AdminPreferences'),
            'requireFilterStatus' => false,
            'form' => $form->createView(),
            'isSslEnabled' => $this->configuration->get('PS_SSL_ENABLED'),
            'sslUri' => $sslUri,
        );
    }

    /**
     * Process the form.
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \LogicException
     */
    public function processFormAction(Request $request)
    {
        if ($this->isDemoModeEnabled()) {
            $this->addFlash('error', $this->getDemoModeErrorMessage());

            return $this->redirectToRoute('admin_preferences');
        }

        if (!in_array(
            $this->authorizationLevel($this::CONTROLLER_NAME),
            array(
                PageVoter::LEVEL_READ,
                PageVoter::LEVEL_UPDATE,
                PageVoter::LEVEL_CREATE,
                PageVoter::LEVEL_DELETE,
            )
        )) {
            $this->addFlash('error', $this->trans('You do not have permission to update this.', 'Admin.Notifications.Error'));

            return $this->redirectToRoute('admin_preferences');
        }

        $this->dispatchHook('actionAdminPreferencesControllerPostProcessBefore', array('controller' => $this));

        /** @var FormInterface $form */
        $form = $this->get('prestashop.adapter.preferences.form_handler')->getForm();
        $form->handleRequest($request);

        if (!$form->isSubmitted()) {
            return $this->redirectToRoute('admin_preferences');
        }

        $data = $form->getData();
        $saveErrors = $this->get('prestashop.adapter.preferences.form_handler')->save($data);

        if (0 === count($saveErrors)) {
            /** @var EntityManager $em */
            $em = $this->get('doctrine.orm.entity_manager');

            /** @var TabRepository $tabRepository */
            $tabRepository = $em->getRepository(Tab::class);

            $tabRepository->changeStatusByClassName(
                'AdminShopGroup',
                (bool) $this->configuration->get('PS_MULTISHOP_FEATURE_ACTIVE')
            );

            $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));

            return $this->redirectToRoute('admin_preferences');
        }

        $this->flashErrors($saveErrors);

        return $this->redirectToRoute('admin_preferences');
    }
}
