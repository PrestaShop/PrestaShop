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

namespace Tests\Unit\Core\Translation\DTO;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Translation\DTO\DomainTranslation;
use PrestaShop\PrestaShop\Core\Translation\DTO\MessageTranslation;
use PrestaShop\PrestaShop\Core\Translation\DTO\Translations;

class TranslationsTest extends TestCase
{
    public function testAddDomainTranslation()
    {
        $translations = new Translations();
        $this->assertSame([], $translations->getDomainTranslations());

        $domainTranslation = new DomainTranslation('domainName');
        $translations->addDomainTranslation($domainTranslation);

        $this->assertCount(1, $translations->getDomainTranslations());
        $this->assertSame([
            'domainName' => $domainTranslation,
        ], $translations->getDomainTranslations());
    }

    public function testAddDomainTranslationIgnoredIfKeyAreTheSame()
    {
        $translations = new Translations();

        $domainTranslationFirst = new DomainTranslation('theDomain');
        $translations->addDomainTranslation($domainTranslationFirst);

        $domainTranslationSecond = new DomainTranslation('theDomain');
        $translations->addDomainTranslation($domainTranslationSecond);

        $this->assertCount(1, $translations->getDomainTranslations());
        $this->assertSame([
            'theDomain' => $domainTranslationFirst,
        ], $translations->getDomainTranslations());
    }

    public function testGetDomainTranslation()
    {
        $translations = new Translations();
        $this->assertSame([], $translations->getDomainTranslations());

        $domainTranslation = new DomainTranslation('domainName');
        $translations->addDomainTranslation($domainTranslation);

        $secondDomainTranslation = new DomainTranslation('secondDomainName');
        $translations->addDomainTranslation($secondDomainTranslation);

        $this->assertCount(2, $translations->getDomainTranslations());
        $this->assertSame($domainTranslation, $translations->getDomainTranslation('domainName'));
        $this->assertSame($secondDomainTranslation, $translations->getDomainTranslation('secondDomainName'));
        $this->assertNull($translations->getDomainTranslation('thirdDomainName'));
    }

    public function testTranslationCounters()
    {
        $translations = new Translations();
        $this->assertSame([], $translations->getDomainTranslations());

        $messageTranslation = new MessageTranslation('keyOne');
        $messageTranslation->setFileTranslation('keyOne file translation');
        $messageTranslation->setUserTranslation('keyOne user translation');

        $domainTranslation = new DomainTranslation('domainName');
        $domainTranslation->addMessageTranslation($messageTranslation);

        $translations->addDomainTranslation($domainTranslation);

        $messageTranslation = new MessageTranslation('keyTwo');
        $messageTranslation->setFileTranslation('keyTwo file translation');

        $secondDomainTranslation = new DomainTranslation('secondDomainName');
        $domainTranslation->addMessageTranslation($messageTranslation);

        $translations->addDomainTranslation($secondDomainTranslation);

        $messageTranslation = new MessageTranslation('keyThree');

        $secondDomainTranslation = new DomainTranslation('thirdDomainName');
        $domainTranslation->addMessageTranslation($messageTranslation);

        $translations->addDomainTranslation($secondDomainTranslation);

        $this->assertSame(1, $translations->getMissingTranslationsCount());
        $this->assertSame(3, $translations->getTranslationsCount());
    }

