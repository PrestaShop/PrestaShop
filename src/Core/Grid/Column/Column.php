<?php
/**
 * 2007-2018 PrestaShop
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

namespace PrestaShop\PrestaShop\Core\Grid\Column;

use Symfony\Component\Form\FormTypeInterface;

/**
 * Class Column is responsible for defining single column in row
 */
final class Column implements ColumnInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $identifier;

    /**
     * @var callable|null
     */
    private $modifier;

    /**
     * @var string
     */
    private $filterFormType;

    /**
     * @var array
     */
    private $filterFormTypeOptions = [];

    /**
     * @var bool
     */
    private $isSortable = true;

    /**
     * @var bool True if column's content must be raw (not escaped) or False otherwise
     */
    private $isRawContent = false;

    /**
     * @param string $identifier Unique column identifier
     * @param string $name Translated column name
     * @param $filterFormType
     * @param array $filterFormTypeOptions
     */
    public function __construct($identifier, $name, $filterFormType = null, array $filterFormTypeOptions = [])
    {
        $this->name = $name;
        $this->identifier = $identifier;
        $this->filterFormType = $filterFormType;

        if (null !== $filterFormType) {
            $this->setFilterFormType($filterFormType, $filterFormTypeOptions);
        }
    }

    /**
     * @param string $formType
     * @param array $options
     *
     * @return $this
     */
    public function setFilterFormType($formType, array $options = [])
    {
        if (!in_array(FormTypeInterface::class, class_implements($formType))) {
            throw new \InvalidArgumentException(sprintf(
                'Could not load type "%s": class does not implement %s',
                $formType,
                FormTypeInterface::class
            ));
        }

        $this->filterFormType = $formType;
        $this->filterFormTypeOptions = $options;

        return $this;
    }

    /**
     * @param bool $isSortable
     *
     * @return $this
     */
    public function setSortable($isSortable)
    {
        $this->isSortable = $isSortable;

        return $this;
    }

    /**
     * @param bool $isRawContent
     *
     * @return $this
     */
    public function setRawContent($isRawContent)
    {
        $this->isRawContent = (bool) $isRawContent;

        return $this;
    }

    /**
     * @param callable $modifier
     *
     * @return Column
     */
    public function setModifier(callable $modifier)
    {
        $this->modifier = $modifier;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return $this->identifier;
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
    public function getModifier()
    {
        return $this->modifier;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilterFormType()
    {
        return $this->filterFormType;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilterFormTypeOptions()
    {
        return $this->filterFormTypeOptions;
    }

    /**
     * {@inheritdoc}
     */
    public function isSortable()
    {
        return $this->isSortable;
    }

    /**
     * {@inheritdoc}
     */
    public function isFilterable()
    {
        // if form type is set for column
        // then column is filterable
        return null !== $this->getFilterFormType();
    }

    /**
     * {@inheritdoc}
     */
    public function isRawContent()
    {
        return $this->isRawContent;
    }
}
