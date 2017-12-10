<html>
    <head>
        <title>Resizr</title>
        <link href='http://fonts.googleapis.com/css?family=Pacifico' rel='stylesheet' type='text/css'>
        <link rel="stylesheet" type="text/css" href="/style.css" />
        <link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet">
    </head>
    <body>
        <a href="https://github.com/blat/resizr"><img style="position: absolute; top: 0; right: 0; border: 0;" src="https://s3.amazonaws.com/github/ribbons/forkme_right_white_ffffff.png" alt="Fork me on GitHub"></a>
        <div id="wrapper">
            <div id="header">
                <h1><a href="/">Resiz<span>r</span></a></h1>
            </div>
            <div id="main">
                <?php if (!empty($error)): ?><p class="error">Error: <?= $error ?>!</p><?php endif ?>
                <ol id="steps">
                    <li <?php if ($step >= 1): ?>class="current"<?php endif ?>>Upload</li>
                    <li <?php if ($step >= 2): ?>class="current"<?php endif ?>>Options</li>
                    <li <?php if ($step == 3): ?>class="current"<?php endif ?>>Download</li>
                </ol>
                <?= $this->section('content') ?>
            </div>
            <div id="footer">
                <a href="http://github.com/blat/resizr">Resizr</a> is tool built to help the <a href="http://www.desinvolt.fr">D&eacute;sinvolt</a> team to easily resize images.
            </div>
        </div>
        <script type="text/javascript" src="//code.jquery.com/jquery-1.10.1.min.js"></script>
        <script type="text/javascript" src="//code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
        <script type="text/javascript" src="/app.js"></script>
    </body>
</html>
