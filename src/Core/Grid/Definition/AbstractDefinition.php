<?php

namespace PrestaShop\PrestaShop\Core\Grid\Definition;

use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractDefinition implements DefinitionInterface
{
    /**
     * @var string  Unique grid identifier
     */
    private $id;

    /**
     * @var string  Grid name
     */
    private $name;

    /**
     * @var ColumnInterface[]
     */
    private $columns;

    /**
     * @var array
     */
    private $options = [];

    /**
     * @param string $id   Unique grid identifier (used as table ID when rendering table)
     */
    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
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
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * {@inheritdoc}
     */
    public function setColumns(ColumnCollectionInterface $columns)
    {
        $this->columns = $columns;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions()
    {
        return $this->options;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
    }
}
