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
declare(strict_types=1);

namespace Tests\Unit\Core\Translation\Builder\Map;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Translation\Builder\Map\Catalogue;
use PrestaShop\PrestaShop\Core\Translation\Builder\Map\Domain;
use PrestaShop\PrestaShop\Core\Translation\Builder\Map\Message;

class CatalogueTest extends TestCase
{
    public function testAddDomain(): void
    {
        $translations = new Catalogue();
        $this->assertSame([], $translations->getDomains());

        $domainTranslation = new Domain('domainName');
        $translations->addDomain($domainTranslation);

        $this->assertCount(1, $translations->getDomains());
        $this->assertSame([
            'domainName' => $domainTranslation,
        ], $translations->getDomains());
    }

    public function testAddDomainIgnoredIfKeyAreTheSame(): void
    {
        $translations = new Catalogue();

        $domainTranslationFirst = new Domain('theDomain');
        $translations->addDomain($domainTranslationFirst);

        $domainTranslationSecond = new Domain('theDomain');
        $translations->addDomain($domainTranslationSecond);

        $this->assertCount(1, $translations->getDomains());
        $this->assertSame([
            'theDomain' => $domainTranslationFirst,
        ], $translations->getDomains());
    }

    public function testGetDomain(): void
    {
        $translations = new Catalogue();
        $this->assertSame([], $translations->getDomains());

        $domainTranslation = new Domain('domainName');
        $translations->addDomain($domainTranslation);

        $secondDomain = new Domain('secondDomainName');
        $translations->addDomain($secondDomain);

        $this->assertCount(2, $translations->getDomains());
        $this->assertSame($domainTranslation, $translations->getDomain('domainName'));
        $this->assertSame($secondDomain, $translations->getDomain('secondDomainName'));
        $this->assertNull($translations->getDomain('thirdDomainName'));
    }

    public function testTranslationCounters(): void
    {
        $translations = new Catalogue();
        $this->assertSame([], $translations->getDomains());

        $messageTranslation = new Message('keyOne');
        $messageTranslation->setFileTranslation('keyOne file translation');
        $messageTranslation->setUserTranslation('keyOne user translation');

        $domainTranslation = new Domain('domainName');
        $domainTranslation->addMessage($messageTranslation);

        $translations->addDomain($domainTranslation);

        $messageTranslation = new Message('keyTwo');
        $messageTranslation->setFileTranslation('keyTwo file translation');

        $secondDomain = new Domain('secondDomainName');
        $domainTranslation->addMessage($messageTranslation);

        $translations->addDomain($secondDomain);

        $messageTranslation = new Message('keyThree');

        $secondDomain = new Domain('thirdDomainName');
        $domainTranslation->addMessage($messageTranslation);

        $translations->addDomain($secondDomain);

        $this->assertSame(1, $translations->getMissingTranslationsCount());
        $this->assertSame(3, $translations->getTranslationsCount());
    }

