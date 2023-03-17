<?php
class Automaton {

    protected array $transitions = [
        'q0' => [
            'i' => 'q3',
            'f' => 'q5',
            'w' => 'q8',
            'variable' => 'q1',
            'integer' => 'q2'
        ],
        'q1' => [
            ' ' => 'q0',
            'variable' => 'q1'
        ],
        'q2' => [
            ' ' => 'q0',
            'integer' => 'q2'
        ],
        'q3' => [
            'f' => 'q4',
            ' ' => 'q0',
            'variable' => 'q1'
        ],
        'q4' => [
            ' ' => 'q0',
            'variable' => 'q1'
        ],
        'q5' => [
            'o' => 'q6',
            ' ' => 'q0',
            'variable' => 'q1'
        ],
        'q6' => [
            'r' => 'q7',
            ' ' => 'q0',
            'variable' => 'q1'
        ],
        'q7' => [
            ' ' => 'q0',
            'variable' => 'q1'
        ],
        'q8' => [
            'h' => 'q9',
            ' ' => 'q0',
            'variable' => 'q1'
        ],
        'q9' => [
            'i' => 'q10',
            ' ' => 'q0',
            'variable' => 'q1'
        ],
        'q10' => [
            'l' => 'q11',
            ' ' => 'q0',
            'variable' => 'q1'
        ],
        'q11' => [
            'e' => 'q12',
            ' ' => 'q0',
            'variable' => 'q1'
        ],
        'q12' => [
            ' ' => 'q0',
            'variable' => 'q1'
        ],
    ];

    private array $chars = [];

    private array $variable = [];

    private array $constant = [];

    private int $if = 0;

    private int $for = 0;

    private int $while = 0;

    public function __construct(string $string)
    {
        $this->setChars($string);
    }

    public function getChars(): array
    {
        return $this->chars;
    }

    public function setChars(string $chars): void
    {
        $this->chars = str_split(mb_strtolower(trim($chars)));
    }

    public function getVariable(): array
    {
        return $this->variable;
    }

    public function setVariable(string $variable): void
    {
        $this->variable[] = $variable;
    }

    public function getConstant(): array
    {
        return $this->constant;
    }

    public function setConstant(string $constant): void
    {
        $this->constant[] = $constant;
    }

    public function getIf(): int
    {
        return $this->if;
    }

    public function setIf(): void
    {
        $this->if++;
    }

    public function getFor(): int
    {
        return $this->for;
    }

    public function setFor(): void
    {
        $this->for++;
    }

    public function getWhile(): int
    {
        return $this->while;
    }

    public function setWhile(): void
    {
        $this->while++;
    }

    /**
     * @return array
     */
    public function getTransitions(): array
    {
        return $this->transitions;
    }

    public function test(): bool
    {
        $transitions = $this->getTransitions();
        $chars = $this->getChars();
        $currentString = '';
        $currentState = 'q0';

        foreach ($chars as $key => $char) {
            if (array_key_exists($char, $transitions[$currentState])) {
                if ($char == ' ') {
                    if ($currentState == 'q1') {
                        $this->setVariable($currentString);
                    } else if ($currentState == 'q2') {
                        $this->setConstant($currentString);
                    } else if ($currentState == 'q4') {
                        $this->setIf();
                    } else if ($currentState == 'q7') {
                        $this->setFor();
                    } else if ($currentState == 'q12') {
                        $this->setWhile();
                    }
                    $currentString = '';
                } else {
                    $currentString .= $char;
                }
                $currentState = $transitions[$currentState][$char];
            } else if (is_numeric($char) && array_key_exists('integer', $transitions[$currentState])) {
                $currentString .= $char;
                $currentState = $transitions[$currentState]['integer'];
            } else if (array_key_exists('variable', $transitions[$currentState])) {
                $currentString .= $char;
                $currentState = $transitions[$currentState]['variable'];
            } else {
                return false;
            }

            if ($key == count($chars) - 1) {
                if ($currentString == 'if') {
                    $this->setIf();
                } else if ($currentString == 'for') {
                    $this->setFor();
                } else if ($currentString == 'while') {
                    $this->setWhile();
                } else if (is_numeric($currentString)) {
                    $this->setConstant($currentString);
                } else {
                    $this->setVariable($currentString);
                }
            }
        }

        return true;
    }
}