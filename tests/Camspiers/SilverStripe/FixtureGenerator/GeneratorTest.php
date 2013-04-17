<?php

namespace Camspiers\SilverStripe\FixtureGenerator;

use Camspiers\SilverStripe\FixtureGenerator\Dumpers\DataArray;

class GeneratorTest extends \PHPUnit_Framework_TestCase
{
    protected $dumperStub;
    protected $generator;
    public function setUp()
    {
        $this->dumperStub = $this->getMock(__NAMESPACE__ . '\\DumperInterface');
        $this->dumperStub->expects($this->any())
            ->method('dump')
            ->will($this->returnArgument(0));

        $this->generator = new Generator($this->dumperStub);
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
            $this->dumperStub,
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
            $this->dumperStub,
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
            $this->dumperStub,
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
            $this->dumperStub,
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
            $this->dumperStub,
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
            $this->dumperStub,
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
