<?php
/**
 * 2007-2018 PrestaShop
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
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Form\Admin\Improve\International\Localization;

use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Localization\Pack\Loader\LocalizationPackLoaderInterface;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class ImportLocalizationPackType
 */
class ImportLocalizationPackType extends TranslatorAwareType
{
    /**
     * @var LocalizationPackLoaderInterface
     */
    private $remoteLocalizationPackLoader;

    /**
     * @var LocalizationPackLoaderInterface
     */
    private $localLocalizationPackLoader;

    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param LocalizationPackLoaderInterface $remoteLocalizationPackLoader
     * @param LocalizationPackLoaderInterface $localLocalizationPackLoader
     * @param ConfigurationInterface $configuration
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        LocalizationPackLoaderInterface $remoteLocalizationPackLoader,
        LocalizationPackLoaderInterface $localLocalizationPackLoader,
        ConfigurationInterface $configuration
    ) {
        parent::__construct($translator, $locales);

        $this->remoteLocalizationPackLoader = $remoteLocalizationPackLoader;
        $this->localLocalizationPackLoader = $localLocalizationPackLoader;
        $this->configuration = $configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('iso_localization_pack', ChoiceType::class, [
                'choices' =>  $this->getLocalizationPackChoices(),
                'attr' => [
                    'data-toggle' => 'select2',
                    'data-minimumResultsForSearch' => '7',
                ],
            ])
            ->add('content_to_import', ChoiceType::class, [
                'expanded' => true,
                'multiple' => true,
                'choices' => [
                    $this->trans('States', 'Admin.International.Feature') => 'states',
                    $this->trans('Taxes', 'Admin.Global') => 'taxes',
                    $this->trans('Currencies', 'Admin.Global') => 'currencies',
                    $this->trans('Languages', 'Admin.Global') => 'languages',
                    $this->trans('Units (e.g. weight, volume, distance)', 'Admin.International.Feature') => 'units',
                    $this->trans('Change the behavior of the price display for groups', 'Admin.International.Feature') => 'groups',
                ],
                'data' => [
                    'states',
                    'taxes',
                    'currencies',
                    'languages',
                    'units',
                ],
            ])
            ->add('download_pack_data', SwitchType::class, [
                'data' => 1,
            ])
        ;
    }

    /**
     * Get available localization packs as choices
     *
     * @return array
     */
    private function getLocalizationPackChoices()
    {
        $localizationPacks = $this->remoteLocalizationPackLoader->getLocalizationPackList();
        if (null === $localizationPacks) {
            $localizationPacks = $this->localLocalizationPackLoader->getLocalizationPackList();
        }

        $choices = [];

        if ($localizationPacks) {
            foreach ($localizationPacks as $pack) {
                $iso = (string) $pack->iso;
                $name = (string) $pack->name;

                $choices[$name] = $iso;
            }
        }

        $rootDir = $this->configuration->get('_PS_ROOT_DIR_');

        $finder = (new Finder())
            ->files()
            ->depth('1')
            ->in($rootDir.'/localization')
            ->name('/^([a-z]{2})\.xml$/');

        foreach ($finder as $file) {
            list($iso) = explode('.', $file->getFilename());

            if (!in_array($iso, $choices)) {
                $pack = simplexml_load_file($file->getPathname());
                $name = $this->trans(
                    '%s (local)',
                    'Admin.International.Feature'
                    [
                        (string) $pack['name']
                    ]
                );

                $choices[$name] = $iso;
            }
        }

        // sort choices alphabetically
        ksort($choices);

        return $choices;
    }
}
