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

class DomainTranslationTest extends TestCase
{
    public function testAddMessageTranslation()
    {
        $domainTranslation = new DomainTranslation('domainName');
        $this->assertSame('domainName', $domainTranslation->getDomainName());

        $messageTranslation = new MessageTranslation('theKey');
        $domainTranslation->addMessageTranslation($messageTranslation);

        $this->assertCount(1, $domainTranslation->getMessageTranslations());
        $this->assertSame(1, $domainTranslation->getTranslationsCount());
        $this->assertSame(1, $domainTranslation->getMissingTranslationsCount());
        $this->assertSame([
            'theKey' => $messageTranslation,
        ], $domainTranslation->getMessageTranslations());
    }

    public function testAddMessageTranslationIgnoredIfKeyAreTheSame()
    {
        $domainTranslation = new DomainTranslation('domainName');
        $this->assertSame('domainName', $domainTranslation->getDomainName());

        $messageTranslationFirst = new MessageTranslation('theKey');
        $domainTranslation->addMessageTranslation($messageTranslationFirst);

        $messageTranslationSecond = new MessageTranslation('theKey');
        $domainTranslation->addMessageTranslation($messageTranslationSecond);

        $this->assertCount(1, $domainTranslation->getMessageTranslations());
        $this->assertSame(1, $domainTranslation->getTranslationsCount());
        $this->assertSame(1, $domainTranslation->getMissingTranslationsCount());
        $this->assertSame([
            'theKey' => $messageTranslationFirst,
        ], $domainTranslation->getMessageTranslations());
    }

    public function testAddMessageTranslationNotTranslatedAreOnTop()
    {
        $domainTranslation = new DomainTranslation('domainName');
        $this->assertSame('domainName', $domainTranslation->getDomainName());

        $messageTranslationFirst = new MessageTranslation('theKey');
        $messageTranslationFirst->setFileTranslation('fileTranslation');
        $messageTranslationFirst->setUserTranslation('userTranslation');
        $domainTranslation->addMessageTranslation($messageTranslationFirst);

        $messageTranslationSecond = new MessageTranslation('aKey');
        $messageTranslationSecond->setFileTranslation('fileTranslation');
        $domainTranslation->addMessageTranslation($messageTranslationSecond);

        $messageTranslationThird = new MessageTranslation('theSecondKey');
        $domainTranslation->addMessageTranslation($messageTranslationThird);

        $this->assertCount(3, $domainTranslation->getMessageTranslations());
        $this->assertSame(3, $domainTranslation->getTranslationsCount());
        $this->assertSame(1, $domainTranslation->getMissingTranslationsCount());
        $this->assertSame([
            'theSecondKey' => $messageTranslationThird,
            'theKey' => $messageTranslationFirst,
            'aKey' => $messageTranslationSecond,
        ], $domainTranslation->getMessageTranslations());
    }

    public function testToArrayWithMetadata()
    {
        $domainTranslation = new DomainTranslation('domainName');

        $this->assertSame([
            Translations::METADATA_KEY_NAME => Translations::EMPTY_META,
        ], $domainTranslation->toArray());

        $messageTranslationFirst = new MessageTranslation('theKey');
        $messageTranslationFirst->setFileTranslation('fileTranslation');
        $messageTranslationFirst->setUserTranslation('userTranslation');
        $domainTranslation->addMessageTranslation($messageTranslationFirst);

        $this->assertSame([
            'theKey' => [
                'default' => 'theKey',
                'project' => 'fileTranslation',
                'user' => 'userTranslation',
                'tree_domain' => [
                    'domain',
                    'Name',
                ],
            ],
            Translations::METADATA_KEY_NAME => [
                'count' => 1,
                'missing_translations' => 0,
            ],
        ], $domainTranslation->toArray());

        $messageTranslationSecond = new MessageTranslation('aKey');
        $messageTranslationSecond->setFileTranslation('aFileTranslation');
        $domainTranslation->addMessageTranslation($messageTranslationSecond);

        $this->assertSame([
            'theKey' => [
                'default' => 'theKey',
                'project' => 'fileTranslation',
                'user' => 'userTranslation',
                'tree_domain' => [
                    'domain',
                    'Name',
                ],
            ],
            'aKey' => [
                'default' => 'aKey',
                'project' => 'aFileTranslation',
                'user' => null,
                'tree_domain' => [
                    'domain',
                    'Name',
                ],
            ],
            Translations::METADATA_KEY_NAME => [
                'count' => 2,
                'missing_translations' => 0,
            ],
        ], $domainTranslation->toArray());

        $messageTranslationThird = new MessageTranslation('theSecondKey');
        $domainTranslation->addMessageTranslation($messageTranslationThird);

        $this->assertSame([
            'theSecondKey' => [
                'default' => 'theSecondKey',
                'project' => null,
                'user' => null,
                'tree_domain' => [
                    'domain',
                    'Name',
                ],
            ],
            'theKey' => [
                'default' => 'theKey',
                'project' => 'fileTranslation',
                'user' => 'userTranslation',
                'tree_domain' => [
                    'domain',
                    'Name',
                ],
            ],
            'aKey' => [
                'default' => 'aKey',
                'project' => 'aFileTranslation',
                'user' => null,
                'tree_domain' => [
                    'domain',
                    'Name',
                ],
            ],
            Translations::METADATA_KEY_NAME => [
                'count' => 3,
                'missing_translations' => 1,
            ],
        ], $domainTranslation->toArray());
    }

