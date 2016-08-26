<?php
/**
 * 2007-2016 PrestaShop.
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
 *  @author 	PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2016 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Tests\Mock;

use PrestaShopBundle\Translation\Provider\ProviderInterface;
use Symfony\Component\Translation\MessageCatalogue;

class MockCatalogueProviderMock implements ProviderInterface
{
    const DEFAULT_LOCALE = 'en-US';

    /**
     * {@inheritdoc}
     */
    public function getDirectories()
    {
        return array('to-be-defined');
    }

    /**
     * {@inheritdoc}
     */
    public function getTranslationDomains()
    {
        return array('');
    }

    /**
     * {@inheritdoc}
     */
    public function getLocale()
    {
        return self::DEFAULT_LOCALE;
    }

    /**
     * {@inheritdoc}
     */
    public function getMessageCatalogue()
    {
        return new MessageCatalogue(self::DEFAULT_LOCALE);
    }
}
