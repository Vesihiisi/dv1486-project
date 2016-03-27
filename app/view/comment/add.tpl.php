<div class='comment-form'>
<?php if (isset($title)) : ?>
    <h2><?=$title?></h2>
<?php endif; ?>
<?php if (isset($url)) : ?>
<p><a href=<?=$url?>>GO BACK</a></p>
<?php endif; ?>

<?=$content?>
</div>

