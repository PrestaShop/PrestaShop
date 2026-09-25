<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\AdvancedParameters\Performance;

use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Cache\Adapter\ApcuAdapter;
use Symfony\Component\Cache\Adapter\MemcachedAdapter;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * This form class generates the "Caching" form in Performance page.
 */
class CachingType extends TranslatorAwareType
{
    /**
     * WHY: Xcache used to be offered here and is not any more. Its last release supports PHP 5.6,
     * there has never been a build for a version this release runs on, and the option was shown with
     * a link telling the merchant to go and install it. `classes/cache/CacheXcache.php` stays, so a
     * shop that somehow has it configured keeps working; it is only no longer proposed.
     *
     * The two Memcached entries are not a duplicate: `memcache` and `memcached` are two different
     * client extensions for the same server, both installable today, and the extension is what the
     * label names.
     */
    private $extensionsList = [
        'CacheMemcache' => ['memcache'],
        'CacheMemcached' => ['memcached'],
        'CacheApc' => ['apc', 'apcu'],
    ];

    /**
     * Extensions the Symfony container caches through, mapped to the adapter that uses them.
     *
     * WHY: a loaded extension is not a usable one - these adapters also require
     * memcached >= 3.1.6 and apc.enabled=1, and throw when that is not met. Offering such an
     * option enabled lets a merchant select a caching system that then breaks every page.
     * Extensions with no entry here back no Symfony adapter, so loading them is all we can test.
     */
    private $adapters = [
        'memcached' => MemcachedAdapter::class,
        'apcu' => ApcuAdapter::class,
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
                    'Memcached via the memcache extension' => 'CacheMemcache',
                    'Memcached via the memcached extension' => 'CacheMemcached',
                    'APC' => 'CacheApc',
                ],
                'choice_label' => function ($value, $key, $index) {
                    return $this->isAvailable($index) ? $value : $this->getErrorsMessages()[$index];
                },
                'choice_attr' => function ($value, $key, $index) {
                    return $this->isAvailable($index) ? [] : ['disabled' => true];
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
     * @param string $cachingSystem one of the keys of $extensionsList
     *
     * @return bool whether the shop can actually cache through that system
     */
    private function isAvailable($cachingSystem)
    {
        foreach ($this->extensionsList[$cachingSystem] as $extensionName) {
            $adapter = $this->adapters[$extensionName] ?? null;
            $available = null === $adapter ? extension_loaded($extensionName) : $adapter::isSupported();

            if ($available) {
                return true;
            }
        }

        return false;
    }

    /**
     * If extensions are unavailable, option message should be completed with installation instructions.
     *
     * @return array
     */
    private function getErrorsMessages()
    {
        return [
            'CacheMemcache' => $this->trans('Memcached via the memcache extension', 'Admin.Advparameters.Feature')
                . ' '
                . $this->trans(
                    '(you must install the [a]Memcache PECL extension[/a])',
                    'Admin.Advparameters.Notification',
                    [
                        '[a]' => '<a href="https://www.php.net/manual/en/memcache.installation.php" class="ml-1" target="_blank">',
                        '[/a]' => '</a>',
                    ]
                ),
            'CacheMemcached' => $this->trans('Memcached via the memcached extension', 'Admin.Advparameters.Feature')
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
                    '(you must install the [a]APCu PECL extension[/a])',
                    'Admin.Advparameters.Notification',
                    [
                        '[a]' => '<a href="https://www.php.net/manual/en/apcu.installation.php" class="ml-1" target="_blank">',
                        '[/a]' => '</a>',
                    ]
                ),
        ];
    }
}
