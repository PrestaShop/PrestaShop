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

namespace PrestaShopBundle\Form\Partial;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Form\ResolvedFormType;
use Symfony\Component\Form\ResolvedFormTypeInterface;

/**
 * ResolvedPartialFormType is the class responsible for building a FormBuilder we need it
 * to use PartialFormBuilder which will itself create a PartialForm. In order to use this
 * class we need to override the ResolvedFormTypeFactory service from Symfony.
 *
 * @see ResolvedPartialFormTypeFactory
 */
class ResolvedPartialFormType extends ResolvedFormType
{
    /**
     * @var bool
     */
    private $isPartialFormType;

    /**
     * @param FormTypeInterface $innerType
     * @param array $typeExtensions
     * @param ResolvedFormTypeInterface|null $parent
     */
    public function __construct(FormTypeInterface $innerType, array $typeExtensions = [], ResolvedFormTypeInterface $parent = null)
    {
        parent::__construct($innerType, $typeExtensions, $parent);

        $this->isPartialFormType = $innerType instanceof PartialFormTypeInterface;
    }

    /**
     * {@inheritDoc}
     */
    protected function newBuilder($name, $dataClass, FormFactoryInterface $factory, array $options)
    {
        if ($this->isPartialFormType) {
            return new PartialFormBuilder($name, $dataClass, new EventDispatcher(), $factory, $options);
        }

        return parent::newBuilder($name, $dataClass, $factory, $options);
    }
}
