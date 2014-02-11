# author Koji Komiya
# desc sudoku

class SudokuSolver
    MAX_NUM = 9
    PROB_PATH = './sudoku.txt'

    # initialize {{{
    def init
        @prob_list = Array.new
        @x_co = 0
        @y_co = 0
        rownum = 0
        File.open(PROB_PATH, 'r') do |file|
            file.each do |buf|
                row = buf.strip.split('').map(&:to_i)
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

    # チェックリスト生成 {{{
    def makeCheckList
        start_x,start_y,end_x,end_y = getBlockRange
        @check_row = Array.new
        @check_column = Array.new
        @check_block = Array.new

        y = 0
        @result_list.each do |row|
            if @y_co == y then
                @check_row = row.clone
            end
            x = 0
            row.each do |col|
                if @x_co == x then
                    @check_column.push col
                end
                if start_x <= x && x <= end_x && start_y <= y && y <= end_y then
                    @check_block.push col
                end
                x += 1
            end
            y += 1
        end
    end
    # }}}

    # 問題に含まれるか {{{
    def isProblem(x, y)
        return @prob_list[y][x] != 0
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
