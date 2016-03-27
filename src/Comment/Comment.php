<?php

namespace Anax\Comment;

/**
 * Model for Comment.
 *
 */
class Comment extends \Anax\MVC\CDatabaseModel
{


    
    public function setup()
    {
        $this->db->dropTableIfExists('Comment')->execute();
        $this->db->createTable(
        'Comment',
        [
            'id' => ['integer', 'primary key', 'not null', 'auto_increment'],
            'author' => ['integer'],
            'created' => ['datetime'],
            'updated' => ['datetime'],
            'deleted' => ['datetime'],
            'title' => ['varchar(150)'],
            'content' => ['text'],
            'type' => ['varchar(80)'],
            'parent' => ['integer'],
        ]
        )->execute();

        $this->db->insert(
        'Comment',
        ['title', 'content', 'author', 'created', 'type']);

        $now = gmdate('Y-m-d H:i:s');

        $this->db->execute([
            'Why do cats purr?','I think purring is amazing but i have no idea how it happens. Why and how do cats purr?',
            5,
            $now,
            'question'
            ]);

        $this->db->execute([
            'How do I stop my cat from clawing my sofa?',"As a cold-hearted capitalist I care deeply about material possessions and display of status. My cat, however, does not. What do I do to prevent him from destroying my really expensive sofa? At least until it's all paid off.",
            3,
            $now,
            'question'
            ]);

        $this->db->execute([
            'I am a vegetarian, can my cat also be?',"Hi, I have no idea how cats work and I'm unfamiliar with google. Can I feed my cat exclusively kale and berries? Organic ofc.",
            1,
            $now,
            'question'
            ]);

        $this->db->execute([
            'How do I get my cat off my keyboard? Pic inside',"My cat, Mr Whiskers, lies on my keyboard a lot (see picture for reference). It's cute but kinda bothersome :( How do I redirect his sweet little butt to a more suitable napping spot? ![Cat on keyboard](http://i.imgur.com/ezxASk8l.jpg)",
            2,
            $now,
            'question'
            ]);


        $this->db->insert(
        'Comment',
        ['author', 'content', 'created', 'parent', 'type']);

        $now = gmdate('Y-m-d H:i:s');


        $this->db->execute([5, 'Use a decoy keyboard. ![Cat on keyboard](http://i.imgur.com/WZOHjO0l.jpg)', $now, 4, 'answer',]);



        $this->db->execute([3, 'No.', $now, 3, 'answer',]);
        $this->db->execute([4, 'No.', $now, 3, 'answer',]);
        $this->db->execute([2, 'No.', $now, 3, 'answer',]);
        $this->db->execute([5, 'No.', $now, 3, 'answer',]);

        $this->db->execute([1, 'This is a comment', $now, 2, 'comment',]);
        $this->db->execute([5, 'This is another comment.', $now, 2, 'comment',]);

        $this->db->execute([2, 'This is a comment to an answer', $now, 7, 'comment',]);


    }

    private function getVotesOfComment($commentId)
    {
        $this->db->select()->from('Activity')->where('commentId = ?');
        $this->db->execute([$commentId]);
        $this->db->setFetchModeClass(__CLASS__);
        return $this->db->fetchAll();
    }

    private function calculateScore($commentId)
    {
        $score = 0;
        $votes = $this->getVotesOfComment($commentId);
        foreach ($votes as $row) {
            if($row->what == "up") {
                $score = $score + 1;
            } elseif ($row->what == "down") {
                $score = $score - 1;
            }
        }
        return $score;
    }

    private function addScore($comment)
    {
        $comment->score = $this->calculateScore($comment->id);
    }


    private function addThreadInfo($comment)
    {
        if($comment->type == "answer") {
            $parent = $this->find($comment->parent);
            $comment->parentTitle = $parent->title;
        } elseif ($comment->type == "comment") {
            $parent = $this->find($comment->parent);
            if ($parent->type == "answer") {
                $parent = $this->find($parent->parent);
            }
            $comment->contentShort = substr($comment->content, 0, 64) . " ...";
            $comment->topParent = $parent->id;
            $comment->parentTitle = $parent->title;
        }
    }


    public function find($id)
    {
        $comment = parent::find($id);
        $this->addScore($comment);
        return $comment;
    }

    public function findAllWithType($type)
    {
        $this->db->select()->from($this->getSource())->where('type = ?')->orderBy("created DESC");
        $this->db->execute([$type]);
        $comments = $this->db->fetchAll();
        foreach ($comments as $comment) {
            $comment = $this->addScore($comment);
        }
        return $comments;
    }


    public function findWithUserId($userId, $type=null)
    {
        $query = 'author = ?';
        $params = [$userId];
        if ($type != null) {
            $query .= ' and type = ?';
            array_push($params, $type);
        }
        $this->db->select()->from($this->getSource())->where($query);
        $this->db->execute($params);
        $this->db->setFetchModeClass(__CLASS__);
        $comments = $this->db->fetchAll();
        foreach ($comments as $comment) {
            $this->addScore($comment);
            $this->addThreadInfo($comment);
        }
        return $comments;
    }

    public function sortBy($comments, $key)
    {
        if ($key == "score") {
            usort($comments, function($a, $b)
            {
                return $b->score - $a->score;
            });
            foreach ($comments as $comment) {
                if($this->checkIfCommentIsAccepted($comment->id)) {
                    $commentToMoveToTheTop = $comment;
                    $index = array_search($comment, $comments);
                    unset($comments[$index]);
                    array_unshift($comments, $commentToMoveToTheTop);
                }
            }
        }
        return $comments;
    }

