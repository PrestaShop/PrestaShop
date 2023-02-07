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

namespace PrestaShop\PrestaShop\Core\Grid\Filter;

use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Exception\OrderReturnException;
use PrestaShop\PrestaShop\Core\Grid\Definition\GridDefinitionInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class OrderReturnProductsFilterFormFactory decorates original filter factory to add custom create action.
 */
final class OrderReturnProductsFilterFormFactory implements GridFilterFormFactoryInterface
{
    /**
     * @var GridFilterFormFactoryInterface
     */
    private $formFactory;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @param GridFilterFormFactoryInterface $formFactory
     * @param UrlGeneratorInterface $urlGenerator
     * @param RequestStack $requestStack
     */
    public function __construct(
        GridFilterFormFactoryInterface $formFactory,
        UrlGeneratorInterface $urlGenerator,
        RequestStack $requestStack
    ) {
        $this->formFactory = $formFactory;
        $this->urlGenerator = $urlGenerator;
        $this->requestStack = $requestStack;
    }

    /**
     * {@inheritdoc}
     */
    public function create(GridDefinitionInterface $definition)
    {
        $orderReturnProductsFilterForm = $this->formFactory->create($definition);

        $newOrderReturnFormBuilder = $orderReturnProductsFilterForm->getConfig()->getFormFactory()->createNamedBuilder(
            $definition->getId()
        );

        /* @var FormInterface $orderReturnFormItem */
        foreach ($orderReturnProductsFilterForm as $orderReturnProductsFormItem) {
            $orderReturnProductsFilterForm->add(
                $orderReturnProductsFormItem->getName(),
                get_class($orderReturnProductsFormItem->getConfig()->getType()->getInnerType()),
                $orderReturnProductsFormItem->getConfig()->getOptions()
            );
        }

        $queryParams = [];

        if (null === ($request = $this->requestStack->getCurrentRequest())
            || !$request->attributes->has('orderReturnId')
        ) {
            throw new OrderReturnException('This page needs to have orderReturnId as a parameter');
        }

        $queryParams['orderReturnId'] = $request->attributes->get('orderReturnId');

        $newOrderReturnFormBuilder->setAction(
            $this->urlGenerator->generate('admin_order_returns_products_filter', $queryParams)
        );

        return $newOrderReturnFormBuilder->getForm();
    }
}
