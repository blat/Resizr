<x-layout>
    @if (isset($error))
    <x-slot name="error">{{ $error }}</x-slot>
    @endif
    <x-slot name="step">{{ $step }}</x-slot>

    <form action="/upload" method="post" enctype="multipart/form-data">
        <p>
            <label class="label">Select a source type</label>
            <input type="radio" id="type-file" name="type" value="file" @if ($type == 'file') checked="checked" @endif class="radio" />
            <label for="type-file" class="radio">File</label>
            <input type="radio" id="type-url" name="type" value="url" @if ($type == 'url') checked="checked" @endif class="radio" />
            <label for="type-url" class="radio">URL</label>
        </p>
        <p id="input-file">
            <label class="label" for="file" id="label-file">File</label>
            <input type="file" name="file" id="file" />
        </p>
        <p id="input-url">
            <label class="label" for="url" class="text" id="label-url">URL</label>
            <input type="text" name="url" id="url" value="{{ $url ?? '' }}" class="text" />
        </p>
        <p class="next">
            <button type="submit">Next <i class='fa fa-chevron-right'></i></button>
        </p>
    </form>
</x-layout>

