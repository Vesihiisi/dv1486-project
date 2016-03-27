<?php

namespace Anax\Users;
 
/**
 * Model for Users.
 *
 */
class UsersController implements \Anax\DI\IInjectionAware
{
    use \Anax\DI\TInjectable;

    public function initialize()
    {
        $this->comment = new \Anax\Comment\Comment();
        $this->comment->setDI($this->di);
        $this->userRole = new \Anax\Users\UserRole();
        $this->userRole->setDI($this->di);
    }


    /**
    * List all users.
    *
    * @return void */
    public function listAction()
    {
        $allUsers = $this->users->findAll();
        if ($this->users->loggedInAdminCheck()) {
            $editUrl = $this->url->create("users/edit");
            $blockUrl = $this->url->create("admin/block");
        } else {
            $editUrl = null;
            $blockUrl = null;
        }
        $this->theme->setTitle("Users");
        $this->views->add('users/list-all', [
            'users' => $allUsers,
            'title' => "Users",
            'editUrl' => $editUrl,
            'profileUrl' => $this->url->create("users/id"),
            'blockUrl' => $blockUrl,
            ], 'full');
    }

    public function viewMostActiveAction($howMany, $where)
    {
        $users = $this->users->findMostActive($howMany);
        $this->views->add('users/minimal', [
        'users' => $users,
        'url' => $this->url->create('users/id'),
        'title' => "Most active users",
        ], $where);
    }

    public function viewMostReputationAction($howMany, $where)
    {
        $users = $this->users->findMostReputation($howMany);
        $this->views->add('users/minimal', [
            'users' => $users,
            'url' => $this->url->create('users/id'),
            'title' => "Top ranked users",
            ], $where);
    }

    public function wasteAction()
    {
        $url = $this->url->create('users');
        $wasteUsers = $this->di->users->findWaste();
        $this->theme->setTitle("List users");
        $this->views->add('users/list-all', [
            'users' => $wasteUsers,
            'title' => "Users that are in the waste basket",
            'url' => $url
            ]);
    }

    public function activeAction()
    {
        $url = $this->url->create('users');
        $users = $this->di->users->findActive();
        $this->theme->setTitle("List users");
        $this->views->add('users/list-all', [
            'users' => $users,
            'title' => "Users that are active and not in the waste basket",
            'url' => $url
            ]);
    }

    public function inactiveAction()
    {
        $url = $this->url->create('users');
        $users = $this->di->users->findInactive();
        $this->theme->setTitle("List users");
        $this->views->add('users/list-all', [
            'users' => $users,
            'title' => "Users that are not active",
            'url' => $url
            ]);
    }

    private function prepareForViewing($user)
    {
        $user->about = $this->textFilter->doFilter($user->about, "shortcode, markdown");
        $user->location = $this->textFilter->doFilter($user->location, "purify");
        return $user;
    }

    public function idAction($id = null)
    {
        $user = $this->di->users->find($id);
        if ($user != false) {
            $user = $this->prepareForViewing($user);
            $this->theme->setTitle("User details");
            $user->setGravatarSize($user, 160);
            if ($this->users->mayEditProfileCheck($this->users->getIdOfLoggedInUser(), $id)) {
                if ($this->users->isAdmin($this->users->getIdOfLoggedInUser())) {
                    $editProfileUrl = $this->url->create("users/edit/" . $id);
                } else {
                    $editProfileUrl = $this->url->create("users/edit");
                }
                
            } else {
                $editProfileUrl = null;
            }
            $this->views->add('users/view', [
                'userData' => $user,
                'editProfileUrl' => $editProfileUrl,
                ], 'featured-1');
            $numberOfContributions = $user->getNumberOfContributions($id);
            $usersQuestions = $this->comment->findWithUserId($id, "question");
            $usersAnswers = $this->comment->findWithUserId($id, "answer");
            $usersComments = $this->comment->findWithUserId($id, "comment");
            $numberOfQuestions = count($usersQuestions);
            $numberOfAnswers = count($this->comment->findWithUserId($id, "answer"));
            $numberOfComments = count($this->comment->findWithUserId($id, "comment"));
            if ($user->blocked == "yes") {
                $this->views->add('common/notificationWarning', [
                    "content" => "This user is currently blocked.",
                    ], "flash");
            }
            $this->views->add('users/about', [
                'userData' => $user,
                ], 'featured-2');   
            $this->views->add('users/activity', [
                'userData' => $user,
                'count' => $numberOfContributions,
                'numberOfQuestions' => $numberOfQuestions,
                'numberOfAnswers' => $numberOfAnswers,
                'numberOfComments' => $numberOfComments,
                'joined' => $this->timeHelper->timeAgo($user->created),
                ], 'featured-3');

            $this->views->add('users/activity-lists', [
                'questions' => $usersQuestions,
                'answers' => $usersAnswers,
                'comments' => $usersComments,
                'commentUrl' => $this->url->create("comment/id"),
                ], 'full');


        } else {
            $this->theme->setTitle("User not found");
            $this->views->add('common/notificationWarning', [
                'content' => "User with this id does not exist."
                ], 'full');
        }
    }

