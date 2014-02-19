<?php
/**
 *  @author     Koji Komiya
 *  @desc       sudoku
 */

class Sudoku_Solver
{
    const MAX_NUM = 9;
    const PROB_PATH = './sudoku.txt';

    private $result_list;
    private $prob_list;
    private $x_co = 0;
    private $y_co = 0;
    private $check_row;
    private $check_column;
    private $check_block;
    private $solve_result;

    // initialize {{{
    private function init()
    {
        $fp_read = @fopen(self::PROB_PATH, 'r');
        if ($fp_read == false) {
            $this->error("can't open file\n");
        }
        $rownum = 0;
        while ($buf = fgets($fp_read)) {
            $row = str_split(trim($buf));
            if (count($row) != self::MAX_NUM) {
                $this->error("invalid format column\n");
            }

            $this->prob_list[] = $row;
            $rownum++;
        }
        fclose($fp_read);

        if ($rownum != self::MAX_NUM) {
            $this->error("invalid format row\n");
        }

        $this->result_list = $this->prob_list;
    }
    // }}}

    // 3*3のマスの左上と右下を返す {{{
    private function getBlockRange()
    {
        $x_seed = intval(floor($this->x_co / 3));
        $y_seed = intval(floor($this->y_co / 3));

        $start_x = $x_seed * 3;
        $start_y = $y_seed * 3;
        $end_x = $start_x + 2;
        $end_y = $start_y + 2;

        return array($start_x, $start_y, $end_x, $end_y);
    }
    // }}}

    // チェックリスト生成 {{{
    private function makeCheckList()
    {
        list($start_x, $start_y, $end_x, $end_y) = $this->getBlockRange();
        $this->check_row = null;
        $this->check_column = null;
        $this->check_block = null;

        foreach ($this->result_list as $y => $row) {
            if ($this->y_co == $y) {
                $this->check_row = $row;
            }
            foreach ($row as $x => $col) {
                if ($this->x_co == $x) {
                    $this->check_column[] = $col;
                }
                if ($start_x <= $x
                    && $x <= $end_x
                    && $start_y <= $y
                    && $y <= $end_y) {
                    $this->check_block[] = $col;
                }
            }
        }
    }
    // }}}

    // 問題に含まれるか {{{
    private function isProblem($x, $y)
    {
        return $this->prob_list[$y][$x] != 0;
    }
    // }}}

    // 対象マスにおけるか {{{
    private function checkNum($num)
    {
        return !in_array($num, $this->check_row) && !in_array($num, $this->check_column) && !in_array($num, $this->check_block);
    }
    // }}}

    // 一ます進む {{{
    private function forward()
    {
        if ($this->x_co + 1 >= self::MAX_NUM) {
            if ($this->y_co + 1 >= self::MAX_NUM) {
                return false;
            }
            $this->x_co = 0;
            $this->y_co++;
        } else {
            $this->x_co++;
        }

        if ($this->isProblem($this->x_co, $this->y_co)) {
            $this->forward();
        }

        return true;
    }
    // }}}

    // 一ます戻る {{{
    private function back()
    {
        if ($this->x_co == 0) {
            if ($this->y_co == 0) {
                return false;
            }
            $this->x_co = self::MAX_NUM - 1;
            $this->y_co--;
        } else {
            $this->x_co--;
        }

        if ($this->isProblem($this->x_co, $this->y_co)) {
            $this->back();
        }

        return true;
    }
    // }}}

    // 問題を解く {{{
    private function solve()
    {
        while(true) {
            // チェック対象ますが問題文に含まれるか調べる
            if ($this->isProblem($this->x_co, $this->y_co)) {
                // 問題文に含まれるなら先に進む
                $can_forward = $this->forward();
                if (!$can_forward) {
                    $this->solve_result = true;
                    break;
                }
            } else {
                $start_num = $this->result_list[$this->y_co][$this->x_co] == 0 ? 1 : $this->result_list[$this->y_co][$this->x_co] + 1;
                $this->result_list[$this->y_co][$this->x_co] = 0;
                if ($start_num > self::MAX_NUM) {
                    // ダメだったら一ます戻る
                    $can_back = $this->back();
                    if (!$can_back) {
                        $this->solve_result = false;
                        break;
                    }
                    continue;
                }

                // チェック対象ますのチェックリストを生成する
                $this->makeCheckList();

                for ($check_num = $start_num; $check_num <= self::MAX_NUM; $check_num++) {
                    $check_result = $this->checkNum($check_num);
                    if ($check_result) {
                        $this->result_list[$this->y_co][$this->x_co] = $check_num;
                        $can_forward = $this->forward();
                        if ($can_forward) {
                            break;
                        } else {
                            $this->solve_result = true;
                            break 2;
                        }
                    }
                }

                if (!$check_result) {
                    // ダメだったら一ます戻る
                    $can_back = $this->back();
                    if (!$can_back) {
                        $this->solve_result = false;
                        break;
                    }
                }
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
        if ($this->solve_result) {
            echo "result\n";
            foreach ($this->result_list as $y => $row) {
                foreach ($row as $x => $col) {
                    if ($this->isProblem($x, $y)) {
                        echo "[" . $col . "]";
                    } else {
                        echo " " . $col . " ";
                    }
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
        $this->init();

        $this->solve();

        $this->output();
    }
    // }}}
}

$obj = new Sudoku_Solver;
$obj->execute();
