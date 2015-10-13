<?php
class VM {
    const DEFAULT_STACK_SIZE = 1000;
    const TRUE = 1;
    const FALSE = 0;

    //registers
    public $ip;          // instruction pointer register
    public $sp = -1;     // stack pointer register
    public $fp = -1;     // frame pointer register

    public $startip = 0;    //where execution begins

    //memory
    public $code = [];             // word-addressable code memory but still bytecodes.
    public $globals = [];          // global variable space
    private $globals_max_length;   // total length of the global space

    public $stack = [];            // Operand stack, grows upwards
    private $stack_max_length;

    public $trace = false;

    public function __construct($code, $startip, $nglobals) {
        $this->code = $code;
        $this->startip = $startip;
        $this->globals_max_length = $nglobals;
        $this->stack_max_length = self::DEFAULT_STACK_SIZE;
    }

    public function exec() {
        $this->ip = $this->startip;
        $this->cpu();
    }

    //simulate the fetch-decode execute cycle
    protected function cpu() {
        $opcode = $this->code[$this->ip];
        $a = $b = $addr = $offset = 0;

        while($opcode != HALT && $this->ip < count($this->code)) {
            if($this->trace) {
                printf("%-35s",$this->disInstr());
            }

            $this->ip++;  //jump to next instruction or to operand
            switch($opcode) {
                case IADD :
                    $b = $this->stack[$this->sp--]; //2nd opnd at top of stack
                    $a = $this->stack[$this->sp--]; //1st opnd 1 below top
                    $this->stack[++$this->sp] = $a + $b; //push result
                    break;
                case ISUB :
                    $b = $this->stack[$this->sp--];
                    $a = $this->stack[$this->sp--];
                    $this->stack[++$this->sp] = $a - $b;
                    break;
                case IMUL :
                    $b = $this->stack[$this->sp--];
                    $a = $this->stack[$this->sp--];
                    $this->stack[++$this->sp] = $a * $b;
                    break;
                case ILT :
                    $b = $this->stack[$this->sp--];
                    $a = $this->stack[$this->sp--];
                    $this->stack[++$this->sp] = $a < $b ? self::TRUE : self::FALSE;
                    break;
                case IEQ :
                    $b = $this->stack[$this->sp--];
                    $a = $this->stack[$this->sp--];
                    $this->stack[++$this->sp] = $a == $b ? self::TRUE : self::FALSE;
                    break;
                case BR :
                    $this->ip = $this->code[$this->ip++];
                    break;
                case BRT :
                    $addr = $this->code[$this->ip++];
                    if($this->stack[$this->sp--] == self::TRUE) {
                        $this->ip = $addr;
                    }
                    break;
                case BRF:
                    $addr = $this->code[$this->ip++];
                    if($this->stack[$this->sp--] == self::FALSE) {
                        $this->ip = $addr;
                    }
                    break;
                case ICONST :
                    $this->stack[++$this->sp] = $this->code[$this->ip++]; //push operand
                    break;
                case LOAD : // load local or arg; 1st local is fp+1, args are fp-3, fp-4, fp-5, ...
                    $offset = $this->code[$this->ip++];
                    $this->stack[++$this->sp] = $this->stack[$this->fp + $offset];
                    break;
                case GLOAD : //load from global memory
                    $addr = $this->code[$this->ip++];
                    $this->stack[++$this->sp] = $this->globals[$addr];
                    break;
                case STORE :
                    $offset = $this->code[$this->ip++];
                    $this->stack[$this->fp + $offset] = $this->stack[$this->sp--];
                    break;
                case GSTORE :
                    $addr = $this->code[$this->ip++];
                    $this->globals[$addr] = $this->stack[$this->sp--];
                    break;
                case PRINTS:
                    echo $this->stack[$this->sp--];
                    $this->br();
                    break;
                case POP:
                    --$this->sp;
                    break;
                default :
                    throw new Exception("invalid opcode: " + $opcode + " at ip=" + ($this->ip - 1));
            }

            if($this->trace) {
                echo $this->stackString();
                $this->br();

            }

            $opcode = $this->code[$this->ip];
        }

        if($this->trace) {
            printf("%-35s", $this->disInstr());
        }

        if($this->trace) {
            echo $this->stackString();
            $this->br();
        }

        if($this->trace) {
            $this->dumpDataMemory();
        }
    }

    protected function stackString() {
        $buf = [];
        $buf[] = 'stack=[';

        for($i=0; $i <= $this->sp; $i++) {
            $o = $this->stack[$i];
            $buf[] = " ";
            $buf[] = $o;
        }

        $buf[] = " ]";

        return implode('',$buf);
    }

    protected function disInstr() {
        $opcode = $this->code[$this->ip];
        $opName = Bytecode::$instructions[$opcode]->name;
        $buf = [];
        $buf[] = sprintf("%04d:\t%-11s",$this->ip,$opName);

        $nargs = Bytecode::$instructions[$opcode]->n;

        if($nargs > 0) {
            $operands = [];
            for($i = $this->ip + 1; $i < $this->ip + $nargs;$i++) {
                $operands[] = $this->code[i];
            }

            for($i=0; $i < count($operands); $i++) {
                $s = $operands[$i];
                if($i > 0) {
                    $buf[] = ", ";
                }
                $buf[] = $s;
            }
        }

        return implode('',$buf);
    }

    protected function dumpDataMemory() {
        echo "Data memory:";
        $this->br();
        $addr = 0;

        foreach($this->globals as $o) {
            printf("%04d: %s", $addr, $o);
            $this->br();
            $addr++;
        }

        $this->br();
    }

    public function dumpCodeMemory() {
        echo "Code memory:";
        $this->br();

        $addr = 0;

        foreach($this->code as $o) {
            printf("%04d: %d", $addr, $o);
            $this->br();
            $addr++;
        }

        $this->br();
    }

    private function br() {
        if(php_sapi_name() === 'cli') {
            echo "\n";
        }else {
            echo "<br/>";
        }
    }
}
