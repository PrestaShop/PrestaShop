<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Column;

use Symfony\Component\OptionsResolver\Exception\NoSuchOptionException;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class AbstractColumn implements reusable column methods.
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
        $this->name = '';
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
     * {@inheritdoc}
     */
    public function getOption(string $name)
    {
        if (array_key_exists($name, $this->options)) {
            return $this->options[$name];
        }

        throw new NoSuchOptionException(sprintf('Option "%s" does not exist in "%s"', $name, static::class));
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
                'sortable' => true,
                'clickable' => false,
                'alignment' => 'left',
                'attr' => [],
            ])
            ->setAllowedTypes('sortable', 'bool')
            ->setAllowedTypes('clickable', 'bool')
            ->setAllowedTypes('alignment', 'string')
            ->setAllowedValues('alignment', ['center', 'left', 'right', 'justify'])
            ->setAllowedTypes('attr', 'array')
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
