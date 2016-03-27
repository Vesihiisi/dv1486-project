<div class='answers-header'>
<?php if($howManyAnswers > 0) : ?>
<h3>This question has <?=$howManyAnswers?> answers</h3>
<p class='sorting-bar'>Sort by: <a href='<?=$sortTime?>' class='<?=$sortTimeClass?>'>time</a> <a href='<?=$sortScore?>' class='<?=$sortScoreClass?>'>score</a></p>
<?php else : ?>
    <h3>This question has no answers yet</h3>
<?php endif; ?>
</div>
