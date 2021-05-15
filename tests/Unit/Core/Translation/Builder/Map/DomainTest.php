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

namespace Tests\Unit\Core\Translation\Builder\Map;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Translation\Builder\Map\Catalogue;
use PrestaShop\PrestaShop\Core\Translation\Builder\Map\Domain;
use PrestaShop\PrestaShop\Core\Translation\Builder\Map\Message;

class DomainTest extends TestCase
{
    public function testAddMessage(): void
    {
        $domainTranslation = new Domain('domainName');
        $this->assertSame('domainName', $domainTranslation->getDomainName());

        $messageTranslation = new Message('theKey');
        $domainTranslation->addMessage($messageTranslation);

        $this->assertCount(1, $domainTranslation->getMessages());
        $this->assertSame(1, $domainTranslation->getTranslationsCount());
        $this->assertSame(1, $domainTranslation->getMissingTranslationsCount());
        $this->assertSame([
            'theKey' => $messageTranslation,
        ], $domainTranslation->getMessages());
    }

    public function testAddMessageIgnoredIfKeyAreTheSame(): void
    {
        $domainTranslation = new Domain('domainName');
        $this->assertSame('domainName', $domainTranslation->getDomainName());

        $messageTranslationFirst = new Message('theKey');
        $domainTranslation->addMessage($messageTranslationFirst);

        $messageTranslationSecond = new Message('theKey');
        $domainTranslation->addMessage($messageTranslationSecond);

        $this->assertCount(1, $domainTranslation->getMessages());
        $this->assertSame(1, $domainTranslation->getTranslationsCount());
        $this->assertSame(1, $domainTranslation->getMissingTranslationsCount());
        $this->assertSame([
            'theKey' => $messageTranslationFirst,
        ], $domainTranslation->getMessages());
    }

    public function testAddMessageNotTranslatedAreOnTop(): void
    {
        $domainTranslation = new Domain('domainName');
        $this->assertSame('domainName', $domainTranslation->getDomainName());

        $messageTranslationFirst = new Message('theKey');
        $messageTranslationFirst->setFileTranslation('fileTranslation');
        $messageTranslationFirst->setUserTranslation('userTranslation');
        $domainTranslation->addMessage($messageTranslationFirst);

        $messageTranslationSecond = new Message('aKey');
        $messageTranslationSecond->setFileTranslation('fileTranslation');
        $domainTranslation->addMessage($messageTranslationSecond);

        $messageTranslationThird = new Message('theSecondKey');
        $domainTranslation->addMessage($messageTranslationThird);

        $this->assertCount(3, $domainTranslation->getMessages());
        $this->assertSame(3, $domainTranslation->getTranslationsCount());
        $this->assertSame(1, $domainTranslation->getMissingTranslationsCount());
        $this->assertSame([
            'theSecondKey' => $messageTranslationThird,
            'theKey' => $messageTranslationFirst,
            'aKey' => $messageTranslationSecond,
        ], $domainTranslation->getMessages());
    }

