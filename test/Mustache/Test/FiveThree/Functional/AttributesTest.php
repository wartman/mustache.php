<?php

/**
 * @group inheritance
 * @group functional
 */
class Mustache_Test_FiveThree_Functional_AttributesTest extends PHPUnit_Framework_TestCase
{
    private $mustache;

    public function setUp()
    {
        $this->mustache = new Mustache_Engine(array(
            'pragmas' => array(
                Mustache_Engine::PRAGMA_BLOCKS,
                Mustache_Engine::PRAGMA_ATTRIBUTES,
            ),
        ));
    }

    /**
     * @dataProvider sectionsWithAttributesData
     */
    public function testSectionsWithAttributes($tpl, $data, $expect)
    {
        $this->assertEquals($expect, $this->mustache->render($tpl, $data));
    }

    public function sectionsWithAttributesData()
    {
        return array(
            array(
                '{{# foo bar="bin" }}{{ bar }}{{/ foo }}',
                array(
                    'foo' => true,
                ),
                'bin',
            ),
            array(
                '{{# foo bar="bin" }}{{ bar }}{{ thing }}{{/ foo }}',
                array(
                    'foo' => array(
                        'thing' => 'bif',
                    ),
                ),
                'binbif',
            ),
            array(
                '{{# foo bar="bin" }}{{ bar }}{{ . }}{{/ foo }}',
                array(
                    'foo' => array(
                        'bif',
                        'bax',
                    ),
                ),
                'binbifbinbax',
            ),
            array(
                '{{# foo bar="bin" }}{{ bar }}{{ . }}{{/ foo }}',
                array(
                    'foo' => 'bif',
                ),
                'binbif',
            ),
            array(
                '{{# foo bar="bin" }}{{ bar }}{{/ foo }}',
                array(
                    'foo' => function ($text, $helper, $attrs) {
                        return '<p>' . $attrs['bar'] . '</p>';
                    },
                ),
                '<p>bin</p>',
            ),
            array(
                '{{# foo bar="ok" }}{{ bar }}{{/ foo }}',
                array(
                    'foo' => function($tpl, $helper, $attrs) {
                        return $helper->renderWith($tpl, $attrs);
                    }
                ),
                'ok'
            ),
            array(
                '{{# foo bar=bin }}{{ bar.ok }}{{/ foo }}',
                array(
                    'foo' => true,
                    'bin' => [
                        'ok' => 'ok'
                    ]
                ),
                'ok'
            )
        );
    }

    /**
     * @dataProvider partialsWithAttributesData
     */
    public function testPartialsWithAttributes($tpl, $data, $partials, $expect)
    {
        $this->mustache->setPartials($partials);
        $this->assertEquals($expect, $this->mustache->render($tpl, $data));
    }

    public function partialsWithAttributesData()
    {
        return array(
            array(
                '{{> foo bar="bin" }}',
                array(),
                array(
                    'foo' => '{{ bar }}',
                ),
                'bin',
            ),
            array(
                '{{< foo bar="bin" }}{{/ foo }}',
                array(),
                array(
                    'foo' => '{{$ bar }}{{ bar }}{{/ bar }}',
                ),
                'bin',
            ),
            array(
                '{{< foo }}{{$ bar bar="bin" }}ok {{ bar }}{{/ bar }}{{/ foo }}',
                array(),
                array(
                    'foo' => '{{$ bar }}{{ bar }}{{/ bar }}',
                ),
                'ok bin',
            ),
        );
    }

    /**
     * @dataProvider varsWithAttributesData
     */
    public function testVarsWithAttributes($tpl, $data, $expect)
    {
        $this->assertEquals($expect, $this->mustache->render($tpl, $data));
    }

    public function varsWithAttributesData()
    {
        return array(
            array(
                '{{ foo bar="foo" }}',
                array(
                    'foo' => function ($attrs) {
                        return $attrs['bar'];
                    },
                ),
                'foo',
            ),
            array(
                '{{ foo bar="1" }}',
                array(
                    'foo' => function ($attrs) {
                        return $attrs['bar'];
                    },
                ),
                '1',
            ),
            array(
                '{{ foo bar="1" }}',
                array(
                    'foo' => 'thing',
                ),
                'thing',
            ),
            array(
                '{{ foo bar="1" }}',
                array(),
                '',
            )
        );
    }

    /**
     * @dataProvider unescapedVarsWithAttributesData
     */
    public function testUnescapedVarsWithAttributes($tpl, $data, $expect)
    {
        $this->assertEquals($expect, $this->mustache->render($tpl, $data));
    }

    public function unescapedVarsWithAttributesData()
    {
        return array(
            array(
                '{{{ foo bar="foo" }}}',
                array(
                    'foo' => function ($attrs) {
                        return $attrs['bar'];
                    },
                ),
                'foo',
            ),
            array(
                '{{{ foo bar="escaped" }}}',
                array(
                    'foo' => function ($attrs) {
                        return $attrs['bar'];
                    },
                ),
                'escaped',
            ),
            array(
                '{{{ foo bar="1" }}}',
                array(
                    'foo' => 'thing',
                ),
                'thing',
            ),
            array(
                '{{{ foo bar="1" }}}',
                array(),
                '',
            ),
        );
    }

    /**
     * @dataProvider varsWithAttributesAndFiltersData
     */
    public function testVarsWithAttributesAndFilters($tpl, $helpers, $data, $expect)
    {
        $this->mustache->setHelpers($helpers);
        $this->assertEquals($expect, $this->mustache->render($tpl, $data));
    }

    public function varsWithAttributesAndFiltersData()
    {
        return array(
            array(
                '{{% FILTERS }}{{ foo bar="foo" | ucase }}',
                array(
                    'ucase' => function ($value) {
                        return ucfirst($value);
                    },
                ),
                array(
                    'foo' => function ($attrs) {
                        return $attrs['bar'];
                    },
                ),
                'Foo',
            ),
        );
    }

    /**
     * @dataProvider classMethodsReceiveAttrsData
     */
    public function testClassMethodsReceiveAttrs($tpl, $data, $expect)
    {
        $this->assertEquals($expect, $this->mustache->render($tpl, $data));
    }

    public function classMethodsReceiveAttrsData()
    {
        return array(
            array(
                '{{ foo bar="foo" }}',
                new Mustache_Test_FiveThree_Functional_AttributesTestStub,
                'foo',
            ),
            array(
                '{{ foo bar="some thing here" }}',
                new Mustache_Test_FiveThree_Functional_AttributesTestStub,
                'some thing here',
            ),
        );
    }

}

class Mustache_Test_FiveThree_Functional_AttributesTestStub
{

    public function foo($attrs)
    {
        return $attrs['bar'];
    }

}
