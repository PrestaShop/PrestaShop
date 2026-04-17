<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Sell\Product\SEO;

use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SerpType extends TranslatorAwareType
{
    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'label' => $this->trans('Here is a preview of your search engine result, play with it!', 'Admin.Catalog.Feature'),
        ]);
    }
}
