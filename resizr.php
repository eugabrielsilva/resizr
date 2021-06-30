<?php

    /*
        #####################################
                    RESIZR v1.0
                  by Gabriel Silva
        #####################################
    */

    // -- USER SETTINGS HERE --
    
    define('RESIZR_SETTINGS', [
        
        // Cache storage folder (must have write permissions)
        'cache_folder' => 'cache',

        // Allow images outside the web server (not recommended)
        'allow_external' => false,

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

    ]);

    // -- END OF USER SETTINGS --

    /*
        #####################################
        Do not edit below this line!
        This is where the magic happens...
        #####################################
    */

    // Validates cache folder
    if(!is_dir(RESIZR_SETTINGS['cache_folder'])) return errorResponse('Cache folder is not a valid directory.');
    if(!is_writable(RESIZR_SETTINGS['cache_folder'])) return errorResponse('Cache folder does not have writing permissions.');

    // Retrieves and validates source image URL parameter
    if(empty($_GET['src'])) return errorResponse('Source image URL is required.');
    $sourceImage = $_GET['src'];

    // Checks for external images
    if(!RESIZR_SETTINGS['allow_external']){
        if(parse_url($sourceImage, PHP_URL_SCHEME) && strtolower(parse_url($sourceImage, PHP_URL_HOST)) != strtolower($_SERVER['HTTP_HOST'])){
            return errorResponse('External images are not allowed.');
        }
    }

    // Retrieves image information
    list($origWidth, $origHeight, $type, $mime) = @getimagesize($sourceImage);
    if(!$type) return errorResponse('Source image is not a valid image file.');

    // Loads the image if supported
    switch($type){
        case IMAGETYPE_JPEG:
            $image = imagecreatefromjpeg($sourceImage);
            break;
        case IMAGETYPE_PNG:
            $image = imagecreatefrompng($sourceImage);
            break;
        case IMAGETYPE_BMP:
            $image = imagecreatefrombmp($sourceImage);
            break;
        case IMAGETYPE_WBMP:
            $image = imagecreatefromwbmp($sourceImage);
            break;
        case IMAGETYPE_WEBP:
            $image = imagecreatefromwebp($sourceImage);
            break;
        default:
            return parseImage($sourceImage, $mime);
    }

    // Retrieves width parameter
    if(!empty($_GET['w'])){
        $maxWidth = intval($_GET['w']);
    }else{
        $maxWidth = intval(RESIZR_SETTINGS['default_width']);
    }

    // Retrieves height parameter
    if(!empty($_GET['h'])){
        $maxHeight = intval($_GET['h']);
    }else{
        $maxHeight = intval(RESIZR_SETTINGS['default_height']);
    }

    // Gets the cached file location
    if(RESIZR_SETTINGS['keep_transparency'] && ($type == IMAGETYPE_PNG || $type == IMAGETYPE_WEBP)){
        $targetExtension = 'png';
        $mime = 'image/png';
    }else{
        $targetExtension = 'jpg';
        $mime = 'image/jpeg';
    }

    $targetImage = rtrim(RESIZR_SETTINGS['cache_folder'], '/') . '/' . md5("{$sourceImage}-w{$maxWidth}-h{$maxHeight}") . ".{$targetExtension}";

    // Checks if the thumb has already been generated
    if(file_exists($targetImage) && filemtime($targetImage) >= filemtime($sourceImage)) return parseImage($targetImage, $mime);

    // Calculates the new dimensions
    $ratio = min(($maxWidth / $origWidth), ($maxHeight / $origHeight));
    $newWidth = (int)$origWidth  * $ratio;
    $newHeight = (int)$origHeight * $ratio;

    // Creates the thumb
    $newImage = imagecreatetruecolor($newWidth, $newHeight);

    // Checks for transparency
    if($targetExtension == 'png'){
        $transparent = imagecolorallocatealpha($newImage, 0, 0, 0, 127);
        imagefill($newImage, 0, 0, $transparent);
        imagesavealpha($newImage, true);
        imagecopyresampled($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);
        imagepng($newImage, $targetImage, RESIZR_SETTINGS['png_compression']);
    }else{
        $fillColor = imagecolorallocate($newImage, ...RESIZR_SETTINGS['fill_color']);
        imagefilledrectangle($newImage, 0, 0, $newWidth, $newHeight, $fillColor);
        imagecopyresampled($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);
        imagejpeg($newImage, $targetImage, RESIZR_SETTINGS['jpeg_quality']);
    }

    // Cleans the memory
    imagedestroy($image);
    imagedestroy($newImage);

    // Redirects to the new image
    return parseImage($targetImage, $mime);

    /**
     * Returns an error with HTTP 500 response code.
     * @param string $message Error message to display.
     */
    function errorResponse(string $message){
        http_response_code(500);
        die($message);
    }

    /**
     * Loads an image in the browser.
     * @param string $filename Image URL to parse.
     * @param string $mime Image MIME type.
     */
    function parseImage(string $filaname, string $mime){
        header("Content-Type: {$mime}");
        echo file_get_contents($filaname);
        exit;
    }
