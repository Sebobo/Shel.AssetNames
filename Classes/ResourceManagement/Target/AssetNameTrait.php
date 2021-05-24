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

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Exception;
use Neos\Flow\ResourceManagement\ResourceMetaDataInterface;
use Neos\Media\Domain\Model\AssetInterface;
use Neos\Media\Domain\Model\ImageInterface;
use Neos\Media\Domain\Model\Thumbnail;
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
     * @Flow\InjectConfiguration(package="Shel.AssetNames", path="expression")
     * @var string
     */
    protected $assetNameExpression;

    /**
     * @Flow\InjectConfiguration(package="Shel.AssetNames", path="enabled")
     * @var bool
     */
    protected $enabled;

    /**
     * @Flow\Inject
     * @var \Neos\Media\Domain\Repository\AssetRepository
     */
    protected $assetRepository;

    /**
     * @Flow\Inject
     * @var \Neos\Media\Domain\Repository\ThumbnailRepository
     */
    protected $thumbnailRepository;

    /**
     * @Flow\Inject(lazy=false)
     * @var \Neos\Eel\CompilingEvaluator
     */
    protected $eelEvaluator;

    /**
     * @var Slugify
     */
    protected $slugify;

    /**
     * Returns a filename based on the objects title if set, and the original filename if not.
     *
     * @param ResourceMetaDataInterface $object
     * @return string
     */
    protected function getTitleBasedFilename(ResourceMetaDataInterface $object): string
    {
        $filename = $object->getFilename();

        if (!$this->enabled) {
            return $filename;
        }

        /** @var AssetInterface $asset */
        $asset = $this->assetRepository->findOneByResourceSha1($object->getSha1());
        $width = 0;
        $height = 0;

        if ($asset === null) {
            $query = $this->thumbnailRepository->createQuery();
            $query->matching($query->equals('resource.sha1', $object->getSha1()))->setLimit(1);
            /** @var Thumbnail $thumbnail */
            $thumbnail = $query->execute()->getFirst();

            if ($thumbnail !== null) {
                $asset = $thumbnail->getOriginalAsset();
                $width = $thumbnail->getWidth();
                $height = $thumbnail->getHeight();
            }
        }

        if (($asset !== null) && !empty($asset->getTitle())) {
            try {
                if ($asset instanceof ImageInterface && (!$width || !$height)) {
                    $width = $asset->getWidth();
                    $height = $asset->getHeight();
                }

                $filename = \Neos\Eel\Utility::evaluateEelExpression(
                    $this->assetNameExpression,
                    $this->eelEvaluator,
                    [
                        'asset' => $asset,
                        'width' => $width,
                        'height' => $height,
                    ],
                    []
                );
            } catch (Exception $e) {
                $filename = $asset->getTitle();
            } finally {
                if (!$this->slugify) {
                    $this->slugify = new Slugify();
                }

                $pathInfo = pathinfo($object->getFilename());
                $fileExtension = $pathInfo['extension'] ?? $asset->getFileExtension();
                $filename = $this->slugify->slugify($filename) . '.' . $fileExtension;
            }
        }

        return $filename;
    }
}
