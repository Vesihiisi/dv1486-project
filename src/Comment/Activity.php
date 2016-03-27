<?php

namespace Anax\Comment;

class Activity extends \Anax\MVC\CDatabaseModel
{
    
    public function setup()
    {
        $this->db->dropTableIfExists('Activity')->execute();
        $this->db->createTable(
        'Activity',
        [
            'id' => ['integer', 'primary key', 'not null', 'auto_increment'],
            'commentId' => ['integer'],
            'userId' => ['integer'],
            'what' => ['varchar(16)'],
            'timestamp' => ['datetime']
        ]
        )->execute();
    }

    public function getUsersVotesOnComment($comment, $user)
    {
        $score = 0;
        $this->db->select()->from('Activity')->where('commentId = ? and userId = ?');
        $this->db->execute([$comment, $user]);
        $result = $this->db->fetchAll();
        foreach ($result as $row) {
            if ($row->what == "up") {
                $score = $score + 1;
            } elseif ($row->what == "down") {
                $score = $score - 1;
            }
        }
        return $score;
    }

    public function vote($what, $who, $type)
    {
        $now = gmdate('Y-m-d H:i:s');
        $this->db->insert(
        'Activity',
        ['commentId', 'userId', 'what', 'timestamp']);
        $this->db->execute([$what, $who, $type, $now]);
    }




} 
