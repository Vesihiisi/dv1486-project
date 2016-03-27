<?php

namespace Anax\HTMLForm;

/**
 * Anax base class for wrapping sessions.
 *
 */
class CFormUpdateComment extends \Mos\HTMLForm\CForm
{
    use \Anax\DI\TInjectionaware,
        \Anax\MVC\TRedirectHelpers;



    /**
     * Constructor
     *
     */
    public function __construct($comment)
    {
        parent::__construct([], [
            'user-id' => [
                'type'        => 'hidden',
                'value'       => $comment->id,
            ],
            'pagekey' => [
                'type' => 'hidden',
                'value' => $comment->pagekey,
            ],
            'name' => [
                'type'        => 'text',
                'label'       => 'Name',
                'required'    => true,
                'validation'  => ['not_empty'],
                'value' => $comment->author,
            ],
            'email' => [
                'type'        => 'text',
                'label'       => 'E-mail',
                'required'    => false,
                'validation'  => [],
                'value' => $comment->email,
            ],
            'website' => [
                'type'        => 'text',
                'label'       => 'Website',
                'required'    => false,
                'validation'  => [],
                'value' => $comment->website,
            ],
            'content' => [
                'type'        => 'textarea',
                'label'       => 'Your comment',
                'required'    => true,
                'validation'  => ['not_empty'],
                'value' => $comment->content,
            ],
            'submit' => [
                'type'      => 'submit',
                'callback'  => [$this, 'callbackSubmit'],
            ],
        ]);
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
            'id' => $this->Value('user-id'),
            'pagekey' => $this->Value('pagekey'),
            'author'      => $this->Value('name'),
            'email'        => $this->Value('email'),
            'website'         => $this->Value('website'),
            'content' => $this->Value('content'),
            'updated'      => gmdate('Y-m-d H:i:s'),
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
        if ($this->Value('pagekey') == "front-page") {
            $this->redirectTo("");
        } else {
            $this->redirectTo($this->Value('pagekey'));
        }
        
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
