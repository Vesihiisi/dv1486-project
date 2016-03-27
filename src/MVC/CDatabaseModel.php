<?php

namespace Anax\MVC;

/**
 * Base class for database models.
 *
 */
class CDatabaseModel implements \Anax\DI\IInjectionAware
{
    use \Anax\DI\TInjectable;

        /**
     * Get the table name.
     *
     * @return string with the table name.
     */
    public function getSource()
    {
        return strtolower(implode('', array_slice(explode('\\', get_class($this)), -1)));
    }

    public function findAll()
    {
        $this->db->select()->from($this->getSource());
        $this->db->execute();
        $this->db->setFetchModeClass(__CLASS__);
        return $this->db->fetchAll();
    }

    public function findWaste()
    {
        $this->db->select()->from($this->getSource())->where('deleted is not NULL');
        $this->db->execute();
        $this->db->setFetchModeClass(__CLASS__);
        return $this->db->fetchAll();
    }

    public function findNotInWaste()
    {
        $this->db->select()->from($this->getSource())->where('deleted is NULL');
        $this->db->execute();
        $this->db->setFetchModeClass(__CLASS__);
        return $this->db->fetchAll();
    }

    public function findInactive()
    {
        $this->db->select()->from($this->getSource())->where('active is NULL');
        $this->db->execute();
        $this->db->setFetchModeClass(__CLASS__);
        return $this->db->fetchAll();
    }

    public function findActive()
    {
        $this->db->select()->from($this->getSource())->where('active is not NULL and deleted is NULL');
        $this->db->execute();
        $this->db->setFetchModeClass(__CLASS__);
        return $this->db->fetchAll();
    }

    public function find($id)
    {
        $this->db->select()->from($this->getSource())->where("id = ?");
        $this->db->execute([$id]);
        $this->db->setFetchModeClass(__CLASS__);
        $result = $this->db->fetchInto($this);
        return $result;
    }

    public function getProperties()
    {
        $properties = get_object_vars($this);
        unset($properties['di']);
        unset($properties['db']);
        return $properties;
    }

    public function delete($id)
    {
        $this->db->delete(
            $this->getSource(),
            'id = ?'
            );
        return $this->db->execute([$id]);
    }

    public function save($values = [])
    {
        $this->setProperties($values);
        $values = $this->getProperties();
        if (isset($values['id'])) {
            return $this->update($values);
        } else {
            return $this->create($values);
        }
    }

    public function setProperties($properties)
    {
        if (!empty($properties)) {
            foreach ($properties as $key => $val) {
                $this->$key = $val;
            }
        }
    }

    public function create($values)
    {
        $keys   = array_keys($values);
        $values = array_values($values);
     
        $this->db->insert(
            $this->getSource(),
            $keys
        );
        $res = $this->db->execute($values);
        $this->id = $this->db->lastInsertId();
        return $res;
    }

    public function update($values)
    {
        $keys   = array_keys($values);
        $values = array_values($values);
     
        // Its update, remove id and use as where-clause
        unset($keys['id']);
        $values[] = $this->id;
     
        $this->db->update(
            $this->getSource(),
            $keys,
            "id = ?"
        );
     
        return $this->db->execute($values);
    }



}
