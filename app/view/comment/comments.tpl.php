<!-- THIS IS A SHORT TAG COPY IT FROM HERE <?=$x?> -->

<div class='question-list'>


<div class='sidebar'>
<p class='votes'><span class='number'><?=$comment->score?></span><br><span class='word'>votes</span></p>
<p class='answers'><span class='number'><?=$numberOfAnswers?></span><br><span class='word'>answers</span></p>
</div>

<div class='main'>


<p class='title'><a href='<?=$commentUrl?>/<?=$comment->id?>'><?=$comment->title?></a></p>

<p class='tag-row'>
<?php foreach($tags as $tag): ?>
<a href='<?=$taggedUrl?>/<?=$tag?>' class='tag'><?=$tag?></a>
<?php endforeach; ?>
</p>
<p class='bottom-row'><a href='<?=$userUrl?>/<?=$user['id']?>'><?=$user['name']?></a>, <span class='timestamp' title='<?=$rawTimestamp?>'><?=$timestamp?></span></p>

</div>






</div>
