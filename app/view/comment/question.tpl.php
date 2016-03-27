<div class='question' id='<?=$questionId?>'>

<h2 class='question-title'><?=$questionTitle?></h2>

<div class='sidebar'>
<p><a href='<?=$voteUrl?>/<?=$questionId?>/up'><i class="fa fa-caret-up" title="Upvote"></i></a></p>
<p class='score'><?=$score?></p>
<p><a href='<?=$voteUrl?>/<?=$questionId?>/down'><i class="fa fa-caret-down" title="Downvote"></i></a></p>
<p><a href='<?=$commentUrl?>/<?=$questionId?>'><i class="fa fa-comment" title="Comment"></i></a></p>
<p><?php if (isset($editUrl) && $editUrl != null) : ?>
<a href='<?=$editUrl?>/<?=$questionId?>'><i class="fa fa-pencil" title='Edit'></i></a>
<?php endif; ?></p>
</div>

<div class='question-content'>

<?=$question?>


<p>
<?php foreach($tags as $tag): ?>
<a href='<?=$taggedUrl?>/<?=$tag?>' class='tag'><?=$tag?></a>
<?php endforeach; ?>
</p>




<div class='question-info'>
<img src='<?=$userData["gravatar"]?>' alt='<?=$userData["name"]?>' title='<?=$userData["name"]?>'>
<span class='author-info'>
<span class='timestamp' title='<?=$rawTimestamp?>'><?=$timestamp?></span>
<a href='<?=$userUrl?>/<?=$userData['id']?>'><?=$userData['name']?></a> (<?=$userData['reputation']?>)</span>
</div>



</div>



<div class='bottom-row'>



</div>



</div>