    public function editAction($id=null)
    {
        $this->theme->setTitle("Edit profile");
        if ($this->users->loggedInCheck()) {
            if($id == null) {
                $thisUser = $this->users->find($this->users->getIdOfLoggedInUser());
            } else {
                if ($this->users->loggedInAdminCheck()) {
                    $thisUser = $this->users->find($id);
                } else {
                    $this->views->add('common/notificationWarning', [
                        'content' => "You do not have permission to view this page."
                        ], 'flash');
                    return;
                }
            }
            if ($thisUser != false) {
                $this->di->session();
                $form = new \Anax\HTMLForm\CFormEditProfile($thisUser);
                $form->setDI($this->di);
                $form->check();
                $this->di->views->add('users/page', [
                'title' => "Edit profile",
                'content' => $form->getHTML(),
                ]);
            } else {
                $this->theme->setTitle("User not found");
                $this->views->add('common/notificationWarning', [
                'content' => "User with this id does not exist."
                ], 'full');
            }
        }
    }

    public function deleteAction($id = null)
    {
        if (!isset($id)) {
            die("Missing id");
        }
        $res = $this->di->users->delete($id);
        $url = $this->url->create('users/list');
        $this->response->redirect($url);
    }

    public function softAction($id = null)
    {
        if (!isset($id)) {
            die("Missing id");
        }
        $now = gmdate('Y-m-d H:i:s');
        $user = $this->di->users->find($id);
        if ($user->deleted == null) {
            $user->deleted = $now;
        } else {
            $user->deleted = $null;
        }
        $user->save();
        $url = $this->url->create('users/list');
        $this->response->redirect($url);
    }

    public function registerAction()
    {
        $url = $this->url->create('users/id');
        $this->di->session();
        $form = new \Anax\HTMLForm\CFormAddUser();
        $form->setDI($this->di);
        $form->check();
        $this->di->theme->setTitle("Register");
        $this->di->views->add('users/page', [
            'title' => "Register",
            'content' => $form->getHTML(),
            ]);
    }

    public function updateAction($id = null)
    {
        if (!isset($id)) {
            die("Missing id");
        }
        $user = $this->di->users->find($id);
        $acronym = $user->acronym;
        $email = $user->email;
        $name = $user->name;
        $active = $user->active;
        if ($active == null) {
            $isUserActive = false;
        } else {
            $isUserActive = true;
        }
        $form = new \Anax\HTMLForm\CFormUpdateUser($id, $acronym, $email, $name, $active, $isUserActive);
        $form->setDI($this->di);
        $status = $form->check();
        $this->di->theme->setTitle("Edit user information");
        $this->di->views->add('users/page', [
            'title' => "Edit user information",
            'content' => $form->getHTML()
        ]);
    }

    public function loginAction()
    {
        $this->di->session();
        $form = new \Anax\HTMLForm\CFormLogin();
        $form->setDI($this->di);
        $form->check();
        $this->di->theme->setTitle("Log in");
        $this->di->views->add('users/page', [
            'title' => "Log in",
            'content' => $form->getHTML(),
            ]);
    }

    public function logoutAction()
    {
        $this->users->logout();
        $this->response->redirect($_SERVER['HTTP_REFERER']);
    }




}
