<?php

require_once __DIR__ . '/../vendor/autoload.php';

define('TYPE', 'file'); // file or url
define('WIDTH', 630);
define('HEIGHT', 250);
define('RESIZE', true);

$app = new Laravel\Lumen\Application(dirname(__DIR__));

// Home page
// Show upload form
$app->router->get('/', function() {
    return view('upload', [
        'step' => 1,
        'type' => TYPE,
    ]);
});

// Upload action
// Try to fetch and load the image
$app->router->post('/upload', function() {
    try {
        // upload/download image from file/url
        $type = request()->input('type');
        if ($type == 'url' && ($url = request()->input('url'))) {
            $image = App\Image::download($url);
        } else if ($type == 'file' && ($file = request()->file('file'))) {
            $image = App\Image::upload($file);
        } else {
            throw new Exception("You have to select a file or an URL");
        }
    } catch (Exception $e) {
        // if an error occured, show error message and go back to upload page
        return view('upload', [
            'error' => $e->getMessage(),
            'step'  => 1,
            'type'  => $type,
            'url'   => isset($url) ? $url : null,
        ]);
    }
    // if it's ok, go to next step
    $nextUrl = sprintf('/options/%s?width=%d&height=%d&resize=%d', $image->filename(), WIDTH, HEIGHT, RESIZE);
    return redirect($nextUrl);
});

// Options page
// Show option form
$app->router->get('/options/{filename}', function($filename) {
    return view('options', [
        'height'       => request()->input('height'),
        'resize'       => request()->input('resize'),
        'step'         => 2,
        'urlNext'      => sprintf("/crop/%s", $filename),
        'urlPrevious'  => "/",
        'width'        => request()->input('width'),
    ]);
});

// Final page
// Show crop interface
$app->router->get('/crop/{filename}', function($filename) {
    $height = request()->input('height');
    $resize = (bool)request()->input('resize');
    $width  = request()->input('width');
    $image  = new App\Image($filename);
    if ($resize) {
        $widthImage  = $image->widthResized($width, $height);
        $heightImage = $image->heightResized($width, $height);
    } else {
        $heightImage = $image->height();
        $widthImage  = $image->width();
    }
    return view('crop', [
        'height'       => $height,
        'heightImage'  => $heightImage,
        'resize'       => $resize,
        'step'         => 3,
        'urlImage'     => sprintf("/download/%s?width=%d&height=%d&resize=%d", $filename, $width, $height, $resize),
        'urlNext'      => sprintf("/download/%s", $filename),
        'urlPrevious'  => sprintf("/options/%s?width=%d&height=%d&resize=%d", $filename, $width, $height, $resize),
        'width'        => $width,
        'widthImage'   => $widthImage,
    ]);
});

// Download action
// Get resized/cropped image
$app->router->get('/download/{filename}', function ($filename) {
    $crop   = (bool)request()->input('crop');
    $height = request()->input('height');
    $resize = (bool)request()->input('resize');
    $width  = request()->input('width');
    $x      = (int)request()->input('x');
    $y      = (int)request()->input('y');
    $image  = new App\Image($filename);
    if ($resize) {
        $image->resize($width, $height);
    }
    if ($crop) {
        $image->crop($x, $y, $width, $height);
    }
    return response()->download($image->path(), $filename);
});

$app->run();
