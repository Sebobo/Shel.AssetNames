# Asset names for Neos CMS

Installing this package will change the naming behavior for files when publishing assets in Neos CMS.
By default Neos CMS will create a symlink in the public `Web` folder to the file stored in the private `Data/Persistent`
folder. These symlinks to images and other assets will have the filenames they got when they were uploaded.

New behavior: The symlinks to these assets will have a filename based on their `title` which can be set in the `Media Module`. 

## Advantages

* You can optimize the filenames for SEO purposes in the `Media Module`.
* The actual file name stored in the database is not changed.
* Old filenames will still work until the `Web/Resources` folder is cleaned up.

## Caveats

* The actual file name stored in the database is not changed. Therefore there is an additional query to the database
to generate the file name when the public uri is requested. This might be optimized in the future.

## How to use it

1. Install the package via composer `composer require --no-update shel/asset-names` in your site package.
2. Run `composer update` in your project's root folder.
2. Run `./flow resource:publish`

## What the package cannot do yet

It currently doesnt work when not using the provided symlink target.
 
If you use the copy target or some cloud based filesystem you can extend those targets the same way and provide
the change as PR to this package.

## Future plans

* This feature should be in the Neos CMS core at some point. 
* This package is meant to find the best implementation and also support older Neos versions until it's part of the core.

## Contributions

Contributions are very welcome! 

Please create detailed issues and PRs.  

**If you use this package and want to support or speed up it's development, [get in touch with me](mailto:assetnames@helzle.it).**

Or you can also support me directly via [patreon](https://www.patreon.com/shelzle).                                  

## License

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
