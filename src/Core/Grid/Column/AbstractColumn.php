<?php

namespace PrestaShop\PrestaShop\Core\Grid\Column;

use PrestaShop\PrestaShop\Core\Grid\Column\ColumnFilterOption;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class AbtractColumn implements reusable column methods
 */
abstract class AbstractColumn implements ColumnInterface
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    private $options = [];

    /**
     * @param string $id
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
    public function setOptions(array $options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'filter' => new ColumnFilterOption(TextType::class, [
                    'required' => false,
                ]),
                'sortable' => true,
            ])
            ->setAllowedTypes('filter', ColumnFilterOption::class)
            ->setAllowedTypes('sortable', 'bool')
        ;
    }
}
