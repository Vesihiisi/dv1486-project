<h1><?=$title?></h1>
<?php foreach ($options as $option) : ?>
    <a href='<?=$adminUrl?>/<?=$option?>'><?=$option?></a>
<?php endforeach; ?>
