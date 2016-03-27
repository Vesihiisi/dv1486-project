<?php
switch ($pagekey) {
    case 'front-page':
    $redirectPage = '';
        break;
    
    default:
       $redirectPage = $pagekey;
        break;
}
?>

<div class='comment-form'>
<h3>Add a comment</h3>
    <form method=post>
        <input type=hidden name="redirect" value="<?=$this->url->create($redirectPage)?>"> 
        <input type=hidden name="pagekey" value="<?=$pagekey?>"> 
        <p><label>Comment<br/><textarea name='content' required><?=$content?></textarea></label></p>
        <p><label>Name<br/><input type='text' name='name' required value='<?=$name?>'/></label></p>
        <p><label>Homepage<br/><input type='url' name='web' value='<?=$web?>'/></label></p>
        <p><label>Email<br/><input type='email' name='mail' value='<?=$mail?>'/></label></p>
        <p class=buttons>
            <input type='submit' name='doCreate' value='Submit' onClick="this.form.action = '<?=$this->url->create('comment/add')?>'"/>
            <input type='submit' name='doRemoveAll' formnovalidate value='Remove all comments' onClick="this.form.action = '<?=$this->url->create('comment/remove-all/' . $pagekey)?>'"/> 
        </p>
    </form>
</div>
