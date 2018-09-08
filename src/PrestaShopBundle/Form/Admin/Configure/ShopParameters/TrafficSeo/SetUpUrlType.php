<?php
/**
 * 2007-2018 PrestaShop.
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

namespace PrestaShopBundle\Form\Admin\Configure\ShopParameters\TrafficSeo;

use PrestaShop\PrestaShop\Core\File\FileFinderInterface;
use PrestaShop\PrestaShop\Core\Util\Url\UrlFileCheckerInterface;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class SetUpUrlType is responsible for providing form fields for Set up urls block located in
 * Shop parameters -> Traffic & Seo -> Seo & urls page.
 */
class SetUpUrlType extends AbstractType
{
    /**
     * @var array
     */
    private $canonicalUrlChoices;

    /**
     * @var FileFinderInterface
     */
    private $htaccessFinder;
    /**
     * @var UrlFileCheckerInterface
     */
    private $htaccessFileChecker;

    /**
     * SetUpUrlType constructor.
     *
     * @param array $canonicalUrlChoices
     * @param FileFinderInterface $htaccessFinder
     * @param UrlFileCheckerInterface $htaccessFileChecker
     */
    public function __construct(
        array $canonicalUrlChoices,
        FileFinderInterface $htaccessFinder,
        UrlFileCheckerInterface $htaccessFileChecker
    ) {
        $this->canonicalUrlChoices = $canonicalUrlChoices;
        $this->htaccessFinder = $htaccessFinder;
        $this->htaccessFileChecker = $htaccessFileChecker;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('friendly_url', SwitchType::class)
            ->add('accented_url', SwitchType::class)
            ->add('canonical_url_redirection', ChoiceType::class, [
                    'choices' => $this->canonicalUrlChoices,
                ]
            )
        ;

        list($htaccessFile) = $this->htaccessFinder->find();

        if ($this->htaccessFileChecker->isValidFile($htaccessFile)) {
            $builder
                ->add('disable_apache_multiview', SwitchType::class)
                ->add('disable_apache_mod_security', SwitchType::class)
            ;
        }
    }
}
