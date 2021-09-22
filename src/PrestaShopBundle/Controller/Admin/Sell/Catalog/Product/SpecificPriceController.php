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
declare(strict_types=1);

namespace PrestaShopBundle\Controller\Admin\Sell\Catalog\Product;

use DateTime;
use DateTimeInterface;
use Exception;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\Exception\SpecificPriceConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\Query\GetSpecificPriceList;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\QueryResult\SpecificPriceListForEditing;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Reduction;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Builder\FormBuilderInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Handler\FormHandlerInterface;
use PrestaShop\PrestaShop\Core\Util\DateTime\DateTime as DateTimeUtil;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SpecificPriceController extends FrameworkBundleAdminController
{
    public function listAction(int $productId): JsonResponse
    {
        $specificPricesList = $this->getQueryBus()->handle(
            new GetSpecificPriceList($productId, $this->getContextLangId())
        );

        return $this->json(['specificPrices' => $this->formatSpecificPricesList($specificPricesList)]);
    }

    public function createAction(Request $request, int $productId): Response
    {
        $form = $this->getFormBuilder()->getForm(['product_id' => $productId]);
        $form->handleRequest($request);

        try {
            $result = $this->getFormHandler()->handle($form);

            if ($result->isSubmitted() && $result->isValid()) {
                $this->addFlash('success', $this->trans('Creation successful', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_products_specific_prices_edit', [
                    'liteDisplaying' => $request->query->has('liteDisplaying'),
                    'specificPriceId' => $result->getIdentifiableObjectId(),
                ]);
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->render('@PrestaShop/Admin/Sell/Catalog/Product/SpecificPrice/create.html.twig', [
            'specificPriceForm' => $form->createView(),
        ]);
    }

    public function editAction(Request $request, int $specificPriceId): Response
    {
        $form = $this->getFormBuilder()->getFormFor($specificPriceId);
        $form->handleRequest($request);

        try {
            $result = $this->getFormHandler()->handle($form);

            if ($result->isSubmitted() && $result->isValid()) {
                $this->addFlash('success', $this->trans('Update successful', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_products_specific_prices_edit', [
                    'liteDisplaying' => $request->query->has('liteDisplaying'),
                ]);
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->render('@PrestaShop/Admin/Sell/Catalog/Product/SpecificPrice/edit.html.twig', [
            'specificPriceForm' => $form->createView(),
            'specificPriceId' => $specificPriceId,
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
            ],
        ];
    }

    private function getFormBuilder(): FormBuilderInterface
    {
        return $this->get('prestashop.core.form.identifiable_object.builder.specific_price_form_builder');
    }

    private function getFormHandler(): FormHandlerInterface
    {
        return $this->get('prestashop.core.form.identifiable_object.handler.specific_price_form_handler');
    }

    private function formatSpecificPricesList(SpecificPriceListForEditing $specificPriceListForEditing): array
    {
        $list = [];
        foreach ($specificPriceListForEditing->getSpecificPrices() as $specificPrice) {
            $list[] = [
                'id' => $specificPrice->getSpecificPriceId(),
                'combination' => $specificPrice->getCombinationName() ?: $this->getUnspecifiedValueFormat(),
                'currency' => $specificPrice->getCurrency() ?? $this->trans('All currencies', 'Admin.Global'),
                'country' => $specificPrice->getCountry() ?? $this->trans('All countries', 'Admin.Global'),
                'group' => $specificPrice->getGroup() ?? $this->trans('All groups', 'Admin.Global'),
                'customer' => $specificPrice->getCustomer() ?? $this->trans('All customers', 'Admin.Global'),
                'price' => $this->getContextLocale()->formatPrice((string) $specificPrice->getPrice(), $this->getContextCurrencyIso()),
                'impact' => $this->formatImpact($specificPrice->getReductionType(), $specificPrice->getReductionValue()),
                'period' => $this->formatPeriod($specificPrice->getDateTimeFrom(), $specificPrice->getDateTimeFrom()),
                'fromQuantity' => $specificPrice->getFromQuantity()
            ];
        }

        return $list;
    }

    private function formatImpact(string $reductionType, DecimalNumber $reductionValue): string
    {
        if ($reductionValue->equalsZero()) {
            return $this->getUnspecifiedValueFormat();
        }

        //@todo: locale seems to have smth like negative percentage/price specifications, but have no idea how to use it??
        $locale = $this->getContextLocale();
        if ($reductionType === Reduction::TYPE_AMOUNT) {
            return sprintf('-%s', $locale->formatPrice((string) $reductionValue, $this->getContextCurrencyIso()));
        }

        return sprintf( '-%s %%', (string) $reductionValue);
    }

    private function formatPeriod(DateTimeInterface $from, DateTimeInterface $to): ?array
    {
        if (DateTimeUtil::isNull($from) && DateTimeUtil::isNull($to)) {
            return null;
        }

        return [
            'from' => DateTimeUtil::isNull($from) ?
                $this->trans('Unlimited', 'Admin.Global') :
                $from->format(DateTimeUtil::DEFAULT_DATETIME_FORMAT),
            'to' => DateTimeUtil::isNull($to) ?
                $this->trans('Unlimited', 'Admin.Global') :
                $to->format(DateTimeUtil::DEFAULT_DATETIME_FORMAT),
        ];
    }

    private function getUnspecifiedValueFormat(): string
    {
        return '--';
    }
}