    public function testToArrayWithMetadata()
    {
        $translations = new Translations();

        $this->assertSame([
            Translations::METADATA_KEY_NAME => Translations::EMPTY_META,
        ], $translations->toArray());

        $messageTranslation = new MessageTranslation('theKey');
        $messageTranslation->setFileTranslation('fileTranslation');
        $messageTranslation->setUserTranslation('userTranslation');

        $domainTranslation = new DomainTranslation('theDomain');
        $domainTranslation->addMessageTranslation($messageTranslation);

        $translations->addDomainTranslation($domainTranslation);

        $this->assertSame([
            Translations::METADATA_KEY_NAME => [
                'count' => 1,
                'missing_translations' => 0,
            ],
            'theDomain' => [
                'theKey' => [
                    'default' => 'theKey',
                    'project' => 'fileTranslation',
                    'user' => 'userTranslation',
                    'tree_domain' => [
                        'the',
                        'Domain',
                    ],
                ],
                Translations::METADATA_KEY_NAME => [
                    'count' => 1,
                    'missing_translations' => 0,
                ],
            ],
        ], $translations->toArray());

        $messageTranslation = new MessageTranslation('aKey');
        $messageTranslation->setFileTranslation('aFileTranslation');

        $domainTranslation = new DomainTranslation('theSecondDomain');
        $domainTranslation->addMessageTranslation($messageTranslation);

        $translations->addDomainTranslation($domainTranslation);

        $this->assertSame([
            Translations::METADATA_KEY_NAME => [
                'count' => 2,
                'missing_translations' => 0,
            ],
            'theDomain' => [
                'theKey' => [
                    'default' => 'theKey',
                    'project' => 'fileTranslation',
                    'user' => 'userTranslation',
                    'tree_domain' => [
                        'the',
                        'Domain',
                    ],
                ],
                Translations::METADATA_KEY_NAME => [
                    'count' => 1,
                    'missing_translations' => 0,
                ],
            ],
            'theSecondDomain' => [
                'aKey' => [
                    'default' => 'aKey',
                    'project' => 'aFileTranslation',
                    'user' => null,
                    'tree_domain' => [
                        'the',
                        'Second',
                        'Domain',
                    ],
                ],
                Translations::METADATA_KEY_NAME => [
                    'count' => 1,
                    'missing_translations' => 0,
                ],
            ],
        ], $translations->toArray());

        $messageTranslation = new MessageTranslation('someKey');

        $domainTranslation = new DomainTranslation('theThirdDomain');
        $domainTranslation->addMessageTranslation($messageTranslation);

        $translations->addDomainTranslation($domainTranslation);

        $this->assertSame([
            Translations::METADATA_KEY_NAME => [
                'count' => 3,
                'missing_translations' => 1,
            ],
            'theDomain' => [
                'theKey' => [
                    'default' => 'theKey',
                    'project' => 'fileTranslation',
                    'user' => 'userTranslation',
                    'tree_domain' => [
                        'the',
                        'Domain',
                    ],
                ],
                Translations::METADATA_KEY_NAME => [
                    'count' => 1,
                    'missing_translations' => 0,
                ],
            ],
            'theSecondDomain' => [
                'aKey' => [
                    'default' => 'aKey',
                    'project' => 'aFileTranslation',
                    'user' => null,
                    'tree_domain' => [
                        'the',
                        'Second',
                        'Domain',
                    ],
                ],
                Translations::METADATA_KEY_NAME => [
                    'count' => 1,
                    'missing_translations' => 0,
                ],
            ],
            'theThirdDomain' => [
                'someKey' => [
                    'default' => 'someKey',
                    'project' => null,
                    'user' => null,
                    'tree_domain' => [
                        'the',
                        'Third',
                        'Domain',
                    ],
                ],
                Translations::METADATA_KEY_NAME => [
                    'count' => 1,
                    'missing_translations' => 1,
                ],
            ],
        ], $translations->toArray());
    }

