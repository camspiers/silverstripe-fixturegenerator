<?php

namespace Camspiers\SilverStripe\FixtureGenerator;

class Generator
{
    /**
     * @param DumperInterface $dumper
     */
    public function __construct(DumperInterface $dumper)
    {
        $this->dumper = $dumper;
    }

    public function process(\DataObjectSet $dataObjectSet)
    {
        $data = array();
        if ($dataObjectSet->Count() > 0) {
            foreach ($dataObjectSet as $dataObject) {
                $data = $this->generateFromDataObject($dataObject, $data);
            }
        }

        return $this->dumper->dump(array_reverse($data, true));
    }

    private function generateFromDataObject(\DataObject $dataObject, array $map = array())
    {
        $className = $dataObject->ClassName;
        // If we havn't encontered a object of ClassName, add ClassName to data
        if (!isset($map[$className])) {
            $map[$className] = array();
        }
        // Skip the object if we have already seen it
        if (!isset($map[$className][$dataObject->ID])) {
            // Add the object to the
            $map[$className][$dataObject->ID] = $this->getMap($dataObject);
            // Loop over the has one of this object
            $hasOnes = $dataObject->has_one();
            if ($hasOnes) {
                foreach ($hasOnes as $relName => $relClass) {
                    // Get the dataobject from the relation
                    $hasOne = $dataObject->$relName();
                    // Only process it if it exists
                    if ($hasOne->exists()) {
                        // Recursively generate a map for this object
                        $map = $this->generateFromDataObject($hasOne, $map);
                        // Add the relation to the current dataobjects map
                        $map[$className][$dataObject->ID] = array_merge(
                            $map[$className][$dataObject->ID],
                            array(
                                $relName => "=>$relClass." . $hasOne->ID
                            )
                        );
                    }
                }
            }
            // Loop over the has many relations
            $hasManys = $dataObject->has_many();
            if ($hasManys) {
                foreach ($hasManys as $relName => $relClass) {
                    // Get the dataobjects from the relation
                    $dataObjects = $dataObject->$relName();
                    // If any exist
                    if ($dataObjects instanceof \DataObjectSet && count($dataObjects) > 0) {
                        // Loops of each dataobject
                        foreach ($dataObjects as $hasMany) {
                            // Only process it if it exists
                            if ($hasMany->exists()) {
                                // Recursively generate a map for this object
                                $map = $this->generateFromDataObject($hasMany, $map);
                                // Add the relation to the original objects map
                                if (!isset($map[$className][$dataObject->ID][$relName])) {
                                    $map[$className][$dataObject->ID] = array_merge(
                                        $map[$className][$dataObject->ID],
                                        array(
                                            $relName => "=>$relClass." . $hasMany->ID
                                        )
                                    );
                                } else {
                                    $map[$className][$dataObject->ID][$relName] .= ", =>$relClass." . $hasMany->ID;
                                }

                            }
                        }
                    }
                }
            }
        }

        return $map;
    }

    private function getMap(\DataObject $dataObject)
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
}
