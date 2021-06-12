<x-layout>
    @if (isset($error))
    <x-slot name="error">{{ $error }}</x-slot>
    @endif
    <x-slot name="step">{{ $step }}</x-slot>

    <form action="{{ $urlNext }}">
        <p>
            <label class="label" for="width">Width <span>(in px)</span></label>
            <input type="text" name="width" id="width" value="{{ $width }}" class="text" />
        </p>
        <p>
            <label class="label" for="height">Height <span>(in px)</span></label>
            <input type="text" name="height" id="height" value="{{ $height }}" class="text" />
        </p>
        <p>
        <input type="checkbox" name="resize" id="resize" @if ($resize) checked="checked" @endif class="checkbox" />
            <label for="resize" class="checkbox">Adjust image size?</label>
        </p>
        <p class="next">
            <a class="button" href="{{ $urlPrevious }}"><i class='fa fa-chevron-left'></i> Previous</a>
            <button type="submit">Next <i class='fa fa-chevron-right'></i></button>
        </p>
    </form>
</x-layout>
