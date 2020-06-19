<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Filter;

use PrestaShop\PrestaShop\Core\Domain\MerchandiseReturn\Exception\MerchandiseReturnException;
use PrestaShop\PrestaShop\Core\Grid\Definition\GridDefinitionInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class CategoryFilterFormFactory decorates original filter factory to add custom submit action.
 */
final class MerchandiseReturnProductsFilterFormFactory implements GridFilterFormFactoryInterface
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
        $merchandiseReturnFilterForm = $this->formFactory->create($definition);

        $newMerchandiseReturnFormBuilder = $merchandiseReturnFilterForm->getConfig()->getFormFactory()->createNamedBuilder(
            $definition->getId(),
            FormType::class
        );

        /* @var FormInterface $categoryFormItem */
        foreach ($merchandiseReturnFilterForm as $merchandiseReturnFormItem) {
            $newMerchandiseReturnFormBuilder->add(
                $merchandiseReturnFormItem->getName(),
                get_class($merchandiseReturnFormItem->getConfig()->getType()->getInnerType()),
                $merchandiseReturnFormItem->getConfig()->getOptions()
            );
        }

        $queryParams = [];

        if (null === ($request = $this->requestStack->getCurrentRequest())
            || !$request->attributes->has('merchandiseReturnId')
        ) {
            throw new MerchandiseReturnException('This page needs to have merchandiseReturnId as a parameter');
        }

        $queryParams['merchandiseReturnId'] = $request->attributes->get('merchandiseReturnId');


        $newMerchandiseReturnFormBuilder->setAction(
            $this->urlGenerator->generate('admin_merchandise_returns_products_filter', $queryParams)
        );

        return $newMerchandiseReturnFormBuilder->getForm();
    }
}
