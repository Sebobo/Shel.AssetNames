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
use Neos\Flow\Persistence\Doctrine\QueryResult;
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
     * @param AssetInterface $asset
     * @noinspection PhpUnused
     */
    public function republishAsset(AssetInterface $asset): void
    {
        if ($asset instanceof AssetVariantInterface) {
            $asset = $asset->getOriginalAsset();
        }

        if (!$asset->isInUse()) {
            return;
        }

        $collection = $this->resourceManager->getCollection($asset->getResource()->getCollectionName());

        if (!$collection) {
            return;
        }

        $variants = $asset instanceof VariantSupportInterface ? $asset->getVariants() : [];
        $variants[] = $asset;

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
}
