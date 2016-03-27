<?php

namespace Anax\Admin;
 
/**
 * Model for Users.
 *
 */
class AdminController implements \Anax\DI\IInjectionAware
{
    use \Anax\DI\TInjectable;

    public function initialize()
    {
        $this->comment = new \Anax\Comment\Comment();
        $this->comment->setDI($this->di);
        $this->tag = new \Anax\Comment\Tag();
        $this->tag->setDI($this->di);
        $this->userRole = new \Anax\Users\UserRole();
        $this->userRole->setDI($this->di);
        $this->user = new \Anax\Users\User();
        $this->user->setDI($this->di);
        $this->activity = new \Anax\Comment\Activity();
        $this->activity->setDI($this->di);
    }

    private function adminCheck($somecode)
    {
        if ($this->user->loggedInAdminCheck()) {
            $somecode();
        } else {
                $this->views->add('common/notificationWarning', [
                'content' => "You do not have permission to view this page."
                ], 'flash');
        }
    }


    public function viewAction()
    {
        $this->adminCheck(function() {
            $options = array("setup");
            $this->theme->setTitle("Admin panel");

            $blocked = $this->users->findAllBlocked();
            $this->views->add('admin/list-blocked', [
                'title' => "Blocked users",
                'users' => $blocked,
                'editUrl' => $this->url->create("users/edit"),
                'blockUrl' => $this->url->create("admin/block"),
                'profileUrl' => $this->url->create("users/id"),
            ]);
        });
    }

    public function blockAction($id=null)
    {
        if ($id!=null) {
            $user = $this->users->find($id);
            if ($user != null) {
                if ($this->users->isBlockedCheck($id)) {
                    $this->users->unblock($id);
                } else {
                    $this->users->block($id);
                }
            }
        }
        $this->response->redirect($_SERVER['HTTP_REFERER']);
    }

    public function setupAction()
    {


            $this->users->setup();
            $this->comment->setup();
            $this->tag->setup();
            $this->activity->setup();
            $this->theme->setTitle("Restore database");
            $this->views->add('users/page', [
                'title' => "Reset database",
                'content' => "The database has been restored"
                ]);
    }

}
