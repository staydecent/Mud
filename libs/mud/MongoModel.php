<?php

namespace Mud;

use Mongo,
    MongoDate,
    MongoID,
    MongoException;

class MongoModel {

    public $created_at,
           $updated_at,
           $objNew;        // update obj

    protected $_m,         // The mongo instance
              $_db,        // Db instance 
              $_collection,// Collection instance
              $_id;        // current doc id

    /**
     * Create a new Mongo object, connection to the desired collection
     * from the desired database.
     *
     * @param  string $db the name of the database
     * @param  string $collection the name of the collection
     * @return void
     */
    public function set_collection($db, $collection) 
    {
        try 
        {
            $this->_m = new Mongo(DB_HOST);
        }
        catch (MongoConnectionException $e) 
        {
            throw new Exception("Error connecting to MongoDB: ".$e->getMessage());
        }

        $this->_db = $this->_m->$db;
        $this->_collection = $this->_db->$collection;
    }

    /**
     * Return the collection.
     *
     * @return MongoCollection 
     */
    public function get_collection() 
    {
        return $this->_collection;
    }

    /**
     * Named array to set the content of each found property.
     *
     * @param  array $properties
     * @return void 
     */
    public function set_properties($properties) 
    {
        foreach ($properties as $property => $value) 
        {
            $this->$property = $value;
        }
    }

    /**
     * Get all properties that would be saved.
     * Ignores properties prefixed with an underscore.
     *
     * @return array 
     */
    public function get_properties() 
    {
        $array = get_object_vars($this);

        foreach ($array as $k => $v) 
        {
            // haystack, needle
            $pos = strpos($k, '_');

            if ($pos === 0)
            {
                unset($array[$k]);
            }
        }

        return $array;
    }

    /**
     * Update the objNew: an array of Mongo commands => properties.
     *
     * @return void 
     */
    private function update_objNew() 
    {
        foreach ($this->objNew as $cmd => $pairs) 
        {
            foreach ($pairs as $k => $v) 
            {
                if (empty($this->$k))
                {
                    unset($this->objNew[$cmd][$k]);
                }
                else
                {
                    $this->objNew[$cmd][$k] = $this->$k;
                }
            }
        }
    }

    /**
     * Wrapper for MongoCollection::find()
     * Querys this collection, returning a MongoCursor for the result set
     *
     * @see    http://ca.php.net/manual/en/mongocollection.find.php
     * @param  array $query The fields for which to search
     * @param  array $fields Fields of the results to return.
     * @param  bool $sort
     * @return MongoCursor 
     */
    public function find($query, $fields, $sort = FALSE) 
    {
        $cursor = $this->_collection->find($query, $fields);

        if (is_array($sort))
        {
            $cursor->sort($sort);
        }

        $this->set_properties($cursor);

        return $cursor;
    }

    /**
     * Wrapper for MongoCollection::findOne()
     * Querys this collection, returning a single element
     *
     * @see    http://ca.php.net/manual/en/mongocollection.findone.php
     * @param  array $query The fields for which to search
     * @param  array $fields Fields of the results to return.
     * @return array 
     */
    public function find_one($query, $fields = array('_id')) 
    {
        $match = $this->_collection->findOne($query, $fields);

        if ($match !== NULL)
        {
            $this->set_properties($match);
        }

        return $match;
    }

    /**
     * Check if a document exists by the specified property.
     *
     * @param  string $property
     * @return bool 
     */
    public function exists($property) 
    {
        if (empty($property))
        {
            return FALSE;
        }

        $match = $this->find_one(array($property=>$this->$property), array('_id'));

        if ($match === NULL)
        {
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }

    /**
     * Return the document ID or FALSE.
     *
     * @return mixed 
     */
    public function id() 
    {
        if ( ! isset($this->_id)) 
        {
            $match = $this->find_one(array('email'=>$this->email), array('_id'));

            if ($match === NULL) 
            {
                return FALSE;
            }
            else 
            {
                $this->_id = $match['_id'];
            }
        }
        
        return $this->_id;
    }

    /**
     * Insert or update a document.
     *
     * @return bool 
     */
    public function save() 
    {
        Event::fire('before_save');

        $update = isset($this->_id) && $this->_id InstanceOf MongoID;
        $array = $this->get_properties();
        $date = new MongoDate();

        if ($update) 
        {
            Event::fire('before_update');

            $this->updated_at = $date;
            $this->update_objNew();

            $this->_collection->update(array('_id'=>$this->id()), $this->objNew);
        }
        else 
        {
            Event::fire('before_insert');

            $array['created_at'] = $date;
            $array['updated_at'] = $date;

            $this->_collection->insert($array);   
            $this->id();
        }

        Event::fire('after_save');

        return TRUE;
    }
}