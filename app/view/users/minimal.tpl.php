<div class='front-page-box'>
<h3 class='background-green'><?=$title?></h3>
<?php foreach($users as $user): ?>
<p><a href='<?=$url?>/<?=$user->id?>'><?=$user->name?> (<?=$user->count?>)</a></p>
<?php endforeach; ?>
</div>
