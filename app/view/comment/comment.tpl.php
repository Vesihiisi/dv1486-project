<!-- THIS IS A SHORT TAG COPY IT FROM HERE <?=$x?> -->
<div class='comment' id='<?=$commentId?>'>
<a href='<?=$voteUrl?>/<?=$comment->id?>/up'><i class="fa fa-arrow-up"></i></a> <?=$comment->score?> 
<a href='<?=$voteUrl?>/<?=$comment->id?>/down'><i class="fa fa-arrow-down"></i></a>
<p><?=$comment->content?></p> — 
<p><a href='<?=$userUrl?>/<?=$userData['id']?>'><?=$userData["name"]?></a>, <span class='timestamp' title='<?=$rawTimestamp?>'><?=$timestamp?></span>
<?php if($editUrl != null) : ?>
<a href='<?=$editUrl?>/<?=$comment->id?>'>[edit]</a>
<?php endif; ?>


</p>
</div>
