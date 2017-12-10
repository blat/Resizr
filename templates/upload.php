<?php $this->layout('layout', [
    'error' => isset($error) ? $error : null,
    'step'  => $step,
]) ?>

<form action="/upload" method="post" enctype="multipart/form-data">
    <p>
        <label class="label">Select a source type</label>
        <input type="radio" id="type-file" name="type" value="file" <?php if ($type == 'file'): ?>checked="checked"<?php endif ?> class="radio" />
        <label for="type-file" class="radio">File</label>
        <input type="radio" id="type-url" name="type" value="url" <?php if ($type == 'url'): ?>checked="checked"<?php endif ?> class="radio" />
        <label for="type-url" class="radio">URL</label>
    </p>
    <p id="input-file">
        <label class="label" for="file" id="label-file">File</label>
        <input type="file" name="file" id="file" />
    </p>
    <p id="input-url">
        <label class="label" for="url" class="text" id="label-url">URL</label>
        <input type="text" name="url" id="url" value="<?= isset($url) ? $url : '' ?>" class="text" />
    </p>
    <p class="next">
        <button type="submit">Next <i class='fa fa-chevron-right'></i></button>
    </p>
</form>
