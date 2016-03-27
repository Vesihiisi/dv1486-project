<div class='answer' id='<?=$answerId?>'>

<div class='sidebar'>
<p><a href='<?=$voteUrl?>/<?=$answerId?>/up'><i class="fa fa-caret-up" title="Upvote"></i></a></p>
<p class='score'><?=$score?></p>
<p><a href='<?=$voteUrl?>/<?=$answerId?>/down'><i class="fa fa-caret-down" title="Downvote"></i></a></p>





<p><a href='<?=$commentUrl?>/<?=$answerId?>'><i class="fa fa-comment" title="Comment"></i></a></p>
<p><?php if($editUrl != null) : ?>
<a href='<?=$editUrl?>/<?=$answerId?>'><i class="fa fa-pencil" title='Edit'></i></a>
<?php endif; ?></p>
</div>

<div class='answer-content'>


<?php if (isset($acceptUrl)) : ?>
<a href='<?=$acceptUrl?>/<?=$answerId?>'><i class="fa fa-check accepted <?=$accepted?>"></i></a>
<?php else : ?>
    <i class="fa fa-check accepted <?=$accepted?>"></i>
<?php endif; ?>



<?=$answerContent?>





</div>

<div class='bottom-row'>
    <div class='question-info'>
        <img src='<?=$userData["gravatar"]?>' alt='<?=$userData["name"]?>' title='<?=$userData["name"]?>'>
        <span class='author-info'>
        <span class='timestamp' title='<?=$rawTimestamp?>'><?=$timestamp?></span>
        <a href='<?=$userUrl?>/<?=$userData['id']?>'><?=$userData['name']?></a> (<?=$userData['reputation']?>)</span>
    </div>
</div>





</div>
