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

use Symfony\Component\Form\Extension\Core\Type\HiddenType;

/**
 * This allows to store a filter parameter in a hidden input which can be prefilled
 * to provide some context or updated dynamically via js for filter component outside
 * the grid headers filters.
 */
class HiddenFilter implements FilterInterface
{
    /**
     * @var array Filter type options
     */
    private $typeOptions = [];

    /**
     * @var string Filter name
     */
    private $name;

    /**
     * @var string|null Column ID if filter is associated with columns
     */
    private $column;

    /**
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return HiddenType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function setTypeOptions(array $filterTypeOptions)
    {
        $this->typeOptions = $filterTypeOptions;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTypeOptions()
    {
        return $this->typeOptions;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setAssociatedColumn($columnId)
    {
        $this->column = $columnId;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAssociatedColumn()
    {
        return $this->column;
    }
}
