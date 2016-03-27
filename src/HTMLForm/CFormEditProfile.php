<?php

namespace Anax\HTMLForm;

/**
 * Anax base class for wrapping sessions.
 *
 */
class CFormEditProfile extends \Mos\HTMLForm\CForm
{
    use \Anax\DI\TInjectionaware,
        \Anax\MVC\TRedirectHelpers;

    private $id;

    /**
     * Constructor
     *
     */
    public function __construct($user)
    {
        $this->id = $user->id;


        parent::__construct([], [
            'acronym' => [
            'type'=>'hidden',
            'value' => $user->acronym],

            'name' => [
                'type'        => 'text',
                'label'       => 'Name',
                'required'    => true,
                'validation'  => ['not_empty'],
                'value' => $user->name,
            ],
            'email' => [
                'type'        => 'text',
                'required'    => true,
                'validation'  => ['not_empty', 'email_adress'],
                'value' => $user->email,
            ],
            'location' => [
            'type' => 'text',
            'value' => $user->location,
            ],
            'about' => [
            'type' => 'textarea',
            'value' => $user->about,],
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
        $this->user = new \Anax\Users\User();
        $this->user->setDI($this->di);
        $this->userRole = new \Anax\Users\UserRole();
        $this->userRole->setDI($this->di);

        $saved = $this->user->save([
            'id' => $this->id,
            'acronym' => $this->Value('acronym'),
            'email'        => $this->Value('email'),
            'name'         => $this->Value('name'),
            'location' => $this->Value('location'),
            'about' => $this->Value('about'),
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
        $this->redirectTo($this->di->url->create("users/id/" . $this->id));
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
