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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Language;
use PHPUnit\Framework\TestCase;
use Tests\Resources\DatabaseDump;

class LanguageTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        DatabaseDump::restoreAllTables();
    }

    public function testGetIdByIso()
    {
        $this->assertNull(Language::getIdByIso('zz', false));
        $this->assertNull(Language::getIdByIso('zz', true));

        $language = new Language();
        $language->name = 'zz';
        $language->iso_code = 'zz';
        $language->locale = 'zz-ZZ';
        $language->language_code = 'zz-ZZ';
        $language->add();

        $idByIso = Language::getIdByIso('zz', false);
        $this->assertNotEquals(0, $idByIso);
        $this->assertIsInt($idByIso);

        $idByIso = Language::getIdByIso('zz', true);
        $this->assertNotEquals(0, $idByIso);
        $this->assertIsInt($idByIso);
    }
}
