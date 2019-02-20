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

use PrestaShop\PrestaShop\Core\Domain\Tax\Exception\TaxException;
use PrestaShop\PrestaShop\Core\Domain\Tax\Exception\TaxNotFoundException;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Responsible for handling "Improve > International > Taxes" page.
 */
class TaxController extends FrameworkBundleAdminController
{
    public function indexAction()
    {
        //@todo: get rid after rebase
        dump('implement index');
        die;
    }

    /**
     * Handles tax edit
     *
     * @param Request $request
     * @param $taxId
     *
     * @return Response
     */
    public function editAction(Request $request, $taxId)
    {
        $taxFormHandler = $this->get('prestashop.core.form.identifiable_object.handler.tax_form_handler');
        $taxFormBuilder = $this->get('prestashop.core.form.identifiable_object.builder.tax_form_builder');

        $taxForm = $taxFormBuilder->getFormFor((int) $taxId);
        $taxForm->handleRequest($request);

        try {
            $result = $taxFormHandler->handleFor((int) $taxId, $taxForm);

            if (null !== $result->getIdentifiableObjectId()) {
                $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_taxes_index');
            }
        } catch (TaxException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));

            if ($e instanceof TaxNotFoundException) {
                return $this->redirectToRoute('admin_taxes_index');
            }
        }

        return $this->render('@PrestaShop/Admin/Improve/International/Tax/edit.html.twig', [
            'taxForm' => $taxForm->createView(),
        ]);
    }

    /**
     * Gets error messages for exceptions
     *
     * @return array
     */
    private function getErrorMessages()
    {
        return [
            TaxNotFoundException::class => $this->trans(
                'The object cannot be loaded (or found)',
                'Admin.Notifications.Error'
            ),
        ];
    }
}
