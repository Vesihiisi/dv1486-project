<h1><?=$title?></h1>
<div class='tags-container'>
<?php foreach ($tags as $tag) : ?>
<div class='tag-overview'>
<p><a href='<?=$url?>/<?=$tag->name?>' class='tag'><?=$tag->name?></a>× <?=$tag->count?></p>
<p class='description'><?=$tag->description?></p>
    </div>
<?php endforeach; ?>
</div>
