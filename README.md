# Resizr
Resizr is a lightweight image resizer / thumb generator for PHP. By default, it keeps the correct aspect ratio of the images, while keeping the maximum sizes below the ones you've set. It also compresses your images to speed up page loading times.

## Supported image extensions
Currently, Resizr supports the conversion of **JPEG, PNG, BMP and WEBP** images.

## How to use
Place `resizr.php` in a public accessible folder inside your web server. You will also need to create a `cache` folder in the same directory (or somewhere else, you can change this folder location in the user settings - see below). This folder **must have writing permissions**.

Now, in your HTML code refer your image URLs to the location of `resizr.php` file, along with one or more valid parameters appended to the URL query string.

_Example_

```html
<img src="resizr.php?src=images/myImage.png&w=500&h=500">
```

## URL parameters
Parameter|Type|Required|Default|Details
---|:---:|:---:|:---:|---
`src`|string|**yes**|-|Must be a relative or absolute URL for a supported image file.
`w`|int|no|200|Max desired width for the resized image.
`h`|int|no|200|Max desired height for the resized image.

## Cache and performance
When calling Resizr, the script will check if the image you are loading was already generated in the desired sizes. If so, Resizr will load the cached image, otherwise it will generate the new image and store it in the cache folder for future calls. This optimizes the script performance and reduces server load.

## User settings
If you want to change Resizr default settings, copy the `resizr-config.php` file to the same folder as the `resizr.php` file and edit it as you need.

Setting|Type|Default|Details
---|:---:|:---:|---
`cache_folder`|string|cache|Cache storage folder location. This folder is relative to the script file path. **It must have writing permissions!**
`default_width`|int|200|Default max width to use when the `w` URL parameter is not specified.
`default_height`|int|200|Default max height to use when the `h` URL parameter is not specified.
`jpeg_quality`|int|70|JPEG quality level to use while compressing images. Goes from **0** (worst quality) to **100** (best quality).
`png_compression`|int|3|PNG compression level to use while compressing transparent images. Goes from **0** (no compression - best quality) to **9** (heavy compression - worst quality).
`keep_transparency`|bool|true|Keep transparency in PNG and WEBP images (if false, all images will be **converted to JPEG**).
`fill_color`|array|[255, 255, 255]|If `keep_transparency` is disabled, the color to fill the alpha channel (array with R, G and B color values).