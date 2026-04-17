<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Action;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ModalOptions used to configure modal used in actions like SubmitBulkAction
 */
class ModalOptions
{
    /**
     * @var array
     */
    private $options;

    /**
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->setOptions($options);
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param array $options
     *
     * @return ModalOptions
     */
    public function setOptions(array $options): ModalOptions
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);

        $this->options = $resolver->resolve($options);

        return $this;
    }

    /**
     * @param OptionsResolver $resolver
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'title' => null,
                'confirm_button_label' => null,
                'confirm_button_class' => 'btn-primary',
                'close_button_label' => null,
            ])
            ->setAllowedTypes('title', ['string', 'null'])
            ->setAllowedTypes('confirm_button_label', ['string', 'null'])
            ->setAllowedTypes('confirm_button_class', 'string')
        ;
    }
}
