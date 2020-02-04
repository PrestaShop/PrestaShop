<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
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
                        '[a]' => '<a href="http://www.php.net/manual/en/memcache.installation.php" target="_blank">',
                        '[/a]' => '</a>',
                    ]
                ),
            'CacheMemcached' => $this->trans('Memcached via PHP::Memcached', 'Admin.Advparameters.Feature')
                . ' '
                . $this->trans(
                    '(you must install the [a]Memcached PECL extension[/a])',
                    'Admin.Advparameters.Notification',
                    [
                        '[a]' => '<a href="http://www.php.net/manual/en/memcached.installation.php" target="_blank">',
                        '[/a]' => '</a>',
                    ]
                ),
            'CacheApc' => $this->trans('APC', 'Admin.Advparameters.Feature')
                . ' '
                . $this->trans(
                    '(you must install the [a]APC PECL extension[/a])',
                    'Admin.Advparameters.Notification',
                    [
                        '[a]' => '<a href="http://www.php.net/manual/en/apc.installation.php" target="_blank">',
                        '[/a]' => '</a>',
                    ]
                ),
            'CacheXcache' => $this->trans('Xcache', 'Admin.Advparameters.Feature')
                . ' '
                . $this->trans(
                    '(you must install the [a]Xcache extension[/a])',
                    'Admin.Advparameters.Notification',
                    [
                        '[a]' => '<a href="http://xcache.lighttpd.net" target="_blank">',
                        '[/a]' => '</a>',
                    ]
                ),
        ];
    }
}
