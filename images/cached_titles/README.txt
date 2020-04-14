title.php creates the images in this folder. The images are automatically created based off the title and font
settings you set in congif.php. That is a slow process, so instead of creating the image fresh every time its
viewed it will be created and cached here. Subsequent views will load this cached version instead.

Under normal circumstances you shouldn't have to clear the cache. If you change the code or somehow this
directory gets overwhelmed you can delete everything in this directory safely.

The naming convention for the images is <title>_<fontsize>_<fontname>.png