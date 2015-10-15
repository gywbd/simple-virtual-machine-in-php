<?php

defined('IADD') or define('IADD',1);           // int add
defined('ISUB') or define('ISUB',2);
defined('IMUL') or define('IMUL',3);
defined('ILT') or define('ILT',4);             // int less than
defined('IEQ') or define('IEQ',5);             // int equal
defined('BR') or define('BR',6);               // branch
defined('BRT') or define('BRT',7);             // branch if true
defined('BRF') or define('BRF',8);             // branch if false
defined('ICONST') or define('ICONST',9);       // push constant integer
defined('LOAD') or define('LOAD',10);          // load from local context
defined('GLOAD') or define('GLOAD',11);        // load from global memory
defined('STORE') or define('STORE',12);        // store in local context
defined('GSTORE') or define('GSTORE',13);      // store in global memory
defined('PRINTS') or define('PRINTS',14);      // print stack top
defined('POP') or define('POP',15);            // throw away top of stack
defined('CALL') or define('CALL',16);
defined('RET') or define('RET',17);
defined('HALT') or define('HALT',18);

class Instruction {
    public $name;
    public $n;

    public function __construct($name,$nargs=0) {
        $this->name = $name;
        $this->n= $nargs;

    }
}

class Bytecode {
    public static $instructions;
}

Bytecode::$instructions = [
    null,// <INVALID>
    new Instruction('iadd'),      // index is the opcode
    new Instruction('isub'),
    new Instruction('imul'),
    new Instruction('ilt'),
    new Instruction('ieq'),
    new Instruction('br',1),
    new Instruction('brt',1),
    new Instruction('brf',1),
    new Instruction('iconst',1),
    new Instruction('load',1),
    new Instruction('gload',1),
    new Instruction('store',1),
    new Instruction('gstore',1),
    new Instruction('prints'),
    new Instruction('pop'),
    new Instruction('call',2), //call addr,nargs
    new Instruction('ret'),
    new Instruction('halt')
];

