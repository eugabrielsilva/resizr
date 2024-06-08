<?php

/*
    #####################################
                RESIZR v2.0
              by Gabriel Silva
    #####################################

    Copy this file to the same folder as resizr.php.
    Edit the following lines to change the default settings of Resizr.
*/

return [
    // Cache storage folder (must have write permissions)
    'cache_folder' => __DIR__ . '/cache',

    // Default max width when URL parameter is not specified
    'default_width' => 200,

    // Default max height when URL parameter is not specified
    'default_height' => 200,

    // JPEG quality level from 0 (worst quality) to 100 (best quality)
    'jpeg_quality' => 70,

    // PNG compression level from 0 (no compression) to 9 (heavy compression)
    'png_compression' => 3,

    // Keep transparency in PNG and WEBP images (if false, all images will be converted to JPG)
    'keep_transparency' => true,

    // If keep_transparency is disabled, the color to fill the alpha channel [R, G, B]
    'fill_color' => [255, 255, 255]
];
