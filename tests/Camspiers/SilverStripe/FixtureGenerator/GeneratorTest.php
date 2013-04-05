<?php

namespace Camspiers\SilverStripe\FixtureGenerator;

use Camspiers\SilverStripe\FixtureGenerator\Dumpers\DataArray;

class GeneratorTest extends \PHPUnit_Framework_TestCase
{
    protected $generator;
    public function setUp()
    {
        $this->generator = new Generator(new DataArray());
    }
    public function testProcessBasic()
    {
        $this->assertEquals(
            array(
                'DataObject' => array(
                    1 => array(
                        'Test' => 'Test'
                    )
                )
            ),
            $this->generator->process(
                new \DataObjectSet(
                    array(
                        new \DataObject(
                            array(
                                'ClassName' => 'DataObject',
                                'ID'        => 1,
                                'Test'      => 'Test'
                            )
                        )
                    )
                )
            )
        );
    }
    public function testProcessHasOne()
    {
        $this->assertEquals(
            array(
                'TestHasOneDataObject' => array(
                    1 => array(
                        'Test'   => 'Test',
                        'Parent' => '=>TestHasManyDataObject.1'
                    )
                ),
                'TestHasManyDataObject' => array(
                    1 => array(
                        'Test' => 'Test'
                    )
                )
            ),
            $this->generator->process(
                new \DataObjectSet(
                    array(
                        new \TestHasOneDataObject(
                            array(
                                'ClassName' => 'TestHasOneDataObject',
                                'ID'        => 1,
                                'Test'      => 'Test'
                            )
                        )
                    )
                )
            )
        );
    }
    public function testProcessHasMany()
    {
        $this->assertEquals(
            array(
                'TestHasManyDataObject' => array(
                    1 => array(
                        'Test'  => 'Test',
                        'Items' => '=>TestHasOneDataObject.1'
                    )
                ),
                'TestHasOneDataObject' => array(
                    1 => array(
                        'Test' => 'Test'
                    )
                )
            ),
            $this->generator->process(
                new \DataObjectSet(
                    array(
                        new \TestHasManyDataObject(
                            array(
                                'ClassName' => 'TestHasManyDataObject',
                                'ID'        => 1,
                                'Test'      => 'Test'
                            )
                        )
                    )
                )
            )
        );
    }
    public function testProcessPatternInclude()
    {
        $g = new Generator(
            new DataArray(),
            array(
                'TestHasManyDataObject.Items'
            )
        );
        $this->assertEquals(
            array(
                'TestHasManyDataObject' => array(
                    1 => array(
                        'Test'  => 'Test',
                        'Items' => '=>TestHasOneDataObject.1'
                    )
                ),
                'TestHasOneDataObject' => array(
                    1 => array(
                        'Test' => 'Test'
                    )
                )
            ),
            $g->process(
                new \DataObjectSet(
                    array(
                        new \TestHasManyDataObject(
                            array(
                                'ClassName' => 'TestHasManyDataObject',
                                'ID'        => 1,
                                'Test'      => 'Test'
                            )
                        )
                    )
                )
            )
        );
        $g = new Generator(
            new DataArray(),
            array(
                'TestHasManyDataObject.*'
            )
        );
        $this->assertEquals(
            array(
                'TestHasManyDataObject' => array(
                    1 => array(
                        'Test'  => 'Test',
                        'Items' => '=>TestHasOneDataObject.1'
                    )
                ),
                'TestHasOneDataObject' => array(
                    1 => array(
                        'Test' => 'Test'
                    )
                )
            ),
            $g->process(
                new \DataObjectSet(
                    array(
                        new \TestHasManyDataObject(
                            array(
                                'ClassName' => 'TestHasManyDataObject',
                                'ID'        => 1,
                                'Test'      => 'Test'
                            )
                        )
                    )
                )
            )
        );
        $g = new Generator(
            new DataArray(),
            array(
                'TestHasManyDataObject.Test'
            )
        );
        $this->assertEquals(
            array(
                'TestHasManyDataObject' => array(
                    1 => array(
                        'Test'  => 'Test'
                    )
                )
            ),
            $g->process(
                new \DataObjectSet(
                    array(
                        new \TestHasManyDataObject(
                            array(
                                'ClassName' => 'TestHasManyDataObject',
                                'ID'        => 1,
                                'Test'      => 'Test'
                            )
                        )
                    )
                )
            )
        );
        $g = new Generator(
            new DataArray(),
            array(
                'TestHasManyDataObject.Item?'
            )
        );
        $this->assertEquals(
            array(
                'TestHasManyDataObject' => array(
                    1 => array(
                        'Test'  => 'Test',
                        'Items' => '=>TestHasOneDataObject.1'
                    )
                ),
                'TestHasOneDataObject' => array(
                    1 => array(
                        'Test' => 'Test'
                    )
                )
            ),
            $g->process(
                new \DataObjectSet(
                    array(
                        new \TestHasManyDataObject(
                            array(
                                'ClassName' => 'TestHasManyDataObject',
                                'ID'        => 1,
                                'Test'      => 'Test'
                            )
                        )
                    )
                )
            )
        );
    }
    public function testProcessPatternExclude()
    {
        $g = new Generator(
            new DataArray(),
            array(
                '*'
            ),
            Generator::RELATION_MODE_EXCLUDE
        );
        $this->assertEquals(
            array(
                'TestHasManyDataObject' => array(
                    1 => array(
                        'Test'  => 'Test'
                    )
                )
            ),
            $g->process(
                new \DataObjectSet(
                    array(
                        new \TestHasManyDataObject(
                            array(
                                'ClassName' => 'TestHasManyDataObject',
                                'ID'        => 1,
                                'Test'      => 'Test'
                            )
                        )
                    )
                )
            )
        );
        $g = new Generator(
            new DataArray(),
            array(
                'TestHasManyDataObject.*'
            ),
            Generator::RELATION_MODE_EXCLUDE
        );
        $this->assertEquals(
            array(
                'TestHasManyDataObject' => array(
                    1 => array(
                        'Test'  => 'Test'
                    )
                )
            ),
            $g->process(
                new \DataObjectSet(
                    array(
                        new \TestHasManyDataObject(
                            array(
                                'ClassName' => 'TestHasManyDataObject',
                                'ID'        => 1,
                                'Test'      => 'Test'
                            )
                        )
                    )
                )
            )
        );
    }
}
