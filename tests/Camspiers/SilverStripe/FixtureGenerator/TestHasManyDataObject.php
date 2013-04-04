<?php

class TestHasManyDataObject extends DataObject
{
    public function has_many($component = null, $classOnly = true)
    {
        return array(
            'Items' => 'TestHasOneDataObject'
        );
    }

    public function Items()
    {
        return new \DataObjectSet(
            array(
                new \TestHasOneDataObject(
                    array(
                        'ID'        => 1,
                        'ClassName' => 'TestHasOneDataObject',
                        'Test'      => 'Test'
                    )
                )
            )
        );
    }
}