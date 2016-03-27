<div class='loginPanel'>
<?php if($userName != null) : ?>
<i class='fa fa-user'></i><a href='<?=$userLink?>'><?=$userName?> (<?=$userRank?>)</a> <a href='<?=$logoutLink?>'>[log out]</a> [<a href='<?=$editLink?>'>edit profile]</a>
<?php else : ?>
<a href='<?=$loginLink?>'>Log in</a> | <a href='<?=$registerLink?>'>Register</a>
<?php endif; ?>
</div>