    public function testToArrayWithMetadata(): void
    {
        $domainTranslation = new Domain('domainName');

        $this->assertSame([
            Catalogue::METADATA_KEY_NAME => Catalogue::EMPTY_META,
        ], $domainTranslation->toArray());

        $messageTranslationFirst = new Message('theKey');
        $messageTranslationFirst->setFileTranslation('fileTranslation');
        $messageTranslationFirst->setUserTranslation('userTranslation');
        $domainTranslation->addMessage($messageTranslationFirst);

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
            Catalogue::METADATA_KEY_NAME => [
                'count' => 1,
                'missing_translations' => 0,
            ],
        ], $domainTranslation->toArray());

        $messageTranslationSecond = new Message('aKey');
        $messageTranslationSecond->setFileTranslation('aFileTranslation');
        $domainTranslation->addMessage($messageTranslationSecond);

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
            Catalogue::METADATA_KEY_NAME => [
                'count' => 2,
                'missing_translations' => 0,
            ],
        ], $domainTranslation->toArray());

        $messageTranslationThird = new Message('theSecondKey');
        $domainTranslation->addMessage($messageTranslationThird);

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
            Catalogue::METADATA_KEY_NAME => [
                'count' => 3,
                'missing_translations' => 1,
            ],
        ], $domainTranslation->toArray());
    }

    public function testToArrayWithoutMetadata(): void
    {
        $domainTranslation = new Domain('domainName');

        $this->assertSame([], $domainTranslation->toArray(false));

        $messageTranslationFirst = new Message('theKey');
        $messageTranslationFirst->setFileTranslation('fileTranslation');
        $messageTranslationFirst->setUserTranslation('userTranslation');
        $domainTranslation->addMessage($messageTranslationFirst);

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

        $messageTranslationSecond = new Message('aKey');
        $messageTranslationSecond->setFileTranslation('aFileTranslation');
        $domainTranslation->addMessage($messageTranslationSecond);

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

        $messageTranslationThird = new Message('theSecondKey');
        $domainTranslation->addMessage($messageTranslationThird);

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

    public function testGetTree(): void
    {
        $domainTranslation = new Domain('domainName');
        $tree = [];

        $this->assertSame([
            Catalogue::METADATA_KEY_NAME => [
                'count' => 0,
                'missing_translations' => 0,
            ],
            'Domain' => [
                Catalogue::METADATA_KEY_NAME => [
                    'count' => 0,
                    'missing_translations' => 0,
                ],
                'Name' => [
                    Catalogue::METADATA_KEY_NAME => [
                        'count' => 0,
                        'missing_translations' => 0,
                    ],
                ],
            ],
        ], $domainTranslation->mergeTree($tree));

        $messageTranslationFirst = new Message('theKey');
        $messageTranslationFirst->setFileTranslation('fileTranslation');
        $messageTranslationFirst->setUserTranslation('userTranslation');
        $domainTranslation->addMessage($messageTranslationFirst);

        $tree = [];
        $this->assertSame([
            Catalogue::METADATA_KEY_NAME => [
                'count' => 0,
                'missing_translations' => 0,
            ],
            'Domain' => [
                Catalogue::METADATA_KEY_NAME => [
                    'count' => 0,
                    'missing_translations' => 0,
                ],
                'Name' => [
                    Catalogue::METADATA_KEY_NAME => [
                        'count' => 1,
                        'missing_translations' => 0,
                    ],
                ],
            ],
        ], $domainTranslation->mergeTree($tree));

        $messageTranslationSecond = new Message('aKey');
        $messageTranslationSecond->setFileTranslation('aFileTranslation');
        $domainTranslation->addMessage($messageTranslationSecond);

        $tree = [];
        $this->assertSame([
            Catalogue::METADATA_KEY_NAME => [
                'count' => 0,
                'missing_translations' => 0,
            ],
            'Domain' => [
                Catalogue::METADATA_KEY_NAME => [
                    'count' => 0,
                    'missing_translations' => 0,
                ],
                'Name' => [
                    Catalogue::METADATA_KEY_NAME => [
                        'count' => 2,
                        'missing_translations' => 0,
                    ],
                ],
            ],
        ], $domainTranslation->mergeTree($tree));

        $messageTranslationThird = new Message('theSecondKey');
        $domainTranslation->addMessage($messageTranslationThird);

        $tree = [];
        $this->assertSame([
            Catalogue::METADATA_KEY_NAME => [
                'count' => 0,
                'missing_translations' => 0,
            ],
            'Domain' => [
                Catalogue::METADATA_KEY_NAME => [
                    'count' => 0,
                    'missing_translations' => 0,
                ],
                'Name' => [
                    Catalogue::METADATA_KEY_NAME => [
                        'count' => 3,
                        'missing_translations' => 1,
                    ],
                ],
            ],
        ], $domainTranslation->mergeTree($tree));
    }

    public function testGetTreeWithSubDomain(): void
    {
        $domainTranslation = new Domain('domainNameWithMoreThanTreeSubdomain');

        $tree = [];
        $this->assertSame([
            Catalogue::METADATA_KEY_NAME => [
                'count' => 0,
                'missing_translations' => 0,
            ],
            'Domain' => [
                Catalogue::METADATA_KEY_NAME => [
                    'count' => 0,
                    'missing_translations' => 0,
                ],
                'Name' => [
                    Catalogue::METADATA_KEY_NAME => [
                        'count' => 0,
                        'missing_translations' => 0,
                    ],
                    'With_more_than_tree_subdomain' => [
                        Catalogue::METADATA_KEY_NAME => [
                            'count' => 0,
                            'missing_translations' => 0,
                        ],
                    ],
                ],
            ],
        ], $domainTranslation->mergeTree($tree));

        $messageTranslationFirst = new Message('theKey');
        $messageTranslationFirst->setFileTranslation('fileTranslation');
        $messageTranslationFirst->setUserTranslation('userTranslation');
        $domainTranslation->addMessage($messageTranslationFirst);

        // If domain already exists in the parameter tree it won't be modified
        $this->assertSame([
            Catalogue::METADATA_KEY_NAME => [
                'count' => 0,
                'missing_translations' => 0,
            ],
            'Domain' => [
                Catalogue::METADATA_KEY_NAME => [
                    'count' => 0,
                    'missing_translations' => 0,
                ],
                'Name' => [
                    Catalogue::METADATA_KEY_NAME => [
                        'count' => 0,
                        'missing_translations' => 0,
                    ],
                    'With_more_than_tree_subdomain' => [
                        Catalogue::METADATA_KEY_NAME => [
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
            Catalogue::METADATA_KEY_NAME => [
                'count' => 0,
                'missing_translations' => 0,
            ],
            'Domain' => [
                Catalogue::METADATA_KEY_NAME => [
                    'count' => 0,
                    'missing_translations' => 0,
                ],
                'Name' => [
                    Catalogue::METADATA_KEY_NAME => [
                        'count' => 0,
                        'missing_translations' => 0,
                    ],
                    'With_more_than_tree_subdomain' => [
                        Catalogue::METADATA_KEY_NAME => [
                            'count' => 1,
                            'missing_translations' => 0,
                        ],
                    ],
                ],
            ],
        ], $domainTranslation->mergeTree($tree));

        // Test the missing translations counter
        $messageTranslationSecond = new Message('aKey');
        $domainTranslation->addMessage($messageTranslationSecond);

        $tree = [];
        $this->assertSame([
            Catalogue::METADATA_KEY_NAME => [
                'count' => 0,
                'missing_translations' => 0,
            ],
            'Domain' => [
                Catalogue::METADATA_KEY_NAME => [
                    'count' => 0,
                    'missing_translations' => 0,
                ],
                'Name' => [
                    Catalogue::METADATA_KEY_NAME => [
                        'count' => 0,
                        'missing_translations' => 0,
                    ],
                    'With_more_than_tree_subdomain' => [
                        Catalogue::METADATA_KEY_NAME => [
                            'count' => 2,
                            'missing_translations' => 1,
                        ],
                    ],
                ],
            ],
        ], $domainTranslation->mergeTree($tree));
    }

    public function testSplitDomain(): void
    {
        $this->assertSame([''], Domain::splitDomain(''));
        $this->assertSame(['domain'], Domain::splitDomain('Domain'));
        $this->assertSame(['domain'], Domain::splitDomain('domain'));
        $this->assertSame(['domain', 'name'], Domain::splitDomain('domainName'));
        $this->assertSame(['domain', 'namesimple'], Domain::splitDomain('domainNamesimple'));
        $this->assertSame(['domain', 'name', 'simple'], Domain::splitDomain('domainNameSimple'));
        $this->assertSame(['domain', 'name', 'with_more_than_three'], Domain::splitDomain('domainNameWithMoreThanThree'));
    }
}
