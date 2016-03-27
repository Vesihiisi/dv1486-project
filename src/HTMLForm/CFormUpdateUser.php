<?php

namespace Anax\HTMLForm;

/**
 * Anax base class for wrapping sessions.
 *
 */
class CFormUpdateUser extends \Mos\HTMLForm\CForm
{
    use \Anax\DI\TInjectionaware,
        \Anax\MVC\TRedirectHelpers;



    /**
     * Constructor
     *
     */
    public function __construct($id, $acronym, $email, $name, $active, $isUserActive)
    {
          parent::__construct([], [
            'user-id' => [
                'type'        => 'hidden',
                'value'       => $id,
            ],
            'acronym' => [
                'type'        => 'hidden',
                'value'       => $acronym,
            ],
            'email' => [
                'type'        => 'text',
                'label'       => 'E-mail',
                'value'       => $email,
                'required'    => true,
                'validation'  => ['not_empty', 'email_adress'],
            ],
            'name' => [
                'type'        => 'text',
                'label'       => 'Name',
                'value'       => $name,
                'required'    => true,
                'validation'  => ['not_empty'],
            ],
             'password' => [
                'type'        => 'password',
                'label'       => 'Password:',
                'required'    => true,
            ],
            'active' => [
            'type'        => 'checkbox',
                'value' => $active,
                'checked' => $isUserActive,
                'label' => 'The user is active',
              ],
            'submit' => [
            'type'        => 'submit',
            'callback'    => [$this, 'callbackSubmit'],
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
        $user = new \Anax\Users\User();
        $user->setDI($this->di);
        $saved = $user->save([
            'id' => $this->Value('user-id'),
            'acronym' => $this->Value('acronym'),
            'email' => $this->Value('email'),
            'name' => $this->Value('name'),
            'password' => password_hash($this->Value('password'), PASSWORD_DEFAULT),
            'updated' => gmdate('Y-m-d H:i:s'),
            'active' => $active,
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
        $this->redirectTo("users");
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
