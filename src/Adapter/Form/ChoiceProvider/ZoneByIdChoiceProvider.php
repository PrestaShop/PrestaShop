<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Core\Form\ConfigurableFormChoiceProviderInterface;
use PrestaShop\PrestaShop\Core\Form\FormChoiceFormatter;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;
use Zone;

/**
 * This class will provide data from DB / ORM about Zone
 */
class ZoneByIdChoiceProvider implements ConfigurableFormChoiceProviderInterface
{
    public function __construct(
        private readonly ?TranslatorInterface $translator = null,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function getChoices(array $options): array
    {
        $options = $this->resolveOptions($options);
        $zones = Zone::getZones($options['active'], $options['active_first']);

        if ($options['mark_inactive']) {
            $inactiveLabel = $this->translator?->trans('Inactive', [], 'Admin.Global') ?? 'Inactive';
            foreach ($zones as &$zone) {
                if (!$zone['active']) {
                    $zone['name'] .= ' (' . $inactiveLabel . ')';
                }
            }
            unset($zone);
        }

        return FormChoiceFormatter::formatFormChoices($zones, 'id_zone', 'name');
    }

    private function resolveOptions(array $options): array
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            'active' => false,
            'active_first' => false,
            'mark_inactive' => false,
        ]);
        $resolver->setAllowedTypes('active', 'bool');
        $resolver->setAllowedTypes('active_first', 'bool');
        $resolver->setAllowedTypes('mark_inactive', 'bool');

        return $resolver->resolve($options);
    }
}
