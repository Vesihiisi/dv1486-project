<?php

namespace Anax\HTMLForm;

/**
 * Anax base class for wrapping sessions.
 *
 */
class CFormEditComment extends \Mos\HTMLForm\CForm
{
    use \Anax\DI\TInjectionaware,
        \Anax\MVC\TRedirectHelpers;

    private $id;
    private $parentId;



    /**
     * Constructor
     *
     */
    public function __construct($comment, $parentId)
    {
        $this->id = $comment->id;
        $this->parentId = $parentId;

        parent::__construct([], [
            'content' => [
                'type'        => 'textarea',
                'label'       => 'Content',
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
            'id' => $this->id,
            'content' => $this->Value('content'),
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
        $this->redirectTo($this->di->url->create("comment/id/" . $this->parentId . "#" . $this->id));
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
