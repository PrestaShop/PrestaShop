<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Column\Type\Common;

use PrestaShop\PrestaShop\Core\Grid\Column\AbstractColumn;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DisableableLinkColumn extends AbstractColumn
{
    /**
     * @var LinkColumn
     */
    private $linkColumn;

    public function __construct($id)
    {
        parent::__construct($id);
        $this->linkColumn = new LinkColumn($id);
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'disableable_link';
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired(['disabled_field'])
            ->setAllowedTypes('disabled_field', ['string', 'null'])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setOptions(array $options)
    {
        $disabledOptions = [];

        if (isset($options['disabled_field'])) {
            $disabledOptions['disabled_field'] = $options['disabled_field'];
            unset($options['disabled_field']);
        }

        $this->linkColumn->setOptions($options);
        parent::setOptions($disabledOptions);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions()
    {
        return array_merge($this->linkColumn->getOptions(), parent::getOptions());
    }
}
