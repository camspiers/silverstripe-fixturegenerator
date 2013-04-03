<?php

class TestHasOneDataObject extends \DataObject
{
    public function has_one($component = null)
    {
        return array(
            'Parent' => 'DataObject'
        );
    }

    public function Parent()
    {
        return new \DataObject(
            array(
                'ID'        => 1,
                'ClassName' => 'DataObject',
                'Test'      => 'Test'
            )
        );
    }
}