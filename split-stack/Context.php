<?php

class Context {
    public $returnip;
    public $locals; //args + locals, indexed from 0

    public function __construct($returnip, $nlocals) {
        $this->returnip = $returnip;
        $this->locals = array_fill(0,$nlocals,0);
    }

}
