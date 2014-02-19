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
        unless File.exist?(PROB_PATH)
            error("can't open file")
        end
        File.open(PROB_PATH, 'r') do |file|
            file.each do |buf|
                row = buf.strip.split('').map(&:to_i)
                if row.count != MAX_NUM
                    error("invalid format column")
                end
                @prob_list.push row
                rownum += 1
            end
        end

        if rownum != MAX_NUM
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
            if @y_co == y
                @check_row = row.clone
            end
            x = 0
            row.each do |col|
                if @x_co == x
                    @check_column.push col
                end
                if start_x <= x && x <= end_x && start_y <= y && y <= end_y
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

    # 対象マスにおけるか {{{
    def checkNum(num)
        return !@check_row.include?(num) && !@check_column.include?(num) && !@check_block.include?(num)
    end
    # }}}

    # 一ます進む {{{
    def forward
        if @x_co + 1 >= MAX_NUM
            if @y_co + 1 >= MAX_NUM
                return false
            end
            @x_co = 0
            @y_co += 1
        else
            @x_co += 1
        end

        if isProblem(@x_co, @y_co)
            forward
        end

        return true
    end
    # }}}

    # 一ます戻る {{{
    def back
        if @x_co == 0
            if @y_co == 0
                return false
            end
            @x_co = MAX_NUM - 1
            @y_co -= 1
        else
            @x_co -= 1
        end

        if isProblem(@x_co, @y_co)
            back
        end

        return true
    end
    # }}}

    # 問題を解く {{{
    def solve
        catch(:finish) {
            while true
                # チェック対象ますが問題文に含まれるか調べる
                if isProblem(@x_co, @y_co)
                    # 問題文に含まれるなら先に進む
                    can_forward = forward
                    unless can_forward
                        @solve_result = true
                        break
                    end
                else
                    start_num = @result_list[@y_co][@x_co] == 0 ? 1 : @result_list[@y_co][@x_co] + 1
                    @result_list[@y_co][@x_co] = 0
                    if start_num > MAX_NUM
                        # ダメだったら一ます戻る
                        can_back = back
                        unless can_back
                            @solve_result = false
                            break
                        end
                        next
                    end

                    # チェック対象ますのチェックリストを生成する
                    makeCheckList

                    for check_num in start_num..MAX_NUM do
                        check_result = checkNum(check_num)
                        if check_result
                            @result_list[@y_co][@x_co] = check_num
                            can_forward = forward
                            if can_forward
                                break
                            else
                                @solve_result = true
                                throw :finish
                            end
                        end
                    end

                    unless check_result
                        # ダメだったら一ます戻る
                        can_back = back
                        unless can_back
                            @solve_result = false
                            break
                        end
                    end
                end
            end
        }
    end
    # }}}

    # error {{{
    def error(message)
        puts message
        exit 1
    end
    # }}}

    # output {{{
    def output
        @solve_result = true
        if @solve_result
            puts "result"
            y = 0
            @result_list.each do |row|
                x = 0
                row.each do |col|
                    if isProblem(x, y)
                        print "[#{col}]"
                    else
                        print " #{col} "
                    end
                    x += 1
                end
                print "\n"
                y += 1
            end
        else
            puts "don't have answer"
        end
    end
    # }}}

    # execute {{{
    def execute
        init
        solve
        output
    end
    # }}}
end

SudokuSolver.new.execute
