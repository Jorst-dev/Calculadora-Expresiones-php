<?php
class Calculadora {
    private string $s;
    private int $i;

    public function evaluar(string $expresion): float {
        $this->s = $expresion;
        $this->i = 0;
        $valor = $this->parseExpr();
        $this->skipBlanks();
        if ($this->i !== strlen($this->s)) {
            throw new Exception("Expresión inválida: sobran caracteres desde posición {$this->i}");
        }
        return $valor;
    }

    private function parseExpr(): float {
        $valor = $this->parseTerm();
        while (true) {
            $this->skipBlanks();
            $c = $this->peek();
            if ($c === '+' || $c === '-') {
                $this->i++;
                $rhs = $this->parseTerm();
                $valor = ($c === '+') ? ($valor + $rhs) : ($valor - $rhs);
            } else {
                break;
            }
        }
        return $valor;
    }

    private function parseTerm(): float {
        $valor = $this->parseFactor();
        while (true) {
            $this->skipBlanks();
            $c = $this->peek();
            if ($c === '*' || $c === '/') {
                $this->i++;
                $rhs = $this->parseFactor();
                if ($c === '*') {
                    $valor *= $rhs;
                } else {
                    if ($rhs == 0.0) {
                        throw new Exception("División por cero");
                    }
                    $valor /= $rhs;
                }
            } else {
                break;
            }
        }
        return $valor;
    }

    private function parseFactor(): float {
        $this->skipBlanks();
        $c = $this->peek();

        // Soporta signo unario
        if ($c === '+' || $c === '-') {
            $this->i++;
            $val = $this->parseFactor();
            return ($c === '-') ? -$val : $val;
        }

        // Paréntesis
        if ($c === '(') {
            $this->i++;
            $val = $this->parseExpr();
            $this->skipBlanks();
            if ($this->peek() !== ')') {
                throw new Exception("Falta cerrar paréntesis en posición {$this->i}");
            }
            $this->i++; // consumir ')'
            return $val;
        }

        // Número (entero o decimal)
        $num = $this->parseNumber();
        return $num;
    }

    private function parseNumber(): float {
        $this->skipBlanks();
        $start = $this->i;
        $dotSeen = false;

        while ($this->i < strlen($this->s)) {
            $c = $this->s[$this->i];
            if ($c >= '0' && $c <= '9') {
                $this->i++;
            } elseif ($c === '.' && !$dotSeen) {
                $dotSeen = true;
                $this->i++;
            } else {
                break;
            }
        }

        if ($this->i === $start) {
            $c = $this->peek();
            if ($c === null) {
                throw new Exception("Se esperaba un número y se alcanzó el final");
            } else {
                throw new Exception("Carácter inválido '{$c}' en posición {$this->i}");
            }
        }

        $lexema = substr($this->s, $start, $this->i - $start);
        return (float)$lexema;
    }

    private function skipBlanks(): void {
        while ($this->i < strlen($this->s) && ctype_space($this->s[$this->i])) {
            $this->i++;
        }
    }

    private function peek(): ?string {
        return ($this->i < strlen($this->s)) ? $this->s[$this->i] : null;
    }
}