    public function testToArrayWithoutMetadata()
    {
        $translations = new Translations();

        $this->assertSame([], $translations->toArray(false));

        $messageTranslation = new MessageTranslation('theKey');
        $messageTranslation->setFileTranslation('fileTranslation');
        $messageTranslation->setUserTranslation('userTranslation');

        $domainTranslation = new DomainTranslation('theDomain');
        $domainTranslation->addMessageTranslation($messageTranslation);

        $translations->addDomainTranslation($domainTranslation);

        $messageTranslation = new MessageTranslation('aKey');
        $messageTranslation->setFileTranslation('aFileTranslation');

        $domainTranslation = new DomainTranslation('theSecondDomain');
        $domainTranslation->addMessageTranslation($messageTranslation);

        $translations->addDomainTranslation($domainTranslation);

        $messageTranslation = new MessageTranslation('someKey');

        $domainTranslation = new DomainTranslation('theThirdDomain');
        $domainTranslation->addMessageTranslation($messageTranslation);

        $translations->addDomainTranslation($domainTranslation);

        $this->assertSame([
            'theDomain' => [
                'theKey' => [
                    'default' => 'theKey',
                    'project' => 'fileTranslation',
                    'user' => 'userTranslation',
                    'tree_domain' => [
                        'the',
                        'Domain',
                    ],
                ],
            ],
            'theSecondDomain' => [
                'aKey' => [
                    'default' => 'aKey',
                    'project' => 'aFileTranslation',
                    'user' => null,
                    'tree_domain' => [
                        'the',
                        'Second',
                        'Domain',
                    ],
                ],
            ],
            'theThirdDomain' => [
                'someKey' => [
                    'default' => 'someKey',
                    'project' => null,
                    'user' => null,
                    'tree_domain' => [
                        'the',
                        'Third',
                        'Domain',
                    ],
                ],
            ],
        ], $translations->toArray(false));
    }

    public function testGetTree()
    {
        $translations = new Translations();

        $this->assertSame([
            Translations::METADATA_KEY_NAME => Translations::EMPTY_META,
        ], $translations->buildTree());

        $domainTranslation = new DomainTranslation('firstDomain');
        $translations->addDomainTranslation($domainTranslation);

        $this->assertSame([
            Translations::METADATA_KEY_NAME => [
                'count' => 0,
                'missing_translations' => 0,
            ],
            'First' => [
                Translations::METADATA_KEY_NAME => [
                    'count' => 0,
                    'missing_translations' => 0,
                ],
                'Domain' => [
                    Translations::METADATA_KEY_NAME => [
                        'count' => 0,
                        'missing_translations' => 0,
                    ],
                ],
            ],
        ], $translations->buildTree());

        $domainTranslation = new DomainTranslation('firstDomainFirstSubDomain');
        $translations->addDomainTranslation($domainTranslation);

        $this->assertSame([
            Translations::METADATA_KEY_NAME => [
                'count' => 0,
                'missing_translations' => 0,
            ],
            'First' => [
                Translations::METADATA_KEY_NAME => [
                    'count' => 0,
                    'missing_translations' => 0,
                ],
                'Domain' => [
                    Translations::METADATA_KEY_NAME => [
                        'count' => 0,
                        'missing_translations' => 0,
                    ],
                    'First_sub_domain' => [
                        Translations::METADATA_KEY_NAME => [
                            'count' => 0,
                            'missing_translations' => 0,
                        ],
                    ],
                ],
            ],
        ], $translations->buildTree());

        $domainTranslation = new DomainTranslation('firstDomainSecondSubDomain');

        $messageTranslation = new MessageTranslation('aMessage');
        $domainTranslation->addMessageTranslation($messageTranslation);
        $translations->addDomainTranslation($domainTranslation);

        $this->assertSame([
            Translations::METADATA_KEY_NAME => [
                'count' => 1,
                'missing_translations' => 1,
            ],
            'First' => [
                Translations::METADATA_KEY_NAME => [
                    'count' => 1,
                    'missing_translations' => 1,
                ],
                'Domain' => [
                    Translations::METADATA_KEY_NAME => [
                        'count' => 1,
                        'missing_translations' => 1,
                    ],
                    'First_sub_domain' => [
                        Translations::METADATA_KEY_NAME => [
                            'count' => 0,
                            'missing_translations' => 0,
                        ],
                    ],
                    'Second_sub_domain' => [
                        Translations::METADATA_KEY_NAME => [
                            'count' => 1,
                            'missing_translations' => 1,
                        ],
                    ],
                ],
            ],
        ], $translations->buildTree());
    }
}
