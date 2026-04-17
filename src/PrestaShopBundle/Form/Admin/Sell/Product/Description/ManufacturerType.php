<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Sell\Product\Description;

use PrestaShop\PrestaShop\Core\Domain\Manufacturer\ValueObject\NoManufacturerId;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class ManufacturerType extends AbstractType
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var FormChoiceProviderInterface
     */
    private $manufacturerChoiceProvider;

    /**
     * @param TranslatorInterface $translator
     * @param FormChoiceProviderInterface $manufacturerChoiceProvider
     */
    public function __construct(
        TranslatorInterface $translator,
        FormChoiceProviderInterface $manufacturerChoiceProvider
    ) {
        $this->translator = $translator;
        $this->manufacturerChoiceProvider = $manufacturerChoiceProvider;
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $manufacturers = $this->manufacturerChoiceProvider->getChoices();
        $choices = [
            $this->trans('No brand', 'Admin.Catalog.Feature') => NoManufacturerId::NO_MANUFACTURER_ID,
        ] + $manufacturers;

        $resolver->setDefaults([
            'label' => $this->trans('Brand', 'Admin.Catalog.Feature'),
            'label_tag_name' => 'h3',
            'required' => false,
            // placeholder false is important to avoid empty option in select input despite required being false
            'placeholder' => false,
            'choices' => $choices,
            'autocomplete' => true,
        ]);
    }

    /**
     * @param string $key
     * @param string $domain
     * @param array $parameters
     *
     * @return string
     */
    protected function trans(string $key, string $domain, array $parameters = []): string
    {
        return $this->translator->trans($key, $parameters, $domain);
    }
}
