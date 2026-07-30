<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Controller\Admin\Sell\BusinessEntity;

use Exception;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Exception\BusinessEntityBillingAddressConstraintException;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Exception\UnableToCreateBusinessEntityAddress;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Builder\FormBuilderInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Handler\FormHandlerInterface;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use PrestaShopBundle\Form\Admin\Sell\BusinessEntity\BusinessEntityAddressType;
use PrestaShopBundle\Form\Admin\Sell\BusinessEntity\BusinessEntityType;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class BusinessEntitiesController manages the "Sell > Business Entities" page.
 */
class BusinessEntitiesController extends PrestaShopAdminController
{
    #[AdminSecurity("is_granted('read', 'AdminBusinessEntities')")]
    public function listAction(): Response
    {
        return $this->render('@PrestaShop/Admin/Sell/BusinessEntity/list.html.twig', [
            'layoutHeaderToolbarBtn' => $this->getBusinessEntitiesToolbarButtons(),
        ]);
    }

    #[AdminSecurity("is_granted('create', 'AdminBusinessEntities')", message: 'You do not have permission to create this.', redirectRoute: 'admin_business_entities_list')]
    public function createAction(
        Request $request,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.builder.business_entity_form_builder')]
        FormBuilderInterface $formBuilder,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.business_entity_form_handler')]
        FormHandlerInterface $formHandler,
        #[Autowire(expression: 'service("prestashop.adapter.legacy.context").getContext().country.id')]
        int $defaultCountryId
    ): Response {
        $submittedData = $request->request->all('business_entity');

        // Pre-fill each submitted address with a country so the State choices can be rebuilt
        // for validation (defaults to the shop country when none was submitted).
        $formData = [];
        foreach ([BusinessEntityType::BILLING_ADDRESS_TYPE, BusinessEntityType::SHIPPING_ADDRESS_TYPE] as $addressType) {
            foreach ($submittedData[$addressType] ?? [] as $index => $address) {
                $formData[$addressType][$index][BusinessEntityAddressType::FIELD_COUNTRY_ID] =
                    $address[BusinessEntityAddressType::FIELD_COUNTRY_ID] ?? $defaultCountryId;
            }
        }

        $form = $formBuilder->getForm($formData);

        $form->handleRequest($request);

        try {
            $result = $formHandler->handle($form);
            if (null !== $result->getIdentifiableObjectId()) {
                $this->addFlash(
                    'success',
                    $this->trans('Business entity successfully created.', [], 'Admin.Notifications.Success')
                );

                return $this->redirectToRoute('admin_business_entities_list');
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->render(
            '@PrestaShop/Admin/Sell/BusinessEntity/create.html.twig',
            [
                'layoutTitle' => $this->trans('New business entity', [], 'Admin.Navigation.Menu'),
                'businessEntityForm' => $form->createView(),
                'enableSidebar' => true,
                'help_link' => $this->generateSidebarLink('AdminBusinessEntities'),
            ]
        );
    }

    protected function getBusinessEntitiesToolbarButtons(): array
    {
        $toolbarButtons = [];

        $toolbarButtons['add'] = [
            'href' => $this->generateUrl('admin_business_entities_create'),
            'desc' => $this->trans('Add new business entity', [], 'Admin.Navigation.Menu'),
            'icon' => 'add_circle_outline',
        ];

        return $toolbarButtons;
    }

    private function getErrorMessages(): array
    {
        return [
            UnableToCreateBusinessEntityAddress::class => $this->trans(
                'An error occurred while creating the business entity.',
                [],
                'Admin.Notifications.Error'
            ),
            BusinessEntityBillingAddressConstraintException::class => [
                BusinessEntityBillingAddressConstraintException::MISSING_BILLING_ADDRESS => $this->trans(
                    'At least one billing address is required if you want to use default billing address as shipping address.',
                    [],
                    'Admin.Notifications.Error'
                ),
                BusinessEntityBillingAddressConstraintException::MISSING_SHIPPING_ADDRESS => $this->trans(
                    'At least one shipping address is required if you don\'t want to use default billing address as shipping address.',
                    [],
                    'Admin.Notifications.Error'
                ),
                BusinessEntityBillingAddressConstraintException::MISSING_DEFAULT_BILLING_ADDRESS => $this->trans(
                    'You must have one default billing address',
                    [],
                    'Admin.Notifications.Error'
                ),
                BusinessEntityBillingAddressConstraintException::MISSING_DEFAULT_SHIPPING_ADDRESS => $this->trans(
                    'You must have one default shipping address',
                    [],
                    'Admin.Notifications.Error'
                ),
                BusinessEntityBillingAddressConstraintException::MULTIPLE_DEFAULT_BILLING_ADDRESSES => $this->trans(
                    'You must have only one default billing address',
                    [],
                    'Admin.Notifications.Error'
                ),
                BusinessEntityBillingAddressConstraintException::MULTIPLE_DEFAULT_SHIPPING_ADDRESSES => $this->trans(
                    'You must have only one default shipping address',
                    [],
                    'Admin.Notifications.Error'
                ),
            ],
        ];
    }
}
