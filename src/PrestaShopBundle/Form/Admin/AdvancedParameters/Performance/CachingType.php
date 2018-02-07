<?php
/**
 * 2007-2017 PrestaShop
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShopBundle\Form\Admin\AdvancedParameters\Performance;

use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * This form class generates the "Caching" form in Performance page
 */
class CachingType extends TranslatorAwareType
{
    private $extensionsList = array(
        'CacheMemcache' => array('memcache'),
        'CacheMemcached' => array('memcached'),
        'CacheApc' => array('apc', 'apcu'),
        'CacheXcache' => array('xcache'),
    );

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('use_cache', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', array(
                'choices'  => array(
                    false => 'No',
                    true => 'Yes',
                ),
                'choice_translation_domain' => 'Admin.Global',
            ))
            ->add('caching_system', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', array(
                'choices'  => array(
                    'Memcached via PHP::Memcache' => 'CacheMemcache',
                    'Memcached via PHP::Memcached' => 'CacheMemcached',
                    'APC' => 'CacheApc',
                    'Xcache' => 'CacheXcache',
                ),
                'choice_label' => function($value, $key, $index) {
                    $disabled = false;
                    foreach ($this->extensionsList[$index] as $extensionName) {
                        if (extension_loaded($extensionName)) {
                            break;
                        }
                        $disabled = true;
                    }

                    return $disabled === true ? $this->getErrorsMessages()[$index] : $value;
                },
                'choice_attr' => function($value, $key, $index) {
                    $disabled = false;
                    foreach ($this->extensionsList[$index] as $extensionName) {
                        if (extension_loaded($extensionName)) {
                            break;
                        }
                        $disabled = true;
                    }

                    return $disabled === true ? array('disabled' => $disabled) : array();
                },
                'expanded' => true,
                'choices_as_values' => true,
                'required' => false,
                'placeholder' => false
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'translation_domain' => 'Admin.Advparameters.Feature',
        ));
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
        return array(
            'CacheMemcache' => $this->trans('Memcached via PHP::Memcache', 'Admin.Advparameters.Feature')
                . ' '
                . $this->trans(
                    '(you must install the [a]Memcache PECL extension[/a])',
                    'Admin.Advparameters.Notification',
                    array(
                        '[a]' => '<a href="http://www.php.net/manual/en/memcache.installation.php" target="_blank">',
                        '[/a]' => '</a>',
                    )
                ),
            'CacheMemcached' => $this->trans('Memcached via PHP::Memcached', 'Admin.Advparameters.Feature')
                . ' '
                . $this->trans(
                    '(you must install the [a]Memcached PECL extension[/a])',
                    'Admin.Advparameters.Notification',
                    array(
                        '[a]' => '<a href="http://www.php.net/manual/en/memcached.installation.php" target="_blank">',
                        '[/a]' => '</a>',
                    )
                ),
            'CacheApc' => $this->trans('APC', 'Admin.Advparameters.Feature')
                . ' '
                . $this->trans(
                    '(you must install the [a]APC PECL extension[/a])',
                    'Admin.Advparameters.Notification',
                    array(
                        '[a]' => '<a href="http://www.php.net/manual/en/apc.installation.php" target="_blank">',
                        '[/a]' => '</a>',
                    )
                ),
            'CacheXcache' => $this->trans('Xcache', 'Admin.Advparameters.Feature')
                . ' '
                . $this->trans(
                    '(you must install the [a]Xcache extension[/a])',
                    'Admin.Advparameters.Notification',
                    array(
                        '[a]' => '<a href="http://xcache.lighttpd.net" target="_blank">',
                        '[/a]' => '</a>',
                    )
                ),
        );
    }
}
