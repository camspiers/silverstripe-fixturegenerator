<?php

namespace Camspiers\SilverStripe\FixtureGenerator;

use DataObject;
use IteratorAggregate;

/**
 * Class Generator
 * @package Camspiers\SilverStripe\FixtureGenerator
 */
class Generator
{
    /**
     * This mode includes relations specified
     */
    const RELATION_MODE_INCLUDE = 0;
    /**
     * This mode excludes relations specified
     */
    const RELATION_MODE_EXCLUDE = 1;
	/**
	 * This mode excludes generation of related objects (however relationships may still be generated)
	 */
	const RELATED_OBJECT_EXCLUDE = 2;
    /**
     * @var DumperInterface
     */
    private $dumper;
    /**
     * @var array
     */
    private $relations;
    /**
     * @var int
     */
    private $mode;
    /**
     * @param DumperInterface $dumper    The objec to dump the output with
     * @param array           $relations An array of shell wildcard patterns
     * @param int             $mode      The mode the patterns should take, include vs. exclude
     */
    public function __construct(
        DumperInterface $dumper = null,
        array $relations = null,
        $mode = self::RELATION_MODE_INCLUDE
    ) {
        $this->dumper = $dumper;
        $this->relations = $relations;
        $this->mode = $mode;
    }
    /**
     * @param IteratorAggregate $set
     * @return mixed
     */
    public function process(IteratorAggregate $set)
    {
        $map = array();
        foreach ($set as $dataObject) {
            if (!$this->hasDataObject($dataObject, $map)) {
                $map = $this->generateFromDataObject($dataObject, $map);
            }
        }

        return $this->dumper->dump(array_reverse($map, true));
    }
    /**
     * @param DataObject $dataObject
     * @param array      $map
     * @return array
     */
    private function generateFromDataObject(DataObject $dataObject, array &$map = array())
    {
        $className = $dataObject->ClassName;
        $id = $dataObject->ID;
        // If we haven't encountered a object of ClassName, add ClassName to data
        if (!isset($map[$className])) {
            $map[$className] = array();
        }
        // Add the object to the
        $map[$className][$id] = $this->getMap($dataObject);
        // Loop over the has one of this object
        if ($hasOnes = $dataObject->has_one()) {
            foreach ($hasOnes as $relName => $relClass) {
                if ($this->isAllowedRelation("$className.$relName")) {
                    // Get the dataobject from the relation
                    $hasOne = $dataObject->$relName();

	                $relClassName = $hasOne->ClassName;

                    // Only process it if it exists
                    if ($hasOne->exists() && !$this->hasDataObject($hasOne, $map)) {
	                    if (($this->mode & self::RELATED_OBJECT_EXCLUDE) === 0) {
		                    // Recursively generate a map for this object
	                        $this->generateFromDataObject($hasOne, $map);
	                    }
                        // Add the relation to the current dataobjects map
                        $map[$className][$id][$relName] = "=>$relClassName." . $hasOne->ID;
                    }
                }
            }
        }
        // Loop over the has many relations
        if ($hasManys = $dataObject->has_many()) {
            foreach ($hasManys as $relName => $relClass) {
                // Get the dataobjects from the relation
                if ($this->isAllowedRelation("$className.$relName")) {
                    $items = $dataObject->$relName();
                    // If any exist
                    if ($items instanceof IteratorAggregate && count($items) > 0) {
                        // Loops of each dataobject
                        foreach ($items as $hasMany) {

	                        $relClassName = $hasMany->ClassName;

                            // Only process it if it exists
                            if ($hasMany->exists() && !$this->hasDataObject($hasMany, $map)) {
	                            if (($this->mode & self::RELATED_OBJECT_EXCLUDE) === 0) {
                                    // Recursively generate a map for this object
	                                $this->generateFromDataObject($hasMany, $map);
	                            }
                                // Add the relation to the original objects map
                                if (!isset($map[$className][$id][$relName])) {
                                    $map[$className][$id] = array_merge(
                                        $map[$className][$id],
                                        array(
                                            $relName => "=>$relClassName." . $hasMany->ID
                                        )
                                    );
                                } else {
                                    $map[$className][$id][$relName] .= ", =>$relClassName." . $hasMany->ID;
                                }
                            }
                        }
                    }
                }
            }
        }
        // Loop over the many many relations
        if ($manyManys = $dataObject->many_many()) {
            foreach ($manyManys as $relName => $relClass) {
                // Get the dataobjects from the relation
                if ($this->isAllowedRelation("$className.$relName")) {
                    $items = $dataObject->$relName();
                    // If any exist
                    if ($items instanceof IteratorAggregate && count($items) > 0) {
                        // Loops of each dataobject
                        foreach ($items as $manyMany) {
                            // Only process it if it exists

	                        $relClassName = $manyMany->ClassName;

                            if ($manyMany->exists() && !$this->hasDataObject($manyMany, $map)) {
	                            if (($this->mode & self::RELATED_OBJECT_EXCLUDE) === 0) {
		                            // Recursively generate a map for this object
	                                $this->generateFromDataObject($manyMany, $map);
	                            }
                                // Add the relation to the original objects map
                                if (!isset($map[$className][$id][$relName])) {
                                    $map[$className][$id] = array_merge(
                                        $map[$className][$id],
                                        array(
                                            $relName => "=>$relClassName." . $manyMany->ID
                                        )
                                    );
                                } else {
                                    $map[$className][$id][$relName] .= ", =>$relClassName." . $manyMany->ID;
                                }
                            }
                        }
                    }
                }
            }
        }

        return $map;
    }
    /**
     * @param DataObject $dataObject
     * @param array      $map
     * @return bool
     */
    private function hasDataObject(DataObject $dataObject, array $map = array())
    {
        return isset($map[$dataObject->ClassName][$dataObject->ID]);
    }
    /**
     * @param DataObject $dataObject
     * @return array
     */
    private function getMap(DataObject $dataObject)
    {
        $map = $dataObject->toMap();
        unset($map['Created']);
        unset($map['LastEdited']);
        unset($map['RecordClassName']);
        unset($map['ClassName']);
        unset($map['ID']);
        foreach ($map as $key => $value) {
            if (substr($key, -2) == 'ID') {
                unset($map[$key]);
            }
        }

        return $map;
    }
    /**
     * @param $relation
     * @return bool
     */
    private function isAllowedRelation($relation)
    {
        if (is_null($this->relations)) {
            return true;
        } else {
            foreach ($this->relations as $pattern) {
                if (fnmatch($pattern, $relation)) {
                    return !$this->mode;
                }
            }

            return (boolean)$this->mode;
        }
    }
}
