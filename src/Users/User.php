<?php

namespace Anax\Users;

/**
 * Model for Users.
 *
 */
class User extends \Anax\MVC\CDatabaseModel
{

    public function setup() 
    {
        $this->db->dropTableIfExists('user')->execute();
 
        $this->db->createTable(
            'User',
            [
                'id' => ['integer', 'primary key', 'not null', 'auto_increment'],
                'acronym' => ['varchar(20)', 'unique', 'not null'],
                'email' => ['varchar(80)'],
                'name' => ['varchar(80)'],
                'location' => ['varchar(80)'],
                'about' => ['text'],
                'password' => ['varchar(255)'],
                'created' => ['datetime'],
                'updated' => ['datetime'],
                'deleted' => ['datetime'],
                'active' => ['datetime'],
            ]
        )->execute();

            $this->db->insert(
            'user',
            ['acronym', 'email', 'name', 'password', 'created', 'active', 'location', 'about', ]
        );
     
        $now = gmdate('Y-m-d H:i:s');
     
        $this->db->execute([
            'admin',
            'admin@example.com',
            'Administrator',
            password_hash('admin', PASSWORD_DEFAULT),
            $now,
            $now,
            'Stockholm, Sweden',
            'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In aliquet quam vel felis maximus, a facilisis urna ultrices. Nunc blandit lorem blandit justo tempor ornare. Pellentesque eleifend nunc a velit maximus, a viverra lectus porta. Etiam ligula ante, pretium eu sem ut, consectetur hendrerit metus.'
        ]);
     
        $this->db->execute([
            'test1',
            'test1@example.com',
            'Gunnar Forsström',
            password_hash('test1', PASSWORD_DEFAULT),
            $now,
            $now,
            'Helsinki, Finland',
            'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In aliquet quam vel felis maximus, a facilisis urna ultrices. Nunc blandit lorem blandit justo tempor ornare. Pellentesque eleifend nunc a velit maximus, a viverra lectus porta. Etiam ligula ante, pretium eu sem ut, consectetur hendrerit metus.'
        ]);

        $this->db->execute([
            'test2',
            'test2@example.com',
            'Åke Svensson',
            password_hash('test2', PASSWORD_DEFAULT),
            $now,
            $now,
            'Göteborg, Sweden',
            'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In aliquet quam vel felis maximus, a facilisis urna ultrices. Nunc blandit lorem blandit justo tempor ornare. Pellentesque eleifend nunc a velit maximus, a viverra lectus porta. Etiam ligula ante, pretium eu sem ut, consectetur hendrerit metus.'
        ]);

        $this->db->execute([
            'test3',
            'test3@example.com',
            'Jan Karlsson',
            password_hash('test3', PASSWORD_DEFAULT),
            $now,
            $now,
            'Oulu, Finland',
            'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In aliquet quam vel felis maximus, a facilisis urna ultrices. Nunc blandit lorem blandit justo tempor ornare. Pellentesque eleifend nunc a velit maximus, a viverra lectus porta. Etiam ligula ante, pretium eu sem ut, consectetur hendrerit metus.'
        ]);

        $this->db->execute([
            'test4',
            'test4@example.com',
            'Albin Andersson',
            password_hash('test4', PASSWORD_DEFAULT),
            $now,
            $now,
            'Stockholm, Sweden',
            'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In aliquet quam vel felis maximus, a facilisis urna ultrices. Nunc blandit lorem blandit justo tempor ornare. Pellentesque eleifend nunc a velit maximus, a viverra lectus porta. Etiam ligula ante, pretium eu sem ut, consectetur hendrerit metus.'
        ]);

        $this->db->dropTableIfExists('UserRole')->execute();
 
        $this->db->createTable(
            'UserRole',
            [
                'id' => ['integer', 'primary key', 'not null', 'auto_increment'],
                'userId' => ['integer'],
                'userRole' => ['varchar(32)'],
            ]
        )->execute();

        $this->db->insert(
            'UserRole',
            ['userId', 'userRole']);

        $this->db->execute([1, 'user']);
        $this->db->execute([1, 'admin']);
        $this->db->execute([2, 'user']);
        $this->db->execute([3, 'user']);
        $this->db->execute([4, 'user']);
        $this->db->execute([5, 'user']);

    }



