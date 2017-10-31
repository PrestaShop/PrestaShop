<?php
/**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Tests\Unit\Core\Foundation\FileSystem;

use PrestaShop\PrestaShop\Tests\TestCase\UnitTestCase;
use PrestaShop\PrestaShop\Core\Foundation\Filesystem\FileSystem;

class Core_Foundation_FileSystem_FileSystemTest extends UnitTestCase
{
    private $fs;
    private $fixturesPath;

    public function setUp()
    {
        $this->fs = new FileSystem();
        $this->fixturesPath = __DIR__ . DIRECTORY_SEPARATOR . 'fixtures';
    }

    public function test_joinPaths_two_paths()
    {
        $this->assertEquals(
            'a' . DIRECTORY_SEPARATOR . 'b',
            $this->fs->joinPaths('a', 'b')
        );
    }

    public function test_joinPaths_three_paths()
    {
        $this->assertEquals(
            'a' . DIRECTORY_SEPARATOR . 'b' . DIRECTORY_SEPARATOR . 'c',
            $this->fs->joinPaths('a', 'b', 'c')
        );
    }

    /**
     * @expectedException \PrestaShop\PrestaShop\Core\Foundation\Filesystem\Exception
     */
    public function test_joinPaths_one_path_throws()
    {
        $this->fs->joinPaths('a');
    }

    /**
     * @expectedException \PrestaShop\PrestaShop\Core\Foundation\Filesystem\Exception
     */
    public function test_joinPaths_zero_path_throws()
    {
        $this->fs->joinPaths();
    }

    public function test_joinPaths_normalizes_directory_separators()
    {
        $this->assertEquals(
            'a' . DIRECTORY_SEPARATOR . 'b' . DIRECTORY_SEPARATOR . 'c' . DIRECTORY_SEPARATOR . 'd',
            $this->fs->joinPaths('a\\', 'b///', 'c\\', 'd/')
        );
    }

    public function test_listEntriesRecursively()
    {
        $expectedPaths = array(
            $this->fs->joinPaths($this->fixturesPath, 'a'),
            $this->fs->joinPaths($this->fixturesPath, 'a', 'a.tmp'),
            $this->fs->joinPaths($this->fixturesPath, 'a', 'b'),
            $this->fs->joinPaths($this->fixturesPath, 'a', 'b', 'b.file'),
            $this->fs->joinPaths($this->fixturesPath, 'toplevel.txt')
        );

        $this->assertEquals(
            $expectedPaths,
            array_keys($this->fs->listEntriesRecursively($this->fixturesPath))
        );
    }

    public function test_listFilesRecursively()
    {
        $expectedPaths = array(
            $this->fs->joinPaths($this->fixturesPath, 'a', 'a.tmp'),
            $this->fs->joinPaths($this->fixturesPath, 'a', 'b', 'b.file'),
            $this->fs->joinPaths($this->fixturesPath, 'toplevel.txt')
        );

        $this->assertEquals(
            $expectedPaths,
            array_keys($this->fs->listFilesRecursively($this->fixturesPath))
        );
    }

    /**
     * Rationale: ls /some/non/existing/file => ls: cannot access /some/non/existing/file: No such file or directory
     * @expectedException \PrestaShop\PrestaShop\Core\Foundation\Filesystem\Exception
     */
    public function test_listEntriesRecursively_throws_if_path_does_not_exist()
    {
        $this->fs->listEntriesRecursively('/some/w/h/e/r/e/over/the/rainbow');
    }

    /**
     *
     * @expectedException \PrestaShop\PrestaShop\Core\Foundation\Filesystem\Exception
     */
    public function test_listEntriesRecursively_throws_when_path_is_a_file()
    {
        $this->fs->listEntriesRecursively(__FILE__);
    }
}
