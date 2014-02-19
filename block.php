<?php
/**
 *  @author     Koji Komiya
 *  @desc       block
 */

// 引数は問題文にあったとおりy,xという形で渡される

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

        $this->makeBoard($x, $y);
    }
    // }}}

    // 面積を返す {{{
    public function getArea()
    {
        return $this->x_co * $this->y_co;
    }
    // }}}

    // ボード生成 {{{
    private function makeBoard($x, $y)
    {
        for ($i = 0; $i < $y; $i++) {
            for ($h = 0; $h < $x; $h++) {
                $this->board[$i][$h] = 0;
            }
        }
    }
    // }}}

    // getBoard {{{
    public function getBoard()
    {
        return $this->board;
    }
    // }}}

    // ブロックをはめる {{{
    public function setBlock($block_no, $block_obj)
    {
        $blank = $this->getBlankOrigin();
        if (!$blank) {
            return false;
        }
        $end_x = $blank["xo"] + $block_obj->getXco();
        $end_y = $blank["yo"] + $block_obj->getYco();
        if ($end_x <= $this->x_co && $end_y <= $this->y_co) {
            for ($i = $blank["yo"]; $i < $end_y; $i++) {
                for ($h = $blank["xo"]; $h < $end_x; $h++) {
                    if ($this->board[$i][$h] != 0) {
                        return false;
                    }
                }
            }
            for ($i = $blank["yo"]; $i < $end_y; $i++) {
                for ($h = $blank["xo"]; $h < $end_x; $h++) {
                    $this->board[$i][$h] = $block_no;
                }
            }
        } else {
            return false;
        }
        return true;
    }
    // }}}

    // ブロックを外す {{{
    public function unsetBlock($block_no)
    {
        foreach ($this->board as $y => $row) {
            foreach ($row as $x => $col) {
                if ($col == $block_no) {
                    $this->board[$y][$x] = 0;
                }
            }
        }
    }
    // }}}

    // 空きスペースの左上座標を返す {{{
    private function getBlankOrigin()
    {
        $result = null;
        foreach ($this->board as $y => $row) {
            foreach ($row as $x => $col) {
                if ($col == 0) {
                    $result = array("xo" => $x, "yo" => $y,);
                    break 2;
                }
            }
        }
        return $result;
    }
    // }}}
}

class Block
{
    private $x_co;
    private $y_co;

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

    // xを返す {{{
    public function getXco()
    {
        return $this->x_co;
    }
    // }}}

    // yを返す {{{
    public function getYco()
    {
        return $this->y_co;
    }
    // }}}
}

class Block_Arranger
{
    private $board_obj;
    private $block_obj;
    private $set_list = array();
    private $arrange_result;

    // construct {{{
    public function __construct($argc, $argv)
    {
        if ($argc < 3) {
            $this->error("invalid argument count\n");
        }
        $block_no = 1;
        foreach ($argv as $key => $value) {
            if ($key == 0) {
                continue;
            }
            $condition = explode(",", trim($value));
            if (count($condition) != 2) {
                $this->error("invalid argument format\n");
            }
            if ($key == 1) {
                $this->board_obj = new Board($condition[1], $condition[0]);
            } else {
                $this->block_obj[$block_no] = new Block($condition[1], $condition[0]);
                $block_no++;
            }
        }

        $this->checkArea();
    }
    // }}}

    // check area {{{
    private function checkArea()
    {
        $board_area = $this->board_obj->getArea();
        $block_area_sum = 0;
        foreach ($this->block_obj as $block) {
            $block_area_sum += $block->getArea();
        }
        if ($board_area != $block_area_sum) {
            $this->error("area unmatched\n");
        }
    }
    // }}}

    // 配置する {{{
    private function arrange()
    {
        $block_count = count($this->block_obj);
        while (true) {
            $result = false;
            $start_no = isset($last_block_no) ? ++$last_block_no : 1;
            for ($i = $start_no; $i <= $block_count; $i++) {
                if (in_array($i, $this->set_list)) {
                    continue;
                }
                $result = $this->board_obj->setBlock($i, $this->block_obj[$i]);
                if ($result) {
                    $last_block_no = null;
                    $this->set_list[] = $i;
                    break;
                }
            }
            if (!$result) {
                if (count($this->set_list) == 0) {
                    $this->arrange_result = false;
                    break;
                }
                $last_block_no = end($this->set_list);
                $this->board_obj->unsetBlock($last_block_no);
                array_pop($this->set_list);
            }
            if (count($this->set_list) == $block_count) {
                $this->arrange_result = true;
                break;
            }
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
        if ($this->arrange_result) {
            $board = $this->board_obj->getBoard();
            foreach ($board as $y => $row) {
                foreach ($row as $x => $col) {
                    echo "[" . $col . "]";
                }
                echo "\n";
            }
        } else {
            echo "don't have answer\n";
        }
    }
    // }}}

    // execute {{{
    public function execute()
    {
        $this->arrange();
        $this->output();
    }
    // }}}
}

$obj = new Block_Arranger($argc, $argv);
$obj->execute();
