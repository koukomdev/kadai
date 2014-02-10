<?php
/**
 *  @author     Koji Komiya
 *  @desc       block
 */

class Board
{
    private $x_co;
    private $y_co;
    private $board;

    // construct {{{
    public function __construct($x, $y)
    {
        $this->x_co = $x;
        $this->y_co = $y;
    }
    // }}}

    // 面積を返す {{{
    public function getArea()
    {
        return $this->x_co * $this->y_co;
    }
    // }}}
}

class Block
{
    private $x_co;
    private $y_co;
    private $x_start;
    private $y_start;
    private $x_end;
    private $y_end;

    // construct {{{
    public function __construct($x, $y)
    {
        $this->x_co = $x;
        $this->y_co = $y;
    }
    // }}}

    // 面積を返す {{{
    public function getArea()
    {
        return $this->x_co * $this->y_co;
    }
    // }}}
}

class Block_Arranger
{
    private $area_obj;
    private $block_obj;

    // construct {{{
    public function __construct($argc, $argv)
    {
        if ($argc < 3) {
            $this->error("invalid argument count\n");
        }
        foreach ($argv as $key => $value) {
            if ($key == 0) {
                continue;
            }
            $condition = explode(",", trim($value));
            if (count($condition) != 2) {
                $this->error("invalid argument format\n");
            }
            if ($key == 1) {
                $this->area_obj = new Board($condition[1], $condition[0]);
            } else {
                $this->block_obj[] = new Block($condition[1], $condition[0]);
            }
        }

        $this->checkArea();
    }
    // }}}

    // check area {{{
    private function checkArea()
    {
        $board_area = $this->area_obj->getArea();
        $block_area_sum = 0;
        foreach ($this->block_obj as $block) {
            $block_area_sum += $block->getArea();
        }
        if ($board_area != $block_area_sum) {
            $this->error("area unmatched\n");
        }
    }
    // }}}

    // error {{{
    private function error($message)
    {
        fputs(STDERR, $message);
        exit;
    }
    // }}}

    // output {{{
    private function output()
    {
    }
    // }}}

    // execute {{{
    public function execute()
    {
        $this->output();
    }
    // }}}
}

$obj = new Block_Arranger($argc, $argv);
$obj->execute();
