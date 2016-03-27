<div class='user-infobox'>

<img src='<?=$userData->gravatar?>'>




<p class='reputation'><span class='number'><?=$userData->reputation?></span> reputation</p>
<p><i class="fa fa-envelope"></i><a href='mailto:<?=$userData->email?>'> <?=$userData->email?></a></p>

<?php if($userData->location != null) : ?>
<p><i class="fa fa-map-marker"></i> <?=$userData->location?></p>
<?php endif; ?>
<?php if($editProfileUrl != null) : ?>
<p><a class='edit-profile' href='<?=$editProfileUrl?>'>Edit profile</a></p>
<?php endif; ?>
</div>
