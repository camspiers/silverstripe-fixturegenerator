<?php

class TestHasOneDataObject extends \DataObject
{
    public function has_one($component = null)
    {
        return array(
            'Parent' => 'TestHasManyDataObject'
        );
    }

    public function Parent()
    {
        return new \TestHasManyDataObject(
            array(
                'ID'        => 1,
                'ClassName' => 'TestHasManyDataObject',
                'Test'      => 'Test'
            )
        );
    }
}