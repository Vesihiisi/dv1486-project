<h3>Activity</h3>
<?php if($count > 0) : ?>
<p>Total contributions: <?=$count?></p>
<ul class="fa-ul">
<li><i class="fa-li fa fa-sign-in"></i> Joined: <span title='<?=$userData->created?>'><?=$joined?></span></li>
<li><i class="fa-li fa fa-question"></i> Questions: <?=$numberOfQuestions?></li>
<li><i class="fa-li fa fa-reply"></i> Answers: <?=$numberOfAnswers?></li>
<li><i class="fa-li fa fa-comments-o"></i> Comments: <?=$numberOfComments?></li>
</ul>
<?php else : ?>
<p><?=$userData->name?> has not asked any questions yet!</p>
<?php endif; ?>


