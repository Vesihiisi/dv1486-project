<?php

namespace Anax\HTMLForm;

/**
 * Anax base class for wrapping sessions.
 *
 */
class CFormAddUser extends \Mos\HTMLForm\CForm
{
    use \Anax\DI\TInjectionaware,
        \Anax\MVC\TRedirectHelpers;



    /**
     * Constructor
     *
     */
    public function __construct()
    {

        parent::__construct([], [
            'acronym' => [
                'type'        => 'text',
                'label'       => 'Username',
                'required'    => true,
                'validation'  => ['not_empty'],
            ],
            'name' => [
                'type'        => 'text',
                'label'       => 'Name',
                'required'    => true,
                'validation'  => ['not_empty'],
            ],
            'email' => [
                'type'        => 'text',
                'label' => "E-mail",
                'required'    => true,
                'validation'  => ['not_empty', 'email_adress'],
            ],
            'password' => [
                'type'        => 'password',
                'label' => "Password",
                'required'    => true,
                'validation'  => ['not_empty'],
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


        $this->user = new \Anax\Users\User();
        $this->user->setDI($this->di);
        $this->userRole = new \Anax\Users\UserRole();
        $this->userRole->setDI($this->di);

        $saved = $this->user->saveWithRole([
            'acronym'      => $this->Value('acronym'),
            'email'        => $this->Value('email'),
            'name'         => $this->Value('name'),
            'password'     => password_hash($this->Value('password'), PASSWORD_DEFAULT),
            'created'      => gmdate('Y-m-d H:i:s'),
            'active'       => gmdate('Y-m-d H:i:s'),
            'userRole' => 'user',
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
        $this->user = new \Anax\Users\User();
        $this->user->setDI($this->di);

        if($this->user->loggedInCheck()) {
            $this->user->logout();
        }
        $this->user->login([
            'acronym'      => $this->Value('acronym'),
            'password'     => $this->Value('password'),
        ]);
        $id = $this->di->db->lastInsertId() - 1;
        $this->redirectTo($this->di->url->create("users/id/" . $id));
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
