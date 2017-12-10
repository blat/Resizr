<?php

namespace App;

class Image
{
    private $_path;
    private $_image;

    /**
     * Image's constructor
     *
     * @param $filename
     */
    public function __construct($filename)
    {
        $this->_path = self::_path($filename);
        $this->_load();
    }

    /**
     * Resize the current image
     *
     * @param Integer $width
     * @param Integer $height
     */
    public function resize($width, $height)
    {
        $this->_path = $this->_pathResized($width, $height);
        if (!file_exists($this->_path)) {
            $width = $this->widthResized($width, $height);
            $height = $this->heightResized($width, $height);
            $this->_image->resize($width, $height);
            $this->_save();
        } else {
            $this->_load();
        }
    }

    /**
     * Crop the current image
     *
     * @param Integer $x
     * @param Integer $y
     * @param Integer $width
     * @param Integer] $height
     */
    public function crop($x, $y, $width, $height)
    {
        $this->_path = $this->_pathCropped($x, $y, $width, $height);
        if (!file_exists($this->_path)) {
            $this->_image->crop($width, $height, $x, $y);
            $this->_save();
        } else {
            $this->_load();
        }
    }

    /**
     * Get the image width
     *
     * @return Integer
     */
    public function width()
    {
        return $this->_image->width();
    }

    /**
     * Get the image height
     *
     * @return Integer
     */
    public function height()
    {
        return $this->_image->height();
    }

    /**
     * Get the image width after resizing
     *
     * @param Integer $width
     * @param Integer $height
     * @return Integer
     */
    public function widthResized($width, $height)
    {
        $ratio = $this->width() / $this->height();
        if ($ratio > $width/$height) {
            $width = $this->width() * $height / $this->height();
        }
        return $width;
    }

    /**
     * Get the image height after resizing
     *
     * @param Integer $width
     * @param Integer $height
     * @return Integer
     */
    public function heightResized($width, $height)
    {
        $ratio = $this->width() / $this->height();
        if ($ratio < $width/$height) {
            $height = $this->height() * $width / $this->width();
        }
        return $height;
    }

    /**
     * Get the image path
     *
     * @return String
     */
    public function path()
    {
        return $this->_path;
    }

    /**
     * Get the image filename
     *
     * @return String
     */
    public function filename()
    {
        return basename($this->_path);
    }

    /**
     * Upload a file
     *
     * @param String $file
     * @return Image
     */
    public static function upload($file)
    {
        if (!$file->isValid()) {
            throw new \Exception("Unable to upload this file");
        }
        $filename = self::_filename($file->getClientOriginalName());
        $path = self::_path($filename);
        $file->move(dirname($path), basename($path));
        return new self($filename);
    }

    /**
     * Download an URL
     *
     * @param String $url
     * @return Image
     */
    public static function download($url)
    {
        $filename = self::_filename($url);
        $path = self::_path($filename);
        try {
            $client = new \GuzzleHttp\Client();
            $data = $client->get($url)->getBody();
            file_put_contents($path, $data);
        } catch (\Exception $e) {
            throw new \Exception("Unable to fetch this url");
        }
        return new self($filename);
    }

    /**
     * Load the image
     */
    private function _load()
    {
        try {
            ini_set('memory_limit', '256M');
            $this->_image = \Intervention\Image\ImageManagerStatic::make($this->_path);
        } catch (\Exception $e) {
            throw new \Exception("Unable to load this image");
        }
    }

    /**
     * Save the image on the filesystem
     */
    private function _save()
    {
        $this->_image->save($this->_path);
    }

    /**
     * Get the path for the resized image
     *
     * @param Integer $width
     * @param Integer $height
     * @return String
     */
    private function _pathResized($width, $height)
    {
        $extension = pathinfo($this->_path, PATHINFO_EXTENSION);
        $basename = basename($this->_path, '.' . $extension);
        return self::_path(sprintf("%s-%dx%d.%s", $basename, $width, $height, $extension));
    }

    /**
     * Get the path for the cropped image
     *
     * @param Integer $x
     * @param Integer $y
     * @return String
     */
    private function _pathCropped($x, $y, $width, $height)
    {
        $extension = pathinfo($this->_path, PATHINFO_EXTENSION);
        $basename = basename($this->_path, '.' . $extension);
        return self::_path(sprintf("%s-%dx%d-%dx%d.%s", $basename, $width, $height, $x, $y, $extension));
    }

    /**
     * Get the complete path for a given filename
     *
     * @param String $filename
     * @return String
     */
    private static function _path($filename)
    {
        return __DIR__ . '/../tmp/' . $filename;
    }

    /**
     * Generate a filename
     *
     * @param String $path
     * @return String
     */
    private static function _filename($path)
    {
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        return sprintf("%d.%s", time(), $extension);
    }

}
