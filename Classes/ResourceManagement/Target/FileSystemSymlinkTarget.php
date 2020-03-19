<?php
declare(strict_types=1);

namespace Shel\AssetNames\ResourceManagement\Target;

/*
 * This file is part of the Shel.AssetNames package.
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Neos\Flow\ResourceManagement\ResourceMetaDataInterface;

/**
 * A target which publishes resources by creating symlinks with the filename based on the files title.
 */
class FileSystemSymlinkTarget extends \Neos\Flow\ResourceManagement\Target\FileSystemSymlinkTarget
{
    use AssetNameTrait;

    /**
     * @inheritDoc
     */
    protected function getRelativePublicationPathAndFilename(ResourceMetaDataInterface $object) : string
    {
        $filename = $this->getTitleBasedFilename($object);

        if ($object->getRelativePublicationPath() !== '') {
            $pathAndFilename = $object->getRelativePublicationPath() . $filename;
        } else {
            if ($this->subdivideHashPathSegment) {
                $sha1Hash = $object->getSha1();
                $pathAndFilename = $sha1Hash[0] . '/' . $sha1Hash[1] . '/' . $sha1Hash[2] . '/' . $sha1Hash[3] . '/' . $sha1Hash . '/' . $filename;
            } else {
                $pathAndFilename = $object->getSha1() . '/' . $filename;
            }
        }
        return $pathAndFilename;
    }
}
