require 'erb'
require 'sinatra'
require './models/image'

set :type,   'file' # file or url
set :width,  640
set :height, 250
set :resize, true

# Home page
# Show upload form
get '/' do
    @type = settings.type
    @step = 1
    erb :upload
end

# Upload action
# Try to fetch and load the image
post '/upload' do
    begin
        # upload/download image from file/url
        @type = params['type']
        if @type.eql? 'url' and not params['url'].empty? then
            @url = params['url']
            image = Image.download @url
        elsif @type.eql? 'file' and not params['file'].nil? then
            image = Image.upload params['file']
        else
            raise "You have to select a file or an URL"
        end
    rescue Exception => e
        # if an error occured, show error message and go back to upload page
        @error = e.message
        @step = 1
        erb :upload
    else
        # if it's ok, go to next step
        redirect "/options/#{image.filename}?width=#{settings.width}&height=#{settings.height}&resize=#{settings.resize}"
    end
end

# Options page
# Show option form
get '/options/:filename' do |filename|
    @width = params['width'].to_i
    @height = params['height'].to_i
    @resize = params['resize'].eql? 'true'
    @url_previous = "/"
    @url_next = "/crop/#{filename}"
    @step = 2
    erb :options
end

# Final page
# Show crop interface
get '/crop/:filename' do |filename|
    @width = params['width'].to_i
    @height = params['height'].to_i
    @resize = params['resize'].eql? 'on'
    image = Image.new filename
    if @resize then
        @width_image = image.width_resized @width, @height
        @height_image = image.height_resized @width, @height
    else
        @height_image = image.height
        @width_image = image.width
    end
    @url_image = "/download/#{filename}?width=#{@width_image}&height=#{@height_image}&resize=#{@resize}"
    @url_previous = "/options/#{filename}?width=#{@width}&height=#{@height}&resize=#{@resize}"
    @url_next  = "/download/#{filename}"
    @step = 3
    erb :crop
end

# Download action
# Get resized/cropped image
get '/download/:filename' do |filename|
    y = params['y'].to_i
    x = params['x'].to_i
    width = params['width'].to_i
    height = params['height'].to_i
    resize = params['resize'].eql? 'true'
    crop = params['crop'].eql? 'true'
    image = Image.new filename
    if resize then
        image.resize width, height
    end
    if crop then
        image.crop x, y, width, height
    end
    send_file image.path, :filename => filename, :type => image.mime_type
end
