<?php
declare(strict_types=1);

namespace Shel\AssetNames\Service;

/*
 * This file is part of the Neos.Media package.
 *
 * (c) Contributors of the Neos Project - www.neos.io
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Neos\Flow\Annotations as Flow;
use Neos\Flow\ResourceManagement\ResourceManager;
use Neos\Flow\ResourceManagement\Target\Exception;
use Neos\Media\Domain\Model\AssetInterface;
use Neos\Media\Domain\Model\AssetVariantInterface;
use Neos\Media\Domain\Model\Thumbnail;
use Neos\Media\Domain\Model\VariantSupportInterface;
use Neos\Media\Domain\Repository\ThumbnailRepository;

/**
 * This service allows republishing all variants and thumbnails of an asset.
 * This way a change to the filename based on its title will work properly.
 *
 * @Flow\Scope("singleton")
 */
class AssetPublishService
{

    /**
     * @Flow\Inject
     * @var ResourceManager
     */
    protected $resourceManager;

    /**
     * @Flow\Inject
     * @var ThumbnailRepository
     */
    protected $thumbnailRepository;

    /**
     * Publishes the resource and all variants thumbnails for an image when it was changed.
     * This is necessary to make sure that all symlinks are updated
     */
    public function republishAsset(AssetInterface $asset): void
    {
        $collection = $this->resourceManager->getCollection($asset->getResource()->getCollectionName());

        if (!$collection) {
            return;
        }

        if ($asset instanceof AssetVariantInterface) {
            $variants = [$asset];
        } else {
            $variants = $asset instanceof VariantSupportInterface ? $asset->getVariants() : [];
            $variants[] = $asset;
        }

        $thumbnails = array_merge(...array_map(function ($variant) {
            /** @noinspection PhpUndefinedMethodInspection */
            return $this->thumbnailRepository->findByOriginalAsset($variant)->toArray();
        }, $variants));

        try {
            $collection->getTarget()->publishResource($asset->getResource(), $collection);

            /** @var Thumbnail $thumbnail */
            foreach ($thumbnails as $thumbnail) {
                if ($thumbnail->getResource()) {
                    $collection->getTarget()->publishResource($thumbnail->getResource(), $collection);
                }
            }
        } catch(Exception $e) {}
    }

    /**
     * Publishes a thumbnail.
     * This is necessary as in certain conditions thumbnails with custom titles from this package
     * are not properly symlinked after publishing.
     */
    public function publishThumbnail(Thumbnail $thumbnail): void
    {
        if (!$thumbnail->getResource()) {
            return;
        }
        $collection = $this->resourceManager->getCollection($thumbnail->getResource()->getCollectionName());
        if (!$collection) {
            return;
        }
        $collection->getTarget()->publishResource($thumbnail->getResource(), $collection);
    }
}
