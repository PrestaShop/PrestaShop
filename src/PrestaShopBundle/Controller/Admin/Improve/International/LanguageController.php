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

namespace PrestaShopBundle\Controller\Admin\Improve\International;

use PrestaShop\PrestaShop\Core\Search\Filters\LanguageFilters;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController as AbstractAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
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
        ]);
    }

    /**
     * Show language creation form page and handle its submit.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function createAction(Request $request)
    {
        $languageFormBuilder = $this->get('prestashop.core.form.identifiable_object.builder.language_form_builder');

        $languageForm = $languageFormBuilder->getForm();
        $languageForm->handleRequest($request);

        return $this->render('@PrestaShop/Admin/Improve/International/Language/create.html.twig', [
            'languageForm' => $languageForm->createView(),
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
    public function editAction($languageId, Request $request)
    {
        return $this->redirect(
            $this->getAdminLink($request->attributes->get('_legacy_controller'), [
                'addlang' => 1,
                'id_lang' => $languageId,
            ])
        );
    }
}
