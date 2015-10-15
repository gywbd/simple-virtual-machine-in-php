<?php

class FuncMetaData {
    public $name;
    public $nargs;
    public $nlocals;
    public $address;

    public function __construct($name, $nargs, $nlocals, $address) {
        $this->name = $name;
        $this->nargs = $nargs;
        $this->nlocals = $nlocals;
        $this->address = $address;
    }
}