    public function login($loginData)
    {
        $this->userRole = new \Anax\Users\UserRole();
        $this->userRole->setDI($this->di);
        $userName = $loginData["acronym"];
        $passwordGiven = $loginData["password"];
        $this->db->select()->from('User')->where("acronym = ?");
        $this->db->execute([$userName]);
        $this->db->setFetchModeClass(__CLASS__);
        $user = $this->db->fetchAll()[0];
        $realPassword = $user->password;
        $informationAboutUser["userId"] = $user->id;
        $informationAboutUser["userName"] = $user->acronym;
        $informationAboutUser["roles"] = $this->userRole->getRolesOfUser($user->id);
        if (password_verify($passwordGiven, $realPassword)) {
            $this->session->set('user', $informationAboutUser);
            return true;
        } else {
            dump("wrong password");
        }
    }

    private function generateGravatarUrl($email, $size=null)
    {
        $hash = md5( strtolower( trim( $email) ) );
        $g = "http://www.gravatar.com/avatar/" . $hash . "?d=identicon";
        if (!is_null($size)) {
            $g = $g . "&s=" . $size;
        }
        return $g;
    }

    public function setGravatarSize($user, $size)
    {
        $user->gravatar = $this->generateGravatarUrl($user->email, $size);
    }

    public function getNumberOfContributions($userId)
    {
        return count($this->comment->findWithUserId($userId));
    }

    private function getScoreOfContributions($userId)
    {

        $score = 0;
        $comments = $this->comment->findWithUserId($userId);
        foreach ($comments as $comment) {
            $score = $score + $comment->score;
        }
        return $score;
    }

    private function getScoreForCreations($userId)
    {
        $score = 0;
        $comments = $this->comment->findWithUserId($userId);
        foreach ($comments as $comment) {
            if ($comment->type == "question") {
                $score = $score + 5;
            } elseif ($comment->type == "answer") {
                $score = $score + 10;
            } elseif ($comment->type == "comment") {
                $score = $score + 2;
            }
        }
        return $score;
    }

    private function getScoreForAcceptedAnswers($userId)
    {
        $score = 0;
        $howManyAnswersByUserHaveBeenAccepted = 0;
        $this->db->select()->from('Comment')->where('author = ? and type = ?');
        $this->db->execute([$userId, 'answer']);
        $answersByUser = $this->db->fetchAll();
        foreach ($answersByUser as $answer) {
            $this->db->select()->from('Activity')->where('commentId = ? and what = ?');
            $this->db->execute([$answer->id, 'a']);
            $accepts = $this->db->fetchAll();
            $howManyAnswersByUserHaveBeenAccepted = $howManyAnswersByUserHaveBeenAccepted + count($accepts);
        }
        $add = $howManyAnswersByUserHaveBeenAccepted * 15;
        return $score + $add;
    }

    private function getScoreForAcceptingAnswers($userId)
    {
        $score = 0;
        $this->db->select()->from('Activity')->where('userId = ? and what = ?');
        $this->db->execute([$userId, 'a']);
        $answers = $this->db->fetchAll();
        $add = count($answers) * 2;
        return $score + $add;
    }

    private function calculateReputation($userId)
    {
        $this->comment = new \Anax\Comment\Comment();
        $this->comment->setDI($this->di);
        $reputation = 1;
        $reputation = $reputation + $this->getScoreForCreations($userId);
        $reputation = $reputation + $this->getScoreOfContributions($userId);
        $reputation = $reputation + $this->getScoreForAcceptedAnswers($userId);
        $reputation = $reputation + $this->getScoreForAcceptingAnswers($userId);
        return $reputation;
    }

