<?php

namespace Anax\Comment;

/**
 * To attach comments-flow to a page or some content.
 *
 */
class CommentController implements \Anax\DI\IInjectionAware
{
    use \Anax\DI\TInjectable;

    public function initialize()
    {
        $this->comment = new \Anax\Comment\Comment();
        $this->comment->setDI($this->di);
        $this->tag = new \Anax\Comment\Tag();
        $this->tag->setDI($this->di);
        $this->activity = new \Anax\Comment\Activity();
        $this->activity->setDI($this->di);
    }

    private function notBlockedCheck($somecode)
    {
        if ($this->theme->getVariable('title') == null) {
            $this->theme->setTitle("You have been blocked");
            $container = "flash";
        } else {
            $container = "main";
        }
        if ($this->users->isBlockedCheck($this->users->getIdOfLoggedInUser())) {
            $this->views->add('common/notificationWarning', [
                'content' => "You have been blocked. Blocked users cannot post any questions, answers or comments."
                ], $container);
        } else {
            $somecode();
        }
    }

    public function editAction($id)
    {
        if ($this->users->mayEditCheck($this->session->get('user')["userId"], $id)) {
            $this->notBlockedCheck(function() use($id) {
                $userId = $this->session->get('user')["userId"];
                $comment = $this->comment->find($id);
                $type = $comment->type;
                if ($type == "question") {
                    $allTags = $this->tag->findAllNames();
                    $checked = $this->tag->findTagsOfSpecificQuestion($comment->id);
                    $form = new \Anax\HTMLForm\CFormEditQuestion($comment, $allTags, $checked);
                } elseif ($type == "comment") {
                    $parentId = $this->comment->getIdOfParent($id);
                    if ($this->comment->checkIfCommentHasParent($parentId)) {
                        $parentId = $this->comment->getIdOfParent($parentId);
                    }
                    $form = new \Anax\HTMLForm\CFormEditComment($comment, $parentId);
                } elseif ($type == "answer") {
                    $parentId = $this->comment->getIdOfParent($id);
                    $form = new \Anax\HTMLForm\CFormEditAnswer($comment, $parentId);
                }
                $form->setDI($this->di);
                $form->check();
                $this->di->theme->setTitle("Edit");
                $this->di->views->add('comment/add', [
                    'content' => $form->getHTML(),
                    'title' => "Edit"
                    ]);
            });
        } else {
            $url = $this->url->create("users/login");
            $this->response->redirect($url);
        }
    }

    public function viewAction()
    {
        if($this->users->loggedInCheck()) {
            $this->views->add('comment/banner', [
            'url' => $this->url->create('comment/add')
            ], 'flash');
        }
        $this->views->add('common/h', [
            'number' => 1,
            'content' => "All questions",
            ], 'full');
        $allComments = $this->comment->findAllWithType('question');
        foreach ($allComments as $comment) {
            $userData = $this->di->users->find($comment->author)->getProperties();
            $numberOfAnswers = $this->comment->getNumberOfAnswers($comment->id);
            $this->views->add('comment/comments', [
            'comment' => $comment,
            'numberOfAnswers' => $numberOfAnswers,
            'tags' => $tags = $this->tag->findTagsOfSpecificQuestion($comment->id),
            'taggedUrl' => $this->url->create("comment/tagged"),
            'timestamp' => $this->timeHelper->timeAgo($comment->created),
            'rawTimestamp' => $this->timeHelper->friendlyTimeStamp($comment->created),
            'user' => $userData,
            'userUrl' => $this->url->create("users/id"),
            'commentUrl' => $this->url->create("comment/id"),
            ], 'full');
        }
    }

    public function viewNewestAction($type, $howMany, $where)
    {
        $newestComments = $this->comment->findNewest($type, $howMany);
        $url = $this->url->create("comment/id");
            $this->views->add('comment/minimal', [
            'comments' => $newestComments,
            'title' => "Recent questions",
            'url' => $url,
            ], $where);
    }

    public function viewMostCommonTagsAction($howMany, $where)
    {
        $tags = $this->tag->findMostCommon($howMany);
        $this->views->add('comment/tag/minimal', [
            'tags' => $tags,
            'url' => $this->url->create("comment/tagged"),
            'title' => "Most popular tags",
            ], $where);
    }

    private function displayComment($comment)
    {
        $comment->content = $this->textFilter->doFilter($comment->content, 'shortcode, markdown');
        $userData = $this->users->find($comment->author)->getProperties();
        if ($this->users->mayEditCheck($this->session->get('user')["userId"], $comment->id)) {
            $editUrl = $this->url->create("comment/edit");
        } else {
            $editUrl = null;
        }
        $this->views->add('comment/comment', [
            'commentId' => $comment->id,
            'comment' => $comment,
            'editUrl' => $editUrl,
            'userData' => $userData,
            'userUrl' => $this->url->create("users/id"),
            'voteUrl' => $this->url->create("comment/v"),
            'timestamp' => $this->timeHelper->timeAgo($comment->created),
            'rawTimestamp' => $this->timeHelper->friendlyTimeStamp($comment->created),
            ]);
    }


