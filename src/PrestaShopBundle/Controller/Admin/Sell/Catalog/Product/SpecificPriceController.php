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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
declare(strict_types=1);

namespace PrestaShopBundle\Controller\Admin\Sell\Catalog\Product;

use DateTimeInterface;
use Exception;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\Command\DeleteSpecificPriceCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\Exception\SpecificPriceConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\Query\GetSpecificPriceList;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\QueryResult\SpecificPriceList;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\ValueObject\FixedPriceInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\ValueObject\InitialPrice;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Reduction;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Builder\FormBuilderInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Handler\FormHandlerInterface;
use PrestaShop\PrestaShop\Core\Util\DateTime\DateTime as DateTimeUtil;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SpecificPriceController extends FrameworkBundleAdminController
{
    private const UNSPECIFIED_VALUE_FORMAT = '--';

    /**
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     */
    public function listAction(Request $request, int $productId): JsonResponse
    {
        /** @var SpecificPriceList $specificPricesList */
        $specificPricesList = $this->getQueryBus()->handle(
            new GetSpecificPriceList(
                $productId,
                $this->getContextLangId(),
                $request->query->getInt('limit') ?: null,
                $request->query->getInt('offset') ?: null,
                // Show only specific prices for current context shop or All shops
                ['shopIds' => [0, $this->getContextShopId()]]
            )
        );

        return $this->json([
            'specificPrices' => $this->formatSpecificPricesList($specificPricesList),
            'total' => $specificPricesList->getTotalSpecificPricesCount(),
        ]);
    }

    /**
     * @AdminSecurity("is_granted('create', request.get('_legacy_controller'))")
     *
     * @return Response
     */
    public function createAction(Request $request, int $productId): Response
    {
        $form = $this->getFormBuilder()->getForm(['product_id' => $productId]);
        $form->handleRequest($request);

        try {
            $result = $this->getFormHandler()->handle($form);

            if ($result->isSubmitted() && $result->isValid()) {
                $this->addFlash('success', $this->trans('Successful creation', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_products_specific_prices_edit', [
                    'liteDisplaying' => $request->query->has('liteDisplaying'),
                    // This action is only used inside a dedicated modal so we always enforce the lite display in the redirection url
                    'specificPriceId' => $result->getIdentifiableObjectId(),
                ]);
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->render('@PrestaShop/Admin/Sell/Catalog/Product/SpecificPrice/create.html.twig', [
            'specificPriceForm' => $form->createView(),
            'liteDisplaying' => $request->query->has('liteDisplaying'),
        ]);
    }

    /**
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     *
     * @return Response
     */
    public function editAction(Request $request, int $specificPriceId): Response
    {
        $form = $this->getFormBuilder()->getFormFor($specificPriceId);
        $form->handleRequest($request);

        try {
            $result = $this->getFormHandler()->handleFor($specificPriceId, $form);

            if ($result->isSubmitted() && $result->isValid()) {
                $this->addFlash('success', $this->trans('Update successful', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_products_specific_prices_edit', [
                    'specificPriceId' => $specificPriceId,
                    'liteDisplaying' => $request->query->has('liteDisplaying'),
                ]);
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->render('@PrestaShop/Admin/Sell/Catalog/Product/SpecificPrice/edit.html.twig', [
            'specificPriceForm' => $form->createView(),
            'liteDisplaying' => $request->query->has('liteDisplaying'),
        ]);
    }

    /**
     * @AdminSecurity("is_granted('delete', request.get('_legacy_controller'))")
     *
     * @param Request $request
     * @param int $specificPriceId
     *
     * @return JsonResponse
     */
    public function deleteAction(Request $request, int $specificPriceId): JsonResponse
    {
        try {
            $this->getCommandBus()->handle(new DeleteSpecificPriceCommand($specificPriceId));
        } catch (Exception $e) {
            return $this->json([
                'error' => $this->getErrorMessageForException($e, $this->getErrorMessages()),
            ], Response::HTTP_BAD_REQUEST);
        }

        return $this->json([
            'message' => $this->trans('Successful deletion', 'Admin.Notifications.Success'),
        ]);
    }

    /**
     * Provides error messages for exceptions
     *
     * @return array<string, mixed>
     */
    private function getErrorMessages(): array
    {
        return [
            SpecificPriceConstraintException::class => [
                SpecificPriceConstraintException::NOT_UNIQUE_PER_PRODUCT => $this->trans(
                    'A specific price already exists for these parameters.',
                    'Admin.Catalog.Notification'
                ),
                SpecificPriceConstraintException::REDUCTION_OR_PRICE_MUST_BE_SET => $this->trans(
                    sprintf(
                        '%s or %s must be set',
                        $this->trans('Retail price (tax excl.)', 'Admin.Catalog.Feature'),
                        $this->trans('Reduction', 'Admin.Catalog.Feature')
                    ),
                    'Admin.Catalog.Notification'
                ),
            ],
        ];
    }

    /**
     * @return FormBuilderInterface
     */
    private function getFormBuilder(): FormBuilderInterface
    {
        return $this->get('prestashop.core.form.identifiable_object.builder.specific_price_form_builder');
    }

    /**
     * @return FormHandlerInterface
     */
    private function getFormHandler(): FormHandlerInterface
    {
        return $this->get('prestashop.core.form.identifiable_object.handler.specific_price_form_handler');
    }

    /**
     * @param SpecificPriceList $specificPriceListForEditing
     *
     * @return array<int, array<string, mixed>>
     */
    private function formatSpecificPricesList(SpecificPriceList $specificPriceListForEditing): array
    {
        $list = [];
        foreach ($specificPriceListForEditing->getSpecificPrices() as $specificPrice) {
            $list[] = [
                'id' => $specificPrice->getSpecificPriceId(),
                'combination' => $specificPrice->getCombinationName() ?: self::UNSPECIFIED_VALUE_FORMAT,
                'currency' => $specificPrice->getCurrencyName() ?? $this->trans('All currencies', 'Admin.Global'),
                'country' => $specificPrice->getCountryName() ?? $this->trans('All countries', 'Admin.Global'),
                'group' => $specificPrice->getGroupName() ?? $this->trans('All groups', 'Admin.Global'),
                'shop' => $specificPrice->getShopName() ?? $this->trans('All stores', 'Admin.Global'),
                'customer' => $specificPrice->getCustomerName() ?? $this->trans('All customers', 'Admin.Global'),
                'price' => $this->formatPrice(
                    $specificPrice->getFixedPrice(),
                    $specificPrice->getCurrencyISOCode() ?: $this->getContextCurrencyIso()
                ),
                'impact' => $this->formatImpact(
                    $specificPrice->getReductionType(),
                    $specificPrice->getReductionValue(),
                    $specificPrice->getCurrencyISOCode() ?: $this->getContextCurrencyIso()
                ),
                'period' => $this->formatPeriod($specificPrice->getDateTimeFrom(), $specificPrice->getDateTimeTo()),
                'fromQuantity' => $specificPrice->getFromQuantity(),
            ];
        }

        return $list;
    }

    /**
     * @param FixedPriceInterface $fixedPrice
     * @param string $currencyIsoCode
     *
     * @return string
     */
    private function formatPrice(FixedPriceInterface $fixedPrice, string $currencyIsoCode): string
    {
        if (InitialPrice::isInitialPriceValue((string) $fixedPrice->getValue())) {
            return self::UNSPECIFIED_VALUE_FORMAT;
        }

        return $this->getContextLocale()->formatPrice((string) $fixedPrice->getValue(), $currencyIsoCode);
    }

    /**
     * @param string $reductionType
     * @param DecimalNumber $reductionValue
     * @param string $currencyIsoCode
     *
     * @return string
     */
    private function formatImpact(string $reductionType, DecimalNumber $reductionValue, string $currencyIsoCode): string
    {
        if ($reductionValue->equalsZero()) {
            return self::UNSPECIFIED_VALUE_FORMAT;
        }

        $reductionValue = $reductionValue->toNegative();

        $locale = $this->getContextLocale();
        if ($reductionType === Reduction::TYPE_AMOUNT) {
            return sprintf('%s', $locale->formatPrice((string) $reductionValue, $currencyIsoCode));
        }

        return sprintf('%s %%', (string) $reductionValue);
    }

    /**
     * @param DateTimeInterface $from
     * @param DateTimeInterface $to
     *
     * @return array<string, string>|null
     */
    private function formatPeriod(DateTimeInterface $from, DateTimeInterface $to): ?array
    {
        if (DateTimeUtil::isNull($from) && DateTimeUtil::isNull($to)) {
            return null;
        }

        return [
            'from' => DateTimeUtil::isNull($from) ?
                $this->trans('Always', 'Admin.Global') :
                $from->format(DateTimeUtil::DEFAULT_DATETIME_FORMAT),
            'to' => DateTimeUtil::isNull($to) ?
                $this->trans('Always', 'Admin.Global') :
                $to->format(DateTimeUtil::DEFAULT_DATETIME_FORMAT),
        ];
    }
}