    public function testToArrayWithoutMetadata()
    {
        $domainTranslation = new DomainTranslation('domainName');

        $this->assertSame([], $domainTranslation->toArray(false));

        $messageTranslationFirst = new MessageTranslation('theKey');
        $messageTranslationFirst->setFileTranslation('fileTranslation');
        $messageTranslationFirst->setUserTranslation('userTranslation');
        $domainTranslation->addMessageTranslation($messageTranslationFirst);

        $this->assertSame([
            'theKey' => [
                'default' => 'theKey',
                'project' => 'fileTranslation',
                'user' => 'userTranslation',
                'tree_domain' => [
                    'domain',
                    'Name',
                ],
            ],
        ], $domainTranslation->toArray(false));

        $messageTranslationSecond = new MessageTranslation('aKey');
        $messageTranslationSecond->setFileTranslation('aFileTranslation');
        $domainTranslation->addMessageTranslation($messageTranslationSecond);

        $this->assertSame([
            'theKey' => [
                'default' => 'theKey',
                'project' => 'fileTranslation',
                'user' => 'userTranslation',
                'tree_domain' => [
                    'domain',
                    'Name',
                ],
            ],
            'aKey' => [
                'default' => 'aKey',
                'project' => 'aFileTranslation',
                'user' => null,
                'tree_domain' => [
                    'domain',
                    'Name',
                ],
            ],
        ], $domainTranslation->toArray(false));

        $messageTranslationThird = new MessageTranslation('theSecondKey');
        $domainTranslation->addMessageTranslation($messageTranslationThird);

        $this->assertSame([
            'theSecondKey' => [
                'default' => 'theSecondKey',
                'project' => null,
                'user' => null,
                'tree_domain' => [
                    'domain',
                    'Name',
                ],
            ],
            'theKey' => [
                'default' => 'theKey',
                'project' => 'fileTranslation',
                'user' => 'userTranslation',
                'tree_domain' => [
                    'domain',
                    'Name',
                ],
            ],
            'aKey' => [
                'default' => 'aKey',
                'project' => 'aFileTranslation',
                'user' => null,
                'tree_domain' => [
                    'domain',
                    'Name',
                ],
            ],
        ], $domainTranslation->toArray(false));
    }

    public function testGetTree()
    {
        $domainTranslation = new DomainTranslation('domainName');
        $tree = [];

        $this->assertSame([
            Translations::METADATA_KEY_NAME => [
                'count' => 0,
                'missing_translations' => 0,
            ],
            'Domain' => [
                Translations::METADATA_KEY_NAME => [
                    'count' => 0,
                    'missing_translations' => 0,
                ],
                'Name' => [
                    Translations::METADATA_KEY_NAME => [
                        'count' => 0,
                        'missing_translations' => 0,
                    ],
                ],
            ],
        ], $domainTranslation->mergeTree($tree));

        $messageTranslationFirst = new MessageTranslation('theKey');
        $messageTranslationFirst->setFileTranslation('fileTranslation');
        $messageTranslationFirst->setUserTranslation('userTranslation');
        $domainTranslation->addMessageTranslation($messageTranslationFirst);

        $tree = [];
        $this->assertSame([
            Translations::METADATA_KEY_NAME => [
                'count' => 0,
                'missing_translations' => 0,
            ],
            'Domain' => [
                Translations::METADATA_KEY_NAME => [
                    'count' => 0,
                    'missing_translations' => 0,
                ],
                'Name' => [
                    Translations::METADATA_KEY_NAME => [
                        'count' => 1,
                        'missing_translations' => 0,
                    ],
                ],
            ],
        ], $domainTranslation->mergeTree($tree));

        $messageTranslationSecond = new MessageTranslation('aKey');
        $messageTranslationSecond->setFileTranslation('aFileTranslation');
        $domainTranslation->addMessageTranslation($messageTranslationSecond);

        $tree = [];
        $this->assertSame([
            Translations::METADATA_KEY_NAME => [
                'count' => 0,
                'missing_translations' => 0,
            ],
            'Domain' => [
                Translations::METADATA_KEY_NAME => [
                    'count' => 0,
                    'missing_translations' => 0,
                ],
                'Name' => [
                    Translations::METADATA_KEY_NAME => [
                        'count' => 2,
                        'missing_translations' => 0,
                    ],
                ],
            ],
        ], $domainTranslation->mergeTree($tree));

        $messageTranslationThird = new MessageTranslation('theSecondKey');
        $domainTranslation->addMessageTranslation($messageTranslationThird);

        $tree = [];
        $this->assertSame([
            Translations::METADATA_KEY_NAME => [
                'count' => 0,
                'missing_translations' => 0,
            ],
            'Domain' => [
                Translations::METADATA_KEY_NAME => [
                    'count' => 0,
                    'missing_translations' => 0,
                ],
                'Name' => [
                    Translations::METADATA_KEY_NAME => [
                        'count' => 3,
                        'missing_translations' => 1,
                    ],
                ],
            ],
        ], $domainTranslation->mergeTree($tree));
    }

