<?php

class Attributes
{
    public function foo($attrs)
    {
        return $attrs['bar'];
    }

    public $bar = array(
        array('label' => 'thing'),
        array('label' => 'other_thing')
    );

    public $bin = array(
        'one',
        'two',
    );
}
