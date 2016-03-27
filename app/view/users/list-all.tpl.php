<h1>
<?=$title?>
</h1>

<?php foreach ($users as $user) : ?>
<div class='user-info'>
<a href='<?=$profileUrl?>/<?=$user->id?>'><img src='<?=$user->gravatar?>'></a>
<a href='<?=$profileUrl?>/<?=$user->id?>'>
<p class='name'>
<?=$user->name?>
<?php if( in_array("admin", $user->roles)) : ?>
<i class="fa fa-star"></i>
<?php endif; ?>
</a>
</p>
<p class='location'><?=$user->location?></p>
<p class='reputation'><?=$user->reputation?></p>
<p>
<?php if(isset($editUrl)) : ?>
<a href='<?=$editUrl?>/<?=$user->id?>'>[edit]</a>
<?php endif; ?>
<?php if(isset($blockUrl)) : ?>
<a href='<?=$blockUrl?>/<?=$user->id?>'>


<?php if($user->blocked == "yes") : ?>
[unblock]
<?php else : ?>
[block]
<?php endif; ?>


</a>
<?php endif; ?>
</p>
</div>

<?php endforeach; ?>
