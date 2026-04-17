<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Type;

use PrestaShopBundle\Form\Admin\Sell\Customer\SearchedCustomerType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class CustomerSearchType extends EntitySearchInputType
{
    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(
        TranslatorInterface $translator,
        RouterInterface $router
    ) {
        parent::__construct($translator);
        $this->router = $router;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'label' => $this->trans('Apply to all customers', 'Admin.Global'),
            'layout' => EntitySearchInputType::LIST_LAYOUT,
            'entry_type' => SearchedCustomerType::class,
            'allow_delete' => false,
            'limit' => 1,
            'disabling_switch' => true,
            'switch_state_on_disable' => 'on',
            'disabling_switch_event' => null,
            'disabled_value' => function ($data) {
                return empty($data[0]['id_customer']);
            },
            'placeholder' => $this->trans('Search customer', 'Admin.Actions'),
            'suggestion_field' => 'fullname_and_email',
            'required' => false,
            'exclude_guests' => false,
        ]);

        $resolver->setAllowedTypes('exclude_guests', 'bool');

        $resolver->setNormalizer('remote_url', function ($options) {
            return $this->buildSearchUrl($options['exclude_guests']);
        });
    }

    private function buildSearchUrl(bool $excludeGuests): string
    {
        $params = [
            'customer_search' => '__QUERY__',
            'exclude_guests' => (int) $excludeGuests,
        ];

        return $this->router->generate('admin_customers_search', $params);
    }
}
