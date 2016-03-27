<?php

namespace Anax\HTMLForm;

/**
 * Anax base class for wrapping sessions.
 *
 */
class CFormAddComment extends \Mos\HTMLForm\CForm
{
    use \Anax\DI\TInjectionaware,
        \Anax\MVC\TRedirectHelpers;



    /**
     * Constructor
     *
     */
    public function __construct($userId, $parentId, $redirectHere)
    {
        parent::__construct([], [
            'userid' => [
                'type' => 'hidden',
                'value' => $userId,
            ],
            'parentid' => [
                'type' => 'hidden',
                'value' => $parentId,
            ],
            'content' => [
                'type'        => 'textarea',
                'label'       => 'Your answer',
                'required'    => true,
                'validation'  => ['not_empty'],
            ],
            'submit' => [
                'type'      => 'submit',
                'callback'  => [$this, 'callbackSubmit'],
            ],
        ]);
        $this->redirectHere = $redirectHere;
    }



    /**
     * Customise the check() method.
     *
     * @param callable $callIfSuccess handler to call if function returns true.
     * @param callable $callIfFail    handler to call if function returns true.
     */
    public function check($callIfSuccess = null, $callIfFail = null)
    {
        return parent::check([$this, 'callbackSuccess'], [$this, 'callbackFail']);
    }

   /**
     * Callback for submit-button.
     *
     */
    public function callbackSubmit()
    {
        $comment = new \Anax\Comment\Comment();
        $comment->setDI($this->di);


        $saved = $comment->save([
            'author'      => $this->Value('userid'),
            'content' => $this->Value('content'),
            'created'      => gmdate('Y-m-d H:i:s'),
            'parent' => $this->Value('parentid'),
            'type' => 'comment',
        ]);

        if ($saved == true) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Callback What to do if the form was submitted?
     *
     */
    public function callbackSuccess()
    {
        $this->redirectTo($this->redirectHere);    
    }

    /**
     * Callback for submit-button.
     *
     */
    public function callbackSubmitFail()
    {
        $this->AddOutput("<p><i>DoSubmitFail(): Form was submitted but I failed to process/save/validate it</i></p>");
        return false;
    }






    /**
     * Callback What to do when form could not be processed?
     *
     */
    public function callbackFail()
    {
        $this->AddOutput("<p><i>Form was submitted and the Check() method returned false.</i></p>");
        $this->redirectTo();
    }
}
