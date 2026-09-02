<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\AdvancedParameters\Performance;

use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * This form class generates the "Caching" form in Performance page.
 */
class CachingType extends TranslatorAwareType
{
    private $extensionsList = [
        'CacheMemcache' => ['memcache'],
        'CacheMemcached' => ['memcached'],
        'CacheApc' => ['apc', 'apcu'],
        'CacheXcache' => ['xcache'],
    ];

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('use_cache', SwitchType::class, [
                'label' => $this->trans('Use cache', 'Admin.Advparameters.Feature'),
            ])
            ->add('caching_system', ChoiceType::class, [
                'label' => $this->trans('Caching system', 'Admin.Advparameters.Feature'),
                'choices' => [
                    'Memcached via PHP::Memcache' => 'CacheMemcache',
                    'Memcached via PHP::Memcached' => 'CacheMemcached',
                    'APC' => 'CacheApc',
                    'Xcache' => 'CacheXcache',
                ],
                'choice_label' => function ($value, $key, $index) {
                    $disabled = false;
                    foreach ($this->extensionsList[$index] as $extensionName) {
                        if (extension_loaded($extensionName)) {
                            $disabled = false;

                            break;
                        }
                        $disabled = true;
                    }

                    return $disabled === true ? $this->getErrorsMessages()[$index] : $value;
                },
                'choice_attr' => function ($value, $key, $index) {
                    $disabled = false;
                    foreach ($this->extensionsList[$index] as $extensionName) {
                        if (extension_loaded($extensionName)) {
                            $disabled = false;

                            break;
                        }
                        $disabled = true;
                    }

                    return $disabled === true ? ['disabled' => $disabled] : [];
                },
                'expanded' => true,
                'required' => false,
                'placeholder' => false,
                'row_attr' => [
                    'class' => 'memcache',
                ],
                'choice_translation_domain' => 'Admin.Advparameters.Feature',
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'performance_caching_block';
    }

    /**
     * If extensions are unavailable, option message should be completed with installation instructions.
     *
     * @return array
     */
    private function getErrorsMessages()
    {
        return [
            'CacheMemcache' => $this->trans('Memcached via PHP::Memcache', 'Admin.Advparameters.Feature')
                . ' '
                . $this->trans(
                    '(you must install the [a]Memcache PECL extension[/a])',
                    'Admin.Advparameters.Notification',
                    [
                        '[a]' => '<a href="https://www.php.net/manual/en/memcache.installation.php" class="ml-1" target="_blank">',
                        '[/a]' => '</a>',
                    ]
                ),
            'CacheMemcached' => $this->trans('Memcached via PHP::Memcached', 'Admin.Advparameters.Feature')
                . ' '
                . $this->trans(
                    '(you must install the [a]Memcached PECL extension[/a])',
                    'Admin.Advparameters.Notification',
                    [
                        '[a]' => '<a href="https://www.php.net/manual/en/memcached.installation.php" class="ml-1" target="_blank">',
                        '[/a]' => '</a>',
                    ]
                ),
            'CacheApc' => $this->trans('APC', 'Admin.Advparameters.Feature')
                . ' '
                . $this->trans(
                    '(you must install the [a]APC PECL extension[/a])',
                    'Admin.Advparameters.Notification',
                    [
                        '[a]' => '<a href="https://www.php.net/manual/en/apcu.installation.php" class="ml-1" target="_blank">',
                        '[/a]' => '</a>',
                    ]
                ),
            'CacheXcache' => $this->trans('Xcache', 'Admin.Advparameters.Feature')
                . ' '
                . $this->trans(
                    '(you must install the [a]Xcache extension[/a])',
                    'Admin.Advparameters.Notification',
                    [
                        '[a]' => '<a href="https://github.com/lighttpd/xcache" class="ml-1" target="_blank">',
                        '[/a]' => '</a>',
                    ]
                ),
        ];
    }
}
