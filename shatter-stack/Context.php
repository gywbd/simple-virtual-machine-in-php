<?php

class Context {
    public $invokingContext;   //parent in the stack or "caller"
    public $metadata;          //info about function we're execution
    public $returnip;
    public $locals; //args + locals, indexed from 0

    public function __construct($invokingContext, $returnip, $metadata) {
        $this->invokingContext = $invokingContext;
        $this->returnip = $returnip;
        $this->metadata = $metadata;
        $this->locals = array_fill(0,$metadata->nargs + $metadata->nlocals,0);
    }

}
