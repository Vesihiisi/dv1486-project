<h1>
<?=$title?>
</h1>
<h2>Routes</h2>
<ul>
<?php foreach ($content as $link => $explanation) : ?>
    <li><a href=<?=$link?>><?=$explanation?></a></li>
<?php endforeach; ?>
</ul>
