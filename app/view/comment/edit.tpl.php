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
<h3>Edit comment</h3>
    <form method=post>
        <input type=hidden name="redirect" value="<?=$this->url->create($redirectPage)?>"> 
        <input type=hidden name="pagekey" value="<?=$pagekey?>"> 
        <p><label>Comment<br/><textarea name='content' required><?=strip_tags($comment['content'])?></textarea></label></p>
        <p><label>Name<br/><input type='text' name='name' required value='<?=$comment['name']?>'/></label></p>
        <p><label>Homepage<br/><input type='url' name='web' value='<?=$comment['web']?>'/></label></p>
        <p><label>Email<br/><input type='email' name='mail' value='<?=$comment['mail']?>'/></label></p>
        <p class="buttons commentButtons">
            <input type='submit' name='doEdit' value='Save' onClick="this.form.action = '<?=$this->url->create('comment/save-comment/' . $id . '/' . '' )?>'"/>
            <input type='submit' name='doRemoveOne' formnovalidate value='Remove this comment' onClick="this.form.action = '<?=$this->url->create('comment/remove-certain/' . $id . '/' . '')?>'"/>
            <input type='submit' name='doGoBack' formnovalidate value='Go back' onClick="this.form.action =
                '<?=$this->url->create($redirectPage)?>'"/>
        </p>

    </form>
</div> 