    public function findNewest($type, $howMany)
    {
        $this->db->select()->from($this->getSource())->where('type = ?')->orderBy("created DESC")->limit($howMany);
        $this->db->execute([$type]);
        $this->db->setFetchModeClass(__CLASS__);
        return $this->db->fetchAll();
    }

    public function findReactionsToQuestion($questionId, $reactionType)
    {
        $this->db->select()->from($this->getSource())->where('parent = ? and type = ?');
        $this->db->execute([$questionId, $reactionType]);
        $this->db->setFetchModeClass(__CLASS__);
        $comments = $this->db->fetchAll();
        foreach ($comments as $comment) {
            $this->addScore($comment);
            if ($comment->type == 'answer') {
                if ($this->checkIfCommentIsAccepted($comment->id)) {
                    $comment->accepted = "yes";
                } else {
                    $comment->accepted = "no";
                }
            }
        }
        return $comments;
    }

    public function checkIfCommentHasParent($commentId)
    {
        $parent = $this->getIdOfParent($commentId);
        if ($parent == null) {
            return false;
        } else {
            return true;
        }
    }

    public function getIdOfParent($childId)
    {
        $this->db->select()->from('Comment')->where('id = ?');
        $this->db->execute([$childId]);
        return $this->db->fetchAll()[0]->parent;
    }

    private function getIdsOfAnswersToQuestion($questionId)
    {
        $ids = array();
        $this->db->select()->from($this->getSource())->where('parent = ? and type = ?');
        $this->db->execute([$questionId, 'answer']);
        $this->db->setFetchModeClass(__CLASS__);
        $answers = $this->db->fetchAll();
        foreach ($answers as $row) {
            array_push($ids, $row->id);
        }
        return $ids;
    }

    public function getNumberOfAnswers($questionId)
    {
        return count($this->getIdsOfAnswersToQuestion($questionId));
    }

    private function checkIfCommentIsAccepted($commentId)
    {
        $this->db->select()->from('Activity')->where('commentId = ? and what = ?');
        $this->db->execute([$commentId, 'a']);
        $accepts = $this->db->fetchAll();
        if (count($accepts) > 0) {
            return true;
        } else {
            return false;
        }
    }

    private function checkIfQuestionHasAcceptedAnswer($questionId)
    {
        $hasAcceptedAnswer = false;
        $answersToQuestion = $this->getIdsOfAnswersToQuestion($questionId);
        foreach ($answersToQuestion as $id) {
            if($this->checkIfCommentIsAccepted($id)) {
                $hasAcceptedAnswer = true;
            }
        }
        return $hasAcceptedAnswer;
    }

    private function getIdOfQuestionsAcceptedAnswer($questionId)
    {
        $answersToQuestion = $this->getIdsOfAnswersToQuestion($questionId);
        foreach ($answersToQuestion as $id) {
            if($this->checkIfCommentIsAccepted($id)) {
                return $id;
            }
        }
    }

    private function saveDeaccept($commentId)
    {
        $this->db->delete(
            'Activity',
            'commentId = ? and what = ?');
        $this->db->execute([$commentId, 'a']);
    }

    private function saveAccept($answerId, $userId)
    {
        $now = gmdate('Y-m-d H:i:s');
        $this->db->insert(
            'Activity',
            ['commentId', 'userId', 'what', 'timestamp']);
        $this->db->execute([$answerId, $userId, 'a', $now]);
    }

    public function accept($answerId, $userId)
    {
        if ($this->checkIfCommentIsAccepted($answerId)) {
            $this->saveDeaccept($answerId);
        } else {
            $parentId = $this->find($answerId)->parent;
            if ($this->checkIfQuestionHasAcceptedAnswer($parentId)) {
                $acceptedAnswer = $this->getIdOfQuestionsAcceptedAnswer($parentId);
                $this->saveDeaccept($acceptedAnswer);
            }
            $this->saveAccept($answerId, $userId);
        }
    }

    public function findWithTagName($tag)
    {
        $questions = array();
        $this->db->select('commentId')
            ->from('Tag JOIN Tag2Comment ON Tag.id = Tag2Comment.tagId')
            ->where('Tag.name = ?');
        $this->db->execute([$tag]);
        $result = $this->db->fetchAll();
        foreach ($result as $row) {
            $id = $row->commentId;
            $this->db->select()->from($this->getSource())->where("id = ?");
            $this->db->execute([$id]);
            $this->db->setFetchModeClass(__CLASS__);
            $question = $this->db->fetchAll()[0];
            $questions[] = $question;
        }
        foreach ($questions as $comment) {
            $this->addScore($comment);
        }
        return $questions;
    }

    private function commentHasTagsCheck($commentId)
    {
        $this->db->select()
            ->from('Tag2Comment')
            ->where('CommentId = ?');
        $this->db->execute([$commentId]);
        $result = $this->db->fetchAll();
        if(count($result) > 0) {
            return true;
        } else {
            return false;
        }
    }





    public function saveWithTags($values = [])
    {
        $this->tag = new \Anax\Comment\Tag();
        $this->tag->setDI($this->di);
        $tags = $values["tags"];
        unset($values["tags"]);
        $this->setProperties($values);
        if (isset($values['id'])) {
            $this->tag->removeTagsFromComment($values["id"]);
            $this->update($values);
        } else {
            $this->create($values);
            $id = $this->di->db->lastInsertId();
            $response["id"] = $id;
        }
        $this->tag->addTagsToQuestion($tags, $this->id);
        $response["trueness"] = true;
        return $response;
    }

} 
