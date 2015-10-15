<?php

include "./Bytecode.php";
include "./VM.php";
include "./Context.php";
include "./FuncMetaData.php";

class Test {
    static $hello = [
        ICONST, 1,
        ICONST, 2,
        IADD,
        PRINTS,
        HALT
    ];

    static $loop = [
    // .GLOBALS 2; N, I
    // N = 10                ADDRESS
           ICONST, 10,      //0
           GSTORE, 0,       //2
    // I = 0
           ICONST, 0,       //4
           GSTORE, 1,       //6
    // WHILE I < N :
    // START (8) :
           GLOAD,  1,       //8
           GLOAD,  0,       //10
           ILT,             //12
           BRF,   24,       //13
    //    I = I + 1
           GLOAD, 1,        //15
           ICONST, 1,       //17
           IADD,            //19
           GSTORE,1,        //20
           BR,  8,          //22
    // DONE (24):
    // PRINT "LOOPED "+N+" TIMES."
           HALT             //24
    ];

    const FACTORIAL_INDEX = 0;
    const FACTORIAL_ADDRESS = 0;
    const MAIN_ADDRESS = 21;

    static $factorial = [
//.def fact: ARGS=1, LOCALS=0           ADDRESS
//      IF N < 2 RETURN 1
            LOAD,    0,                 //0
            ICONST,  2,                  //2
            ILT,                         //4
            BRF,  10,                    //5
            ICONST, 1,                   //7
            RET,                         //9
//CONT:
//      RETURN N * FACT(N-1)
            LOAD, 0,                    //10
            LOAD, 0,                    //12
            ICONST, 1,                   //14
            ISUB,                        //16
            CALL, self::FACTORIAL_INDEX,       //17
            IMUL,                        //19
            RET,                         //20
//.DEF MAIN: ARGS=0 LOCAL=0
// PRINT   FACT(1)
            ICONST, 5,                   //21    <-- MAIN METHOD!
            CALL, self::FACTORIAL_INDEX,    //23
            PRINTS,                      //25
            HALT                         //26
    ];

    static $factorial_metadata = [];


    static $f = [
        //                                  Address
        //.def main() { print f(10); }
        ICONST, 10,                      //0
        CALL, 0,                         //2
        PRINTS,                          //4
        HALT,                            //5

        //.def f(x): ARGS=1, LOCALS=1
        // a = x
        LOAD, 0,                          //6    <-- start of f
        STORE, 1,

        // return 2*a
        LOAD, 1,
        ICONST, 2,
        IMUL,
        RET
    ];

    static $f_metadata = [];

    public static function run() {
        $vm = new VM(static::$hello, 0, 0);
        $vm->trace = true;
        $vm->exec();
        $vm->dumpCodeMemory();

        $vm = new VM(static::$loop, 0, 2);
        $vm->trace = true;
        $vm->exec();

        static::$factorial_metadata[] = new FuncMetaData('factorial',1,0,self::FACTORIAL_ADDRESS);
        $vm = new VM(static::$factorial,self::MAIN_ADDRESS,0,static::$factorial_metadata);
        $vm->trace = true;
        $vm->exec();

        static::$f_metadata[] =  new FuncMetaData("f", 1, 1, 6);
        $vm = new VM(static::$f, 0, 2, static::$f_metadata);
        $vm->trace = true;
        $vm->exec();
    }
}

Test::run();
