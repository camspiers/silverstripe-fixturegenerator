<?php

class TestHasManyDataObject extends DataObject
{
    public function has_many($component = null, $classOnly = true)
    {
        return array(
            'Items' => 'DataObject'
        );
    }

    public function Items()
    {
        return new \DataObjectSet(
            array(
                new \DataObject(
                    array(
                        'ID'                      => 1,
                        'ClassName'               => 'DataObject',
                        'Test'                    => 'Test',
                        'TestHasManyDataObjectID' => 1
                    )
                )
            )
        );
    }
}