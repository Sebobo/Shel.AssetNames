<?php
namespace Shel\AssetNames\ResourceManagement\Target;

/*
 * This file is part of the Shel.AssetNames package.
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Neos\Flow\ResourceManagement\ResourceMetaDataInterface;
use Cocur\Slugify\Slugify;

/**
 * Provides methods to get an assets filename based on it's title instead the filename stored when the
 * asset was uploaded.
 *
 * Using this trait requires an injected AssetRepository.
 */
trait AssetNameTrait
{

    /**
     * Returns a filename based on the objects title if set, and the original filename if not.
     *
     * @param ResourceMetaDataInterface $object
     * @return string
     */
    protected function getTitleBasedFilename(ResourceMetaDataInterface $object): string
    {
        $filename = $object->getFilename();
        $asset = $this->assetRepository->findOneByResourceSha1($object->getSha1());

        if ($asset !== null) {
            $slugify = new Slugify();
            if (!empty($asset->getTitle())) {
                $filename = $slugify->slugify($asset->getTitle()) . '.' . $asset->getFileExtension();
            }
        }

        return $filename;
    }
}