    private function process($userData)
    {
        $userData->gravatar = $this->generateGravatarUrl($userData->email);
        $userData->reputation = $this->calculateReputation($userData->id);
        $userData->roles = $this->userRole->getRolesOfUser($userData->id);
        $userData->blocked = ($this->isBlockedCheck($userData->id) ? "yes" : "no");
        return $userData;
    }

    public function find($id)
    {
        $this->userRole = new \Anax\Users\UserRole();
        $this->userRole->setDI($this->di);
        $userData = parent::find($id);
        if($userData != false) {
            $userData = $this->process($userData);
        }
        return $userData;
    }

    public function findAll()
    {
        $this->userRole = new \Anax\Users\UserRole();
        $this->userRole->setDI($this->di);
        $users = parent::findAll();
        foreach ($users as $user) {
            $user = $this->process($user);
        }
        return $users;
    }

    public function findAllBlocked()
    {

        $this->userRole = new \Anax\Users\UserRole();
        $this->userRole->setDI($this->di);
        $this->activity = new \Anax\Comment\Activity();
        $this->activity->setDI($this->di);
        $this->db->select()->from('Activity')->where('what = ?');
        $this->db->execute(['b']);
        $blocks = $this->db->fetchAll();
        $users = array();
        foreach ($blocks as $block) {
            $this->db->select()->from('User')->where("id = ?");
            $this->db->execute([$block->userId]);
            $this->db->setFetchModeClass(__CLASS__);
            $result = $this->db->fetchAll()[0];
            $result = $this->process($result);
            $users[] = $result;
        }
        return $users;
    }

    public function isAdmin($userId)
    {
        $this->userRole = new \Anax\Users\UserRole();
        $this->userRole->setDI($this->di);
        if ($this->userRole->doesUserHaveRole($userId, "admin")) {
            return true;
        } else {
            return false;
        }
    }

    public function findMostActive($howMany)
    {
        $userIds = array();
        $users = array();
        $this->db->select('author, count(*) as count')
            ->from('Comment')
            ->groupBy('author')
            ->orderBy('count(*) DESC')
            ->limit($howMany);
        $this->db->execute();
        $result = $this->db->fetchAll();
        foreach ($result as $row) {
            array_push($userIds, [$row->author, $row->count]);
        }
        foreach ($userIds as $id) {
            $this->db->select()->from($this->getSource())->where("id = ?");
            $this->db->execute([$id[0]]);
            $this->db->setFetchModeClass(__CLASS__);
            $user = $this->db->fetchAll()[0];
            $user->count = $id[1];
            array_push($users, $user);
        }
        return $users;
    }

    public function findMostReputation($howMany)
    {
        $userIds = array();
        $users = array();
        $allUsers = parent::findAll();
        foreach ($allUsers as $user) {
            $user->count = $this->calculateReputation($user->id);
        }
        usort($allUsers, function($a, $b)
        {
            return $b->count - $a->count;
        });
        foreach ($allUsers as $user) {
            array_push($users, $user);
        }
        return array_slice($users, 0, $howMany);
    }

    public function loggedInCheck()
    {
        if ($this->session->has("user")) {
            return true;
        } else {
            return false;
        }
    }

    public function loggedInAdminCheck()
    {
        if ($this->loggedInCheck() && in_array("admin", $this->getRolesOfLoggedInUser())) {
            return true;
        } else {
            return false;
        }
    }

    public function getNameOfLoggedInUser()
    {
        return $this->session->get("user")["userName"];
    }

    public function getIdOfLoggedInUser()
    {
        return $this->session->get("user")["userId"];
    }

    public function getRolesOfLoggedInUser()
    {
        return $this->session->get("user")["roles"];
    }

    public function logOut()
    {
        $this->session->get('user');
        $this->session->set('user', null);
    }

