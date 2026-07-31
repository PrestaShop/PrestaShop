<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Controller\Admin\Sell\BusinessEntity;

use Exception;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Exception\BusinessEntityBillingAddressConstraintException;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Exception\BusinessEntityConstraintException;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Exception\BusinessEntityException;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Exception\BusinessEntityNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Exception\UnableToCreateBusinessEntityAddress;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Query\GetBusinessEntityForViewing;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Query\GetPendingBusinessEntitiesCount;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\QueryResult\BusinessEntityForViewing;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Builder\FormBuilderInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Handler\FormHandlerInterface;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\BusinessEntityGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Grid\GridFactoryInterface;
use PrestaShop\PrestaShop\Core\Search\Filters\BusinessEntityFilters;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use PrestaShopBundle\Entity\Enum\BusinessEntityStatus;
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
    /**
     * Lists the business entities the employee is allowed to see, scoped to the current shop context.
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function listAction(
        Request $request,
        BusinessEntityFilters $filters,
        #[Autowire(service: 'prestashop.core.grid.factory.business_entity')]
        GridFactoryInterface $businessEntityGridFactory,
    ): Response {
        $pendingCount = $this->dispatchQuery(new GetPendingBusinessEntitiesCount());

        $currentStatusFilter = $filters->getFilters()['status'] ?? null;
        $isPendingFilter = ($currentStatusFilter === BusinessEntityStatus::PENDING->value);

        $pendingUrl = $this->generateUrl('admin_business_entities_list', [
            BusinessEntityGridDefinitionFactory::GRID_ID => [
                'filters' => ['status' => BusinessEntityStatus::PENDING->value],
            ],
        ]);

        return $this->render(
            '@PrestaShop/Admin/Sell/BusinessEntity/list.html.twig',
            [
                'enableSidebar' => true,
                'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
                'layoutTitle' => $this->trans('Business entities', [], 'Admin.Navigation.Menu'),
                'layoutHeaderToolbarBtn' => $this->getBusinessEntitiesToolbarButtons(),
                'businessEntityGrid' => $this->presentGrid($businessEntityGridFactory->getGrid($filters)),
                'pendingCount' => $pendingCount,
                'pendingUrl' => $pendingUrl,
                'isPendingFilter' => $isPendingFilter,
            ]
        );
    }

    /**
     * Shows a single business entity in read-only. An entity belonging to another shop is reported
     * as not found rather than as an access error, so the listing does not leak its existence.
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))", redirectRoute: 'admin_business_entities_list')]
    public function viewAction(
        Request $request,
        int $businessEntityId,
    ): Response {
        try {
            /** @var BusinessEntityForViewing $businessEntityForViewing */
            $businessEntityForViewing = $this->dispatchQuery(
                new GetBusinessEntityForViewing($businessEntityId)
            );
        } catch (BusinessEntityException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));

            return $this->redirectToRoute('admin_business_entities_list');
        }

        return $this->render(
            '@PrestaShop/Admin/Sell/BusinessEntity/view.html.twig',
            [
                'enableSidebar' => true,
                'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
                'layoutTitle' => $this->trans(
                    'Business entity %name%',
                    [
                        '%name%' => $businessEntityForViewing->getName(),
                    ],
                    'Admin.Navigation.Menu'
                ),
                'businessEntity' => $businessEntityForViewing,
            ]
        );
    }

    #[AdminSecurity("is_granted('create', request.get('_legacy_controller'))", message: 'You do not have permission to create this.', redirectRoute: 'admin_business_entities_list')]
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
                'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            ]
        );
    }

    private function getBusinessEntitiesToolbarButtons(): array
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
            BusinessEntityNotFoundException::class => $this->trans(
                'The object cannot be loaded (or found).',
                [],
                'Admin.Notifications.Error'
            ),
            BusinessEntityConstraintException::class => [
                BusinessEntityConstraintException::INVALID_ID => $this->trans(
                    'The object cannot be loaded (the identifier is missing or invalid)',
                    [],
                    'Admin.Notifications.Error'
                ),
            ],
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
