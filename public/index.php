<?php

require_once __DIR__ . '/../vendor/autoload.php';

define('TYPE', 'file'); // file or url
define('WIDTH', 630);
define('HEIGHT', 250);
define('RESIZE', true);

$app = new Phencil\App([
    'templates' => __DIR__ . '/../templates',
]);

// Home page
// Show upload form
$app->get('/', function() {
    return $this->render('upload', [
        'step' => 1,
        'type' => TYPE,
    ]);
});

// Upload action
// Try to fetch and load the image
$app->post('/upload', function() {
    try {
        // upload/download image from file/url
        $type = $this->getParam('type');
        if ($type == 'url' && ($url = $this->getParam('url'))) {
            $image = App\Image::download($url);
        } else if ($type == 'file' && ($file = $this->getFile('file'))) {
            $image = App\Image::upload($file);
        } else {
            throw new Exception("You have to select a file or an URL");
        }
    } catch (Exception $e) {
        // if an error occured, show error message and go back to upload page
        return $this->render('upload', [
            'error' => $e->getMessage(),
            'step'  => 1,
            'type'  => $type,
            'url'   => isset($url) ? $url : null,
        ]);
    }
    // if it's ok, go to next step
    $nextUrl = sprintf('/options/%s?width=%d&height=%d&resize=%d', $image->filename(), WIDTH, HEIGHT, RESIZE);
    $this->redirect($nextUrl);
});

// Options page
// Show option form
$app->get('/options/{filename}', function($filename) {
    return $this->render('options', [
        'height'       => $this->getParam('height'),
        'resize'       => $this->getParam('resize'),
        'step'         => 2,
        'urlNext'      => sprintf("/crop/%s", $filename),
        'urlPrevious'  => "/",
        'width'        => $this->getParam('width'),
    ]);
});

// Final page
// Show crop interface
$app->get('/crop/{filename}', function($filename) {
    $height = $this->getParam('height');
    $resize = (bool)$this->getParam('resize');
    $width  = $this->getParam('width');
    $image  = new App\Image($filename);
    if ($resize) {
        $widthImage  = $image->widthResized($width, $height);
        $heightImage = $image->heightResized($width, $height);
    } else {
        $heightImage = $image->height();
        $widthImage  = $image->width();
    }
    return $this->render('crop', [
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
$app->get('/download/{filename}', function ($filename) {
    $crop   = (bool)$this->getParam('crop');
    $height = $this->getParam('height');
    $resize = (bool)$this->getParam('resize');
    $width  = $this->getParam('width');
    $x      = (int)$this->getParam('x');
    $y      = (int)$this->getParam('y');
    $image  = new App\Image($filename);
    if ($resize) {
        $image->resize($width, $height);
    }
    if ($crop) {
        $image->crop($x, $y, $width, $height);
    }
    $this->sendFile($image->path(), $filename);
});

$app->run();
