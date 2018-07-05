<?php

namespace PrestaShop\PrestaShop\Core\Grid\Column;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class AbtractColumn implements reusable column methods.
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
    private $options;

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
        $this->resolveOptions($options);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions()
    {
        if (null === $this->options) {
            $this->resolveOptions();
        }

        return $this->options;
    }

    /**
     * Default column options configuration. You can override or extend it needed options.
     *
     * @param OptionsResolver $resolver
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'filter_type' => TextType::class,
                'filter_type_options' => [
                    'required' => false,
                ],
                'sortable' => true,
            ])
            ->setAllowedTypes('filter_type', ['string', 'null'])
            ->setAllowedTypes('filter_type_options', 'array')
            ->setAllowedTypes('sortable', 'bool')
        ;
    }

    /**
     * Resolve column options.
     *
     * @param array $options
     */
    private function resolveOptions(array $options = [])
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);

        $this->options = $resolver->resolve($options);
    }
}
