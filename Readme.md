# Asset names for Neos CMS

Installing this package will change the naming behavior for files when publishing assets in Neos CMS.
By default Neos CMS will create a symlink in the public `Web` folder to the file stored in the private `Data/Persistent`
folder. These symlinks to images and other assets will have the filenames they got when they were uploaded.

New behavior: The symlinks to these assets will have a filename based on their `title` which can be set in the `Media Module`. 

## Advantages

* You can optimize the filenames for SEO purposes in the `Media Module`.
* Old filenames will still work until the `Web/Resources` folder is cleaned up.

## How to use it

1. Install the package via composer `composer require --no-update shel/asset-names` in your site package.
2. Run `composer update` in your project's root folder.
2. Run `./flow resource:publish`

## What the package cannot do yet

It doesn't work when not using the provided symlink target. 
If you use the copy target or some cloud based filesystem you can extend those targets the same way and provide
the change as PR to this package.
