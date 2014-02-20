# author Koji Komiya
# desc block

# 引数は問題文にあったとおりy,xという形で渡される

class Board
    # initialize {{{
    def initialize(x, y)
        @x_co = x
        @y_co = y

        makeBoard(x, y)
    end
    # }}}

    # 面積を返す {{{
    def getArea
        return @x_co * @y_co
    end
    # }}}

    # ボード生成 {{{
    def makeBoard(x, y)
        @board = Array.new(y).map {Array.new(x, 0)}
    end
    # }}}

    # getBoard {{{
    def getBoard
        return @board
    end
    # }}}

    # ブロックをはめる {{{
    def setBlock(block_no, block_obj)
        blank = getBlankOrigin
        unless blank
            return false
        end
        end_x = blank['xo'] + block_obj.getXco
        end_y = blank['yo'] + block_obj.getYco
        if end_x <= @x_co && end_y <= @y_co
            for i in blank['yo']..(end_y - 1) do
                for h in blank['xo']..(end_x - 1) do
                    if @board[i][h] != 0
                        return false
                    end
                end
            end
            for i in blank['yo']..(end_y - 1) do
                for h in blank['xo']..(end_x - 1) do
                    @board[i][h] = block_no
                end
            end
        else
            return false
        end
        return true
    end
    # }}}

    # ブロックを外す {{{
    def unsetBlock(block_no)
        @board.each_with_index do |row, y|
            row.each_with_index do |col, x|
                if col == block_no
                    @board[y][x] = 0
                end
            end
        end
    end
    # }}}

    # 空きスペースの左上座標を返す {{{
    def getBlankOrigin
        @board.each_with_index do |row, y|
            row.each_with_index do |col, x|
                if col == 0
                    blank = {'xo' => x, 'yo' => y}
                    return blank
                end
            end
        end

        return nil
    end
    # }}}
end

class Block
    # initialize {{{
    def initialize(x, y)
        @x_co = x
        @y_co = y
    end
    # }}}

    # 面積を返す {{{
    def getArea
        return @x_co * @y_co
    end
    # }}}

    # xを返す {{{
    def getXco
        return @x_co
    end
    # }}}

    # yを返す {{{
    def getYco
        return @y_co
    end
    # }}}
end

class Block_Arranger
    # initialize {{{
    def initialize(arg)
        if arg.count < 2
            error("invalid argument count")
        end
        block_no = 1
        @block_obj = {}
        @set_list = Array.new
        arg.each_with_index do |value, key|
            condition = value.split(',').map(&:to_i)
            if condition.count != 2
                error("invalid argument format")
            end
            if key == 0
                @board_obj = Board.new(condition[1], condition[0])
            else
                @block_obj[block_no] = Block.new(condition[1], condition[0])
                block_no += 1
            end
        end

        checkArea
    end
    # }}}

    # check area {{{
    def checkArea
        board_area = @board_obj.getArea
        block_area_sum = 0
        @block_obj.each do |block_no, block|
            block_area_sum += block.getArea
        end
        if board_area != block_area_sum
            error("area unmatched")
        end
    end
    # }}}

    # 配置する {{{
    def arrange
        block_count = @block_obj.count
        last_block_no = nil
        while true
            result = false
            if !last_block_no.nil?
                last_block_no += 1
            end
            start_no = !last_block_no.nil? ? last_block_no : 1
            for i in start_no..block_count do
                if @set_list.include?(i)
                    next
                end
                result = @board_obj.setBlock(i, @block_obj[i])
                if result
                    last_block_no = nil
                    @set_list.push(i)
                    break
                end
            end
            unless result
                if @set_list.count == 0
                    @arrange_result = false
                    break
                end
                last_block_no = @set_list.last
                @board_obj.unsetBlock(last_block_no)
                @set_list.pop
            end
            if @set_list.count == block_count
                @arrange_result = true
                break
            end
        end
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
        if @arrange_result
            color_list = {0 => '#ffffff', 1 => '#d3d3d3', 2 => '#4169e1', 3 => '#008b8b', 4 => '#bdb76b', 5 => '#ffa500', 6 => '#a52a2a', 7 => '#dc143c', 8 => '#ee82ee', 9 => '#4b0082'}

puts <<DOC1
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8" />
<title></title>
</head>
<body>
<table border=1 width=300 align=center>
DOC1

            @board_obj.getBoard.each_with_index do |row, y|
                puts '<tr>'
                row.each_with_index do |col, x|
                    color = color_list[col % 10]
                    puts "<td bgcolor='#{color}'>#{col}</td>"
                end
                puts '</tr>'
            end

puts <<DOC2
</table>
</body>
</html>
DOC2
        else
            puts "don't have answer"
        end
    end
    # }}}

    # execute {{{
    def execute
        arrange
        output
    end
    # }}}
end

Block_Arranger.new(ARGV).execute
