<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

/**
 * Exception thrown when creating a release by tools/build.
 */
class BuildException extends Exception
{
    /** @var string */
    protected $message = 'Can not build the release';
}
