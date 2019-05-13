<?php
namespace Shel\AssetNames\ResourceManagement\Target;

use Neos\Flow\Annotations as Flow;
use Cocur\Slugify\Slugify;
use Neos\Flow\ResourceManagement\ResourceMetaDataInterface;
use Neos\Media\Domain\Repository\AssetRepository;

/**
 * A target which publishes resources by creating symlinks with the filename based on the files title.
 */
class FileSystemSymlinkTarget extends \Neos\Flow\ResourceManagement\Target\FileSystemSymlinkTarget
{
    /**
     * @Flow\Inject
     * @var AssetRepository
     */
    protected $assetRepository;

    /**
     * @inheritDoc
     */
    protected function getRelativePublicationPathAndFilename(ResourceMetaDataInterface $object)
    {
        $filename = $object->getFilename();

        $asset = $this->assetRepository->findOneByResourceSha1($object->getSha1());

        if ($asset) {
            $slugify = new Slugify();
            if (!empty($asset->getTitle())) {
                $filename = $slugify->slugify($asset->getTitle()) . '.' . $asset->getFileExtension();
            }
        }

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
