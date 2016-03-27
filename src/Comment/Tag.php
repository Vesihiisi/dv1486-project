<?php

namespace Anax\Comment;

/**
 * Model for Comment.
 *
 */
class Tag extends \Anax\MVC\CDatabaseModel
{
    
    public function setup()
    {
        $this->db->dropTableIfExists('Tag')->execute();
        $this->db->createTable(
        'Tag',
        [
            'id' => ['integer', 'primary key', 'not null', 'auto_increment'],
            'name' => ['varchar(64)'],
            'description' => ['varchar(256)'],
        ]
        )->execute();

        $this->db->insert(
        'Tag',
        ['name', 'description']);


        $this->db->execute(['diet','Questions related to feeding your cat for optimal health and performance.']);
        $this->db->execute(['breeds','On the various breeds of cats.']);
        $this->db->execute(['health','Questions related to cat health and diseases.']);
        $this->db->execute(['behavior','The things cats do, and why.']);
        $this->db->execute(['training','How to teach your cat stuff.']);
        $this->db->execute(['lifestyle','How to organize your life around your cat.']);

        $this->db->dropTableIfExists('Tag2Comment')->execute();
        $this->db->createTable(
        'Tag2Comment',
        [
            'id' => ['integer', 'primary key', 'not null', 'auto_increment'],
            'tagId' => ['integer'],
            'commentId' => ['integer'],
        ]
        )->execute();

        $this->db->insert(
        'Tag2Comment',
        ['tagId', 'commentId']);

        $this->db->execute(['4','1']);
        $this->db->execute(['5','2']);
        $this->db->execute(['6','2']);
        $this->db->execute(['1','3']);
        $this->db->execute(['3','3']);
        $this->db->execute(['5','4']);
    }

    private function getCount($tagId)
    {
        $this->db->select('count(*) as count')
            ->from('Tag2Comment')
            ->where('tagId = ?');
        $this->db->execute([$tagId]);
        $count = $this->db->fetchAll()[0]->count;
        return $count;
    }

    public function findAll()
    {
        $allTags = parent::findAll();
        foreach ($allTags as $tag) {
            $tag->count = $this->getCount($tag->id);
        }
        return $allTags;
    }

    public function findAllNames()
    {
        $names = array();
        $allTags = parent::findAll();
        foreach ($allTags as $tag) {
            array_push($names, $tag->name);
        }
        return $names;
    }

    private function tagIdsToNames($arrayOfIds)
    {
        $names = array();
        foreach ($arrayOfIds as $id) {
            $this->db->select('name')
                ->from('Tag')
                ->where('id = ?');
            $this->db->execute([$id]);
            $name = $this->db->fetchAll();
            array_push($names, $name[0]->name);
        }
        return $names;
    }

    public function findTagsOfSpecificQuestion($questionId)
    {
        $this->db->select('tagId')
            ->from('Tag2Comment')
            ->where('commentId = ?');
        $this->db->execute([$questionId]);
        $tags = $this->db->fetchAll();
        $tagIds = array();
        foreach ($tags as $tag) {
            array_push($tagIds, $tag->tagId);
        }
        $tagNames = $this->tagIdsToNames($tagIds);
        return $tagNames;
    }

    public function findMostCommon($howMany)
    {
        $this->db->select('Tag.id, Tag.name, count(*) as count')
            ->from('Tag2Comment')
            ->join('Tag', 'Tag2Comment.tagId = Tag.id')
            ->groupBy('tagId')
            ->orderBy('count(*) DESC')
            ->limit($howMany);
        $this->db->execute();
        return $this->db->fetchAll();
    }

    public function getDescription($name)
    {
        $this->db->select('description')
            ->from('Tag')
            ->where('name = ?');
        $this->db->execute([$name]);
        return $this->db->fetchAll()[0]->description;
    }

    private function tagNameToId($name)
    {
        $this->db->select('id')
            ->from('Tag')
            ->where('name = ?');
        $this->db->execute([$name]);
        return $this->db->fetchAll()[0]->id;
    }

    public function addTagsToQuestion($tags, $id)
    {
        foreach ($tags as $tag) {
            $tagId = $this->tagNameToId($tag);
            $this->db->insert('Tag2Comment', ['tagId', 'commentId']);
            $this->db->execute([$tagId, $id]);
        }
    }

    public function removeTagsFromComment($commentId)
    {
        $this->db->delete(
            'Tag2Comment',
            'commentId = ?');
        $this->db->execute([$commentId]);
    }

} 