    public function testToArrayWithMetadata(): void
    {
        $translations = new Catalogue();

        $this->assertSame([
            Catalogue::METADATA_KEY_NAME => Catalogue::EMPTY_META,
        ], $translations->toArray());

        $messageTranslation = new Message('theKey');
        $messageTranslation->setFileTranslation('fileTranslation');
        $messageTranslation->setUserTranslation('userTranslation');

        $domainTranslation = new Domain('theDomain');
        $domainTranslation->addMessage($messageTranslation);

        $translations->addDomain($domainTranslation);

        $this->assertSame([
            Catalogue::METADATA_KEY_NAME => [
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
                Catalogue::METADATA_KEY_NAME => [
                    'count' => 1,
                    'missing_translations' => 0,
                ],
            ],
        ], $translations->toArray());

        $messageTranslation = new Message('aKey');
        $messageTranslation->setFileTranslation('aFileTranslation');

        $domainTranslation = new Domain('theSecondDomain');
        $domainTranslation->addMessage($messageTranslation);

        $translations->addDomain($domainTranslation);

        $this->assertSame([
            Catalogue::METADATA_KEY_NAME => [
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
                Catalogue::METADATA_KEY_NAME => [
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
                Catalogue::METADATA_KEY_NAME => [
                    'count' => 1,
                    'missing_translations' => 0,
                ],
            ],
        ], $translations->toArray());

        $messageTranslation = new Message('someKey');

        $domainTranslation = new Domain('theThirdDomain');
        $domainTranslation->addMessage($messageTranslation);

        $translations->addDomain($domainTranslation);

        $this->assertSame([
            Catalogue::METADATA_KEY_NAME => [
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
                Catalogue::METADATA_KEY_NAME => [
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
                Catalogue::METADATA_KEY_NAME => [
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
                Catalogue::METADATA_KEY_NAME => [
                    'count' => 1,
                    'missing_translations' => 1,
                ],
            ],
        ], $translations->toArray());
    }

    public function testToArrayWithoutMetadata(): void
    {
        $translations = new Catalogue();

        $this->assertSame([], $translations->toArray(false));

        $messageTranslation = new Message('theKey');
        $messageTranslation->setFileTranslation('fileTranslation');
        $messageTranslation->setUserTranslation('userTranslation');

        $domainTranslation = new Domain('theDomain');
        $domainTranslation->addMessage($messageTranslation);

        $translations->addDomain($domainTranslation);

        $messageTranslation = new Message('aKey');
        $messageTranslation->setFileTranslation('aFileTranslation');

        $domainTranslation = new Domain('theSecondDomain');
        $domainTranslation->addMessage($messageTranslation);

        $translations->addDomain($domainTranslation);

        $messageTranslation = new Message('someKey');

        $domainTranslation = new Domain('theThirdDomain');
        $domainTranslation->addMessage($messageTranslation);

        $translations->addDomain($domainTranslation);

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

    public function testGetTree(): void
    {
        $translations = new Catalogue();

        $this->assertSame([
            Catalogue::METADATA_KEY_NAME => Catalogue::EMPTY_META,
        ], $translations->buildTree());

        $domainTranslation = new Domain('firstDomain');
        $translations->addDomain($domainTranslation);

        $this->assertSame([
            Catalogue::METADATA_KEY_NAME => [
                'count' => 0,
                'missing_translations' => 0,
            ],
            'First' => [
                Catalogue::METADATA_KEY_NAME => [
                    'count' => 0,
                    'missing_translations' => 0,
                ],
                'Domain' => [
                    Catalogue::METADATA_KEY_NAME => [
                        'count' => 0,
                        'missing_translations' => 0,
                    ],
                ],
            ],
        ], $translations->buildTree());

        $domainTranslation = new Domain('firstDomainFirstSubDomain');
        $translations->addDomain($domainTranslation);

        $this->assertSame([
            Catalogue::METADATA_KEY_NAME => [
                'count' => 0,
                'missing_translations' => 0,
            ],
            'First' => [
                Catalogue::METADATA_KEY_NAME => [
                    'count' => 0,
                    'missing_translations' => 0,
                ],
                'Domain' => [
                    Catalogue::METADATA_KEY_NAME => [
                        'count' => 0,
                        'missing_translations' => 0,
                    ],
                    'First_sub_domain' => [
                        Catalogue::METADATA_KEY_NAME => [
                            'count' => 0,
                            'missing_translations' => 0,
                        ],
                    ],
                ],
            ],
        ], $translations->buildTree());

        $domainTranslation = new Domain('firstDomainSecondSubDomain');

        $messageTranslation = new Message('aMessage');
        $domainTranslation->addMessage($messageTranslation);
        $translations->addDomain($domainTranslation);

        $this->assertSame([
            Catalogue::METADATA_KEY_NAME => [
                'count' => 1,
                'missing_translations' => 1,
            ],
            'First' => [
                Catalogue::METADATA_KEY_NAME => [
                    'count' => 1,
                    'missing_translations' => 1,
                ],
                'Domain' => [
                    Catalogue::METADATA_KEY_NAME => [
                        'count' => 1,
                        'missing_translations' => 1,
                    ],
                    'First_sub_domain' => [
                        Catalogue::METADATA_KEY_NAME => [
                            'count' => 0,
                            'missing_translations' => 0,
                        ],
                    ],
                    'Second_sub_domain' => [
                        Catalogue::METADATA_KEY_NAME => [
                            'count' => 1,
                            'missing_translations' => 1,
                        ],
                    ],
                ],
            ],
        ], $translations->buildTree());
    }
}
