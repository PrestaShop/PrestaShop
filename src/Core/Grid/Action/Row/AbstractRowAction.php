<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Action\Row;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class AbstractRowAction.
 */
abstract class AbstractRowAction implements RowActionInterface
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
     * @var array|null
     */
    private $options;

    /**
     * @var string
     */
    private $icon;

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
    public function getIcon()
    {
        return $this->icon;
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
    public function setIcon($icon)
    {
        $this->icon = $icon;

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
     * {@inheritdoc}
     */
    public function isApplicable(array $record)
    {
        return true;
    }

    /**
     * Default action options configuration. You can override it if options are needed.
     *
     * @param OptionsResolver $resolver
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'use_inline_display' => false,
            ])
            // if set to true then it displays only icons
            ->setAllowedTypes('use_inline_display', 'bool')
        ;
    }

    /**
     * Resolve action options.
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