    public function testGetTreeWithSubDomain()
    {
        $domainTranslation = new DomainTranslation('domainNameWithMoreThanTreeSubdomain');

        $tree = [];
        $this->assertSame([
            Translations::METADATA_KEY_NAME => [
                'count' => 0,
                'missing_translations' => 0,
            ],
            'Domain' => [
                Translations::METADATA_KEY_NAME => [
                    'count' => 0,
                    'missing_translations' => 0,
                ],
                'Name' => [
                    Translations::METADATA_KEY_NAME => [
                        'count' => 0,
                        'missing_translations' => 0,
                    ],
                    'With_more_than_tree_subdomain' => [
                        Translations::METADATA_KEY_NAME => [
                            'count' => 0,
                            'missing_translations' => 0,
                        ],
                    ],
                ],
            ],
        ], $domainTranslation->mergeTree($tree));

        $messageTranslationFirst = new MessageTranslation('theKey');
        $messageTranslationFirst->setFileTranslation('fileTranslation');
        $messageTranslationFirst->setUserTranslation('userTranslation');
        $domainTranslation->addMessageTranslation($messageTranslationFirst);

        // If domain already exists in the parameter tree it won't be modified
        $this->assertSame([
            Translations::METADATA_KEY_NAME => [
                'count' => 0,
                'missing_translations' => 0,
            ],
            'Domain' => [
                Translations::METADATA_KEY_NAME => [
                    'count' => 0,
                    'missing_translations' => 0,
                ],
                'Name' => [
                    Translations::METADATA_KEY_NAME => [
                        'count' => 0,
                        'missing_translations' => 0,
                    ],
                    'With_more_than_tree_subdomain' => [
                        Translations::METADATA_KEY_NAME => [
                            'count' => 0,
                            'missing_translations' => 0,
                        ],
                    ],
                ],
            ],
        ], $domainTranslation->mergeTree($tree));

        // reset the tree
        $tree = [];
        $this->assertSame([
            Translations::METADATA_KEY_NAME => [
                'count' => 0,
                'missing_translations' => 0,
            ],
            'Domain' => [
                Translations::METADATA_KEY_NAME => [
                    'count' => 0,
                    'missing_translations' => 0,
                ],
                'Name' => [
                    Translations::METADATA_KEY_NAME => [
                        'count' => 0,
                        'missing_translations' => 0,
                    ],
                    'With_more_than_tree_subdomain' => [
                        Translations::METADATA_KEY_NAME => [
                            'count' => 1,
                            'missing_translations' => 0,
                        ],
                    ],
                ],
            ],
        ], $domainTranslation->mergeTree($tree));

        // Test the missing translations counter
        $messageTranslationSecond = new MessageTranslation('aKey');
        $domainTranslation->addMessageTranslation($messageTranslationSecond);

        $tree = [];
        $this->assertSame([
            Translations::METADATA_KEY_NAME => [
                'count' => 0,
                'missing_translations' => 0,
            ],
            'Domain' => [
                Translations::METADATA_KEY_NAME => [
                    'count' => 0,
                    'missing_translations' => 0,
                ],
                'Name' => [
                    Translations::METADATA_KEY_NAME => [
                        'count' => 0,
                        'missing_translations' => 0,
                    ],
                    'With_more_than_tree_subdomain' => [
                        Translations::METADATA_KEY_NAME => [
                            'count' => 2,
                            'missing_translations' => 1,
                        ],
                    ],
                ],
            ],
        ], $domainTranslation->mergeTree($tree));
    }

    public function testSplitDomain()
    {
        $this->assertSame([''], DomainTranslation::splitDomain(''));
        $this->assertSame(['domain'], DomainTranslation::splitDomain('Domain'));
        $this->assertSame(['domain'], DomainTranslation::splitDomain('domain'));
        $this->assertSame(['domain', 'name'], DomainTranslation::splitDomain('domainName'));
        $this->assertSame(['domain', 'namesimple'], DomainTranslation::splitDomain('domainNamesimple'));
        $this->assertSame(['domain', 'name', 'simple'], DomainTranslation::splitDomain('domainNameSimple'));
        $this->assertSame(['domain', 'name', 'with_more_than_three'], DomainTranslation::splitDomain('domainNameWithMoreThanThree'));
    }
}
