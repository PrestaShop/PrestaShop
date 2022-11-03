<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace Tests\Unit\Core\Util\HelperCard;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Util\HelperCard\DocumentationLinkProvider;
use PrestaShop\PrestaShop\Core\Util\HelperCard\DocumentationLinkProviderInterface;
use PrestaShop\PrestaShop\Core\Util\HelperCard\HelperCardDocumentationDoesNotExistException;

class DocumentationLinkProviderTest extends TestCase
{
    public function testIsValidImplementation()
    {
        $provider = new DocumentationLinkProvider(
            'FR',
            ['seo_card' => [
                'FR' => 'https://doc.presta.com/fr/seo',
                'EN' => 'https://doc.presta.com/en/seo',
                '_fallback' => 'https://doc.presta.com/seo',
            ]]
        );

        $this->assertInstanceOf(DocumentationLinkProviderInterface::class, $provider);
    }

    public function testGetFRValidLink()
    {
        $provider = new DocumentationLinkProvider(
            'FR',
            ['seo_card' => [
                'FR' => 'https://doc.presta.com/fr/seo',
                'EN' => 'https://doc.presta.com/en/seo',
                '_fallback' => 'https://doc.presta.com/seo',
            ]]
        );

        $link = $provider->getLink('seo_card');

        $this->assertEquals('https://doc.presta.com/fr/seo', $link);
    }

    public function testGetBadCardLink()
    {
        $provider = new DocumentationLinkProvider(
            'FR',
            ['seo_card' => [
                'FR' => 'https://doc.presta.com/fr/seo',
                'EN' => 'https://doc.presta.com/en/seo',
                '_fallback' => 'https://doc.presta.com/seo',
            ]]
        );

        $this->expectException(HelperCardDocumentationDoesNotExistException::class);

        $link = $provider->getLink('aaaa');
    }

    public function testGetITInvalidLinkSoExpectsFallback()
    {
        $provider = new DocumentationLinkProvider(
            'IT',
            ['seo_card' => [
                'FR' => 'https://doc.presta.com/fr/seo',
                'EN' => 'https://doc.presta.com/en/seo',
                '_fallback' => 'https://doc.presta.com/seo',
            ]]
        );

        $link = $provider->getLink('seo_card');

        $this->assertEquals('https://doc.presta.com/seo', $link);
    }

    public function testGetEZInvalidLinkWithoutFallback()
    {
        $provider = new DocumentationLinkProvider(
            'EZ',
            ['seo_card' => [
                'FR' => 'https://doc.presta.com/fr/seo',
                'EN' => 'https://doc.presta.com/en/seo',
            ]]
        );

        $this->expectException(HelperCardDocumentationDoesNotExistException::class);

        $link = $provider->getLink('seo_card');
    }
}
