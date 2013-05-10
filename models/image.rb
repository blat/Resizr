require 'fileutils'
require 'open-uri'
require 'RMagick'

class Image

    @path
    @image

    # Image's constructor
    #
    # @param filename
    def initialize filename
        @path = Image._path filename
        self._load
    end

    # Resize the current image
    #
    # @param [Integer] width
    # @param [Integer] height
    def resize width, height
        @path = self._path_resized width, height
        if not File.exist? @path then
            width = self.width_resized width, height
            height = self.height_resized width, height
            @image = @image.resize width, height
            self._save
        else
            self._load
        end
    end

    # Crop the current image
    #
    # @param [Integer] x
    # @param [Integer] y
    # @param [Integer] width
    # @param [Integer] height
    def crop x, y, width, height
        @path = self._path_cropped x, y, width, height
        if not File.exist? @path then
            @image = @image.crop x, y, width, height
            self._save
        else
            self._load
        end
    end

    # Get the image width
    #
    # @return [Integer]
    def width
        return @image.columns
    end

    # Get the image height
    #
    # @return [Integer]
    def height
        return @image.rows
    end

    # Get the image width after resizing
    #
    # @param [Integer] width
    # @param [Integer] height
    # @return [Integer]
    def width_resized width, height
        ratio = @image.columns/@image.rows
        if ratio > width/height then
            width = @image.columns * height / @image.rows
        end
        return width
    end

    # Get the image height after resizing
    #
    # @param [Integer] width
    # @param [Integer] height
    # @return [Integer]
    def height_resized width, height
        ratio = @image.columns/@image.rows
        if ratio < width/height then
            height = @image.rows * width / @image.columns
        end
        return height
    end

    # Get the image path
    #
    # @return [String]
    def path
        return @path
    end

    # Get the image filename
    #
    # @return [String]
    def filename
        return File.basename @path
    end

    # Get the image type
    #
    # @return [String]
    def mime_type
        extension = File.extname @path
        type = extension.sub /\./, ''
        if type.eql? 'jpg' then
            type = 'jpeg'
        end
        return "image/#{type}"
    end

    # Upload a file
    #
    # @param [String] file
    # @return [Image]
    def self.upload file
        filename = Image._filename file[:filename]
        path = Image._path filename
        begin
            FileUtils.cp file[:tempfile].path, path
        rescue
            raise "Unable to upload this file"
        end
        return Image.new filename
    end

    # Download an URL
    #
    # @param [String] url
    # @return [Image]
    def self.download url
        filename = Image._filename url
        path = Image._path filename
        begin
            input = open url
            output = open path, 'wb'
            output.write input.read
            output.close
        rescue
            raise "Unable to fetch this url"
        end
        return Image.new filename
    end

    # Load the image
    def _load
        begin
            image = Magick::Image.read @path
            @image = image.first
        rescue
            raise "Unable to load this image"
        end
    end

    # Save the image on the filesystem
    def _save
        @image.write(@path){self.density="72x72"}
    end

    # Get the path for the resized image
    #
    # @param [Integer] width
    # @param [Integer] height
    # @return [String]
    def _path_resized width, height
        extension = File.extname @path
        basename = File.basename @path, extension
        return Image._path "#{basename}-#{width}x#{height}#{extension}"
    end

    # Get the path for the cropped image
    #
    # @param [Integer] x
    # @param [Integer] y
    # @return [String]
    def _path_cropped x, y, width, height
        extension = File.extname @path
        basename = File.basename @path, extension
        return Image._path "#{basename}-#{width}x#{height}-#{x}x#{y}#{extension}"
    end

    # Get the complete path for a given filename
    #
    # @param [String] filename
    # @return [String]
    def self._path filename
        return "#{File.dirname(__FILE__)}/../tmp/#{filename}"
    end

    # Generate a filename
    #
    # @param [String] path
    # @return [String]
    def self._filename path
        extension = File.extname path
        return "#{Time.now.to_i}#{extension}"
    end

end
