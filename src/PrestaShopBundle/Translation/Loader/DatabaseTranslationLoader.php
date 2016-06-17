<?php

/**
 * 2007-2015 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @copyright 2007-2015 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Translation\Loader;

use Symfony\Component\Translation\Loader\LoaderInterface;
use Symfony\Component\Translation\MessageCatalogue;
use Doctrine\ORM\EntityManagerInterface;

class DatabaseTranslationLoader implements LoaderInterface
{
    /** @var EntityManagerInterface */
    protected $em;
    
    /**
     * 
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    
    /**
     * {@inheritdoc}
     */
    public function load($resource, $locale, $domain = 'messages')
    {
        $lang = $this->em->getRepository('PrestaShopBundle:Lang')->findOneByLocale($locale);
        $translations = $this->em->getRepository('PrestaShopBundle:Translation')->findBy(['lang' => $lang, 'domain' => $domain]);
        $catalogue = new MessageCatalogue($locale);
        
        foreach ($translations as $translation) {
            $catalogue->set($translation->getKey(), $translation->getTranslation(), $translation->getDomain());
        }
        
        return $catalogue;
    }
}
