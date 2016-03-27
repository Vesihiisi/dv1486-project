<div class='front-page-box'>
<h3 class='background-orange'><?=$title?></h3>
<?php foreach($tags as $tag): ?>
<p><a href='<?=$url?>/<?=$tag->name?>'><?=$tag->name?> (<?=$tag->count?>)</a></p>
<?php endforeach; ?>
</div>