    public function generateLoginPanel()
    {
        if ($this->loggedInCheck()) {
            $userName = $this->getNameOfLoggedInUser();
            $userRank = $this->calculateReputation($this->getIdOfLoggedInUser());
        } else {
            $userName = null;
            $userRank = null;
        }
        $this->views->add('users/panel', [
        'editLink' => $this->url->create('users/edit'),
        'loginLink' => $this->url->create('users/login'),
        'logoutLink' => $this->url->create('users/logout'),
        'registerLink' => $this->url->create('users/register'),
        'userLink' => $this->url->create('users/id') . "/" . $this->getIdOfLoggedInUser(),
        'userName' => $userName,
        'userRank' => $userRank,
        ], "header");
    }

    public function userIsAuthorOfComment($userId, $commentId)
    {

        $this->db->select()->from('Comment')->where("id = ?");
        $this->db->execute([$commentId]);
        $comment = $this->db->fetchAll()[0];
        if ($comment->author == $userId) {
            return true;
        } else {
            return false;
        }
    }

    public function userIsAuthorOfParentComment($userId, $childCommentId)
    {
        $this->db->select()->from('Comment')->where("id = ?");
        $this->db->execute([$childCommentId]);
        $childComment = $this->db->fetchAll()[0];
        $parentId = $childComment->parent;
        $this->db->select()->from('Comment')->where("id = ?");
        $this->db->execute([$parentId]);
        $parentComment = $this->db->fetchAll()[0];
        $parentAuthor = $parentComment->author;
        if ($parentAuthor == $userId) {
            return true;
        } else {
            return false;
        }
    }

    public function mayVoteCheck($comment, $type, $user)
    {
        $this->activity = new \Anax\Comment\Activity();
        $this->activity->setDI($this->di);
        if ($this->userIsAuthorOfComment($user, $comment)) {
            return false;
        }
        $userScore = $this->activity->getUsersVotesOnComment($comment, $user);
        if ($userScore == 0) {
            return true;
        } elseif ($userScore == 1 && $type == "down") {
            return true;
        } elseif ($userScore == -1 && $type == "up") {
            return true;
        }
        return false;
    }

    public function mayAcceptCheck($comment, $user)
    {
        if ($this->userIsAuthorOfComment($user, $comment)) {
            return false;
        }
        if ($this->userIsAuthorOfParentComment($user, $comment)) {
            return true;
        }
        return false;
    }

    public function mayEditCheck($userId, $commentId)
    {
        if($this->userIsAuthorOfComment($userId, $commentId) || $this->isAdmin($userId)) {
            return true;
        } else {
            return false;
        }
    }

    public function mayEditProfileCheck($userId, $profileId) {
        if($this->isAdmin($userId) || $userId == $profileId) {
            return true;
        } else {
            return false;
        }
    }

    public function isBlockedCheck($userId)
    {
        $this->db->select()->from('Activity')->where('userId = ? and what = ?');
        $this->db->execute([$userId, 'b']);
        $result = $this->db->fetchAll();
        if (count($result) >0) {
            return true;
        } else {
            return false;
        }
    }
    public function block($id)
    {
        $now = gmdate('Y-m-d H:i:s');
        $this->db->insert(
        'Activity',
        ['userId', 'what', 'timestamp']);
        $this->db->execute([$id, "b", $now]);
    }

    public function unblock($id)
    {
        $this->db->delete(
            'Activity',
            'userId = ? and what = ?'
            );
        $this->db->execute([$id, 'b']);
    }


    public function saveWithRole($values = [])
    {
        $this->userRole = new \Anax\Users\UserRole();
        $this->userRole->setDI($this->di);
        $userRole = $values["userRole"];
        unset($values["userRole"]);
        $this->setProperties($values);
        $this->create($values);
        $this->userRole->assign($this->id, $userRole);
        return true;
    }

} 