    public function idAction($id = null)
    {
        if (isset($_GET["sort"]) && !empty($_GET["sort"])) {
            $sort = htmlspecialchars($_GET["sort"]);
        } else {
            $sort = null;
        }
        $thisComment = $this->comment->find($id);
        $tags = $this->tag->findTagsOfSpecificQuestion($id);
        if ($thisComment->type == "question") {
            if ($this->users->mayEditCheck($this->session->get('user')["userId"], $id)) {
                $editUrl = $this->url->create("comment/edit");
            } else {
                $editUrl = null;
            }
            $this->theme->setTitle($thisComment->title);
            $userData = $this->di->users->find($thisComment->author)->getProperties();
            $idOfQuestionAuthor = $thisComment->author;
            $answers = $this->comment->findReactionsToQuestion($id, 'answer');
            $answers = $this->comment->sortBy($answers, $sort);
            $howManyAnswers = count($answers);
            $this->views->add('comment/question', [
                'commentUrl' => $this->url->create("comment/comment"),
                'editUrl' => $editUrl,
                'question' => $this->textFilter->doFilter($thisComment->content, 'shortcode, markdown'),
                'questionId' => $thisComment->id,
                'questionTitle' => htmlspecialchars($thisComment->title),
                'score' => $thisComment->score,
                'taggedUrl' => $this->url->create("comment/tagged"),
                'tags' => $tags,
                'title' => "View thread",
                'timestamp' => $this->timeHelper->timeAgo($thisComment->created),
                'rawTimestamp' => $this->timeHelper->friendlyTimeStamp($thisComment->created),
                'userData' => $userData,
                'userUrl' => $this->url->create("users/id"),
                'voteUrl' => $this->url->create("comment/v"),
                ]);
            $comments = $this->comment->findReactionsToQuestion($id, 'comment');
            foreach ($comments as $comment) {
                $this->displayComment($comment);
            }
            $this->views->add('comment/answersHeader', [
                'howManyAnswers' => $howManyAnswers,
                'sortTime' => $this->request->getCurrentUrl(false) . "?sort=time",
                'sortScore' => $this->request->getCurrentUrl(false) . "?sort=score",
                'sortTimeClass' => ($sort == "time" ? 'selected' : ''),
                'sortScoreClass' => ($sort == "score" ? 'selected' : ''),
                ]);
            foreach ($answers as $answer) {
                $userData = $this->di->users->find($answer->author)->getProperties();
                if ($this->users->getIdOfLoggedInUser() == $idOfQuestionAuthor) {
                    $acceptUrl = $this->url->create("comment/a");
                } else {
                    $acceptUrl = null;
                }
                if ($this->users->mayEditCheck($this->session->get('user')["userId"], $answer->id)) {
                    $editUrl = $this->url->create("comment/edit");
                } else {
                    $editUrl = null;
                }
                $this->views->add('comment/answer', [
                    'acceptUrl' => $acceptUrl,
                    'accepted' => $answer->accepted,
                    'answerId' => $answer->id,
                    'answerContent' => $this->textFilter->doFilter($answer->content, 'shortcode, markdown'),
                    'editUrl' => $editUrl,
                    'userData' => $userData,
                    'userUrl' => $this->url->create("users/id"),
                    'commentUrl' => $this->url->create("comment/comment"),
                    'score' => $answer->score,
                    'voteUrl' => $this->url->create("comment/v"),
                    'timestamp' => $this->timeHelper->timeAgo($answer->created),
                    'rawTimestamp' => $this->timeHelper->friendlyTimeStamp($answer->created),
                    ]);
                $comments = $this->comment->findReactionsToQuestion($answer->id, 'comment');
                foreach ($comments as $comment) {
                    $this->displayComment($comment);
                }
            }
            if ($this->di->users->loggedInCheck()) {
                $this->notBlockedCheck(function() use ($id) {
                    $form = new \Anax\HTMLForm\CFormAddAnswer([$this->session->get('user')["userId"], $id]);
                    $form->setDI($this->di);
                    $form->check();
                    $this->di->views->add('comment/add', [
                        'content' => $form->getHTML(),
                        'title' => "Answer this question"
                        ]);
                });
            } else {
                $this->views->add('common/notificationInfo', [
                'content' => "You must be logged in to answer."
                ]);
            }
        }
    }

