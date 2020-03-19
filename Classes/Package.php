<?php
declare(strict_types=1);

namespace Shel\AssetNames;

/*
 * This file is part of the Shel.AssetNames package.
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Neos\Flow\Core\Bootstrap;
use Neos\Flow\Package\Package as BasePackage;
use Neos\Media\Domain\Service\AssetService;
use Shel\AssetNames\Service\AssetPublishService;

/**
 * The Neos Package
 */
class Package extends BasePackage
{
    /**
     * @param Bootstrap $bootstrap The current bootstrap
     * @return void
     */
    public function boot(Bootstrap $bootstrap): void
    {
        $dispatcher = $bootstrap->getSignalSlotDispatcher();

        $dispatcher->connect(
            AssetService::class, 'assetUpdated',
            AssetPublishService::class, 'republishAsset'
            );
    }
}
