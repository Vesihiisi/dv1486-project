<div class='front-page-box'>
<h3 class='background-brown'><?=$title?></h3>
<?php foreach($comments as $comment): ?>
    <p><a href='<?=$url?>/<?=$comment->id?>'><?=$comment->title?></a></p>
<?php endforeach; ?>
</div>