    public function commentAction($id)
    {
        if ($this->di->users->loggedInCheck()) {
            $this->notBlockedCheck(function() use($id) {

                $thisComment = $this->comment->find($id);
                if ($thisComment->type == "question") {
                    $redirectHere = $this->url->create("comment/id/" . $id);
                } else {
                    $redirectHere = $this->url->create("comment/id/" . $this->comment->getIdOfParent($id));
                }
                
                $form = new \Anax\HTMLForm\CFormAddComment($this->session->get('user')["userId"], $id, $redirectHere);
                $form->setDI($this->di);
                $form->check();
                $thisComment = $this->comment->find($id);
                $tags = $this->tag->findTagsOfSpecificQuestion($id);
                $this->theme->setTitle($thisComment->title);
                $userData = $this->di->users->find($thisComment->author)->getProperties();
                $idOfQuestionAuthor = $thisComment->author;
                $answers = $this->comment->findReactionsToQuestion($id, 'answer');
                $howManyAnswers = count($answers);
                $this->views->add('comment/question', [
                    'commentUrl' => $this->url->create("comment/comment"),
                    'question' => $this->textFilter->doFilter($thisComment->content, 'shortcode, markdown'),
                    'questionId' => $thisComment->id,
                    'questionTitle' => htmlspecialchars($thisComment->title),
                    'score' => $thisComment->score,
                    'taggedUrl' => $this->url->create("comment/tagged"),
                    'tags' => $tags,
                    'title' => "View thread",
                    'timestamp' => $this->timeHelper->timeAgo($thisComment->created),
                    'rawTimestamp' => $this->timeHelper->friendlyTimeStamp($thisComment->created),
                    'userData' => $userData,
                    'userUrl' => $this->url->create("users/id"),
                    'voteUrl' => $this->url->create("comment/v"),
                    ]);
                $comments = $this->comment->findReactionsToQuestion($id, 'comment');
                foreach ($comments as $comment) {
                    $this->displayComment($comment);
                }
                $this->di->views->add('comment/add', [
                    'content' => $form->getHTML(),
                    'title' => "Add comment"
                    ]);
            });

        } else {
            $url = $this->url->create("users/login");
            dump("you must be logged in to answert");
            // $this->response->redirect($url);
        }
    }

    public function taggedAction($tag)
    {
        $tag = htmlspecialchars($tag);
        $questions = $this->comment->findWithTagName($tag);
        $this->views->add('comment/tag/tagHeading', [
            'title' => "Tagged questions",
            'tag' => $tag,
            'description' => $this->tag->getDescription($tag),
            ]);
        foreach ($questions as $q) {
            $userData = $this->di->users->find($q->author)->getProperties();
            $numberOfAnswers = $this->comment->getNumberOfAnswers($q->id);
            $this->views->add('comment/comments', [
            'comment' => $q,
            'user' => $userData,
            'numberOfAnswers' => $numberOfAnswers,
            'userUrl' => $this->url->create("users/id"),
            'commentUrl' => $this->url->create("comment/id"),
            'taggedUrl' => $this->url->create("comment/tagged"),
            'tags' => $tags = $this->tag->findTagsOfSpecificQuestion($q->id),
            'timestamp' => $this->timeHelper->timeAgo($q->created),
            'rawTimestamp' => $this->timeHelper->friendlyTimeStamp($q->created),
            'title' => "Questions tagged " . $tag,
            ], 'main');
        }
        $this->di->theme->setTitle($tag);
    }

    public function addAction()
    {
       
        if ($this->di->users->loggedInCheck()) {
            $this->notBlockedCheck(function() {
                $id = $this->session->get('user')["userId"];
                $tags = $this->tag->findAllNames();
                $this->di->session();
                $form = new \Anax\HTMLForm\CFormAddQuestion($id, $tags);
                $form->setDI($this->di);
                $form->check();
                $this->di->theme->setTitle("Ask a question");
                $this->di->views->add('comment/add', [
                    'content' => $form->getHTML(),
                    'title' => "Ask a question"
                    ]);
            });
        } else {
            $url = $this->url->create("users/login");
            $this->response->redirect($url);
        }
    }

    public function viewTagsAction()
    {
        $allTags = $this->tag->findAll();
            $this->di->views->add('comment/tag/overview', [
                'tags' => $allTags,
                'url' => $this->url->create("comment/tagged"),
                'title' => "Tags",
                ], 'full');
    }


    public function vAction($id, $type)
    {
        if ($this->users->loggedInCheck() && $this->users->mayVoteCheck($id, $type, $this->users->getIdOfLoggedInUser())) {
            $this->activity->vote($id, $this->users->getIdOfLoggedInUser(), $type);
        }
        $this->response->redirect($_SERVER['HTTP_REFERER']);
    }

    public function aAction($id)
    {
        if ($this->users->loggedInCheck() && $this->users->mayAcceptCheck($id, $this->users->getIdOfLoggedInUser())) {
            $this->comment->accept($id, $this->users->getIdOfLoggedInUser());
        }
        $this->response->redirect($_SERVER['HTTP_REFERER']);
    }




}
