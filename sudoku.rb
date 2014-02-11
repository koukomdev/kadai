# author Koji Komiya
# desc sudoku

class SudokuSolver
    MAX_NUM = 9
    PROB_PATH = './sudoku.txt'

    @check_row
    @check_column
    @check_block
    @solve_result

    # initialize {{{
    def init
        @prob_list = Array.new
        @x_co = 0
        @y_co = 0
        rownum = 0
        File.open(PROB_PATH, 'r') do |file|
            file.each do |buf|
                row = buf.strip.split('')
                if row.count != MAX_NUM then
                    error("invalid format column")
                end
                @prob_list.push row
                rownum += 1
            end
        end

        if rownum != MAX_NUM then
            error("invalid format row")
        end

        @result_list = @prob_list.map(&:dup)
    end
    # }}}

    # 3*3のマスの左上と右下を返す {{{
    def getBlockRange
        x_seed = (@x_co / 3).to_i
        y_seed = (@y_co / 3).to_i
        start_x = x_seed * 3
        start_y = y_seed * 3
        end_x = start_x + 2
        end_y = start_y + 2
        return start_x,start_y,end_x,end_y
    end
    # }}}

    # error {{{
    def error(message)
        puts message
        exit 1
    end
    # }}}

    # execute {{{
    def execute
        init
    end
    # }}}
end

SudokuSolver.new.execute
