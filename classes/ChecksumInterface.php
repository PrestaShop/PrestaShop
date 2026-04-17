<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
interface ChecksumInterface
{
    /**
     * @param object $object Checksum target
     *
     * @return string
     */
    public function generateChecksum($object);
}
