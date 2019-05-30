进口 OS
导入系统
进口 shlex
import ntpath
导入子流程
进口崇高
import sublime_plugin

类 PhpunitTestCommand（sublime_plugin。WindowCommand）：

    lastTestCommand =  False

    def  get_setting（self，key，default = None）：
        return sublime.load_settings（“ Preferences.sublime-settings ”）。get（key，default）

    def  get_cmd_connector（self）：
        如果 ' fish '  ==  self .get_setting（' phpunit-sublime-shell '，' bash '）：
            回归 ' ; 和'
        否则：
            返回 ' && '

    def  get_paths（self）：
        file_name =  self .window.active_view（）。file_name（）
        phpunit_config_path =  self .find_phpunit_config（file_name）

        directory = os.path.dirname（os.path.realpath（file_name））

        file_name = file_name.replace（'  '，' \ '）
        phpunit_config_path = phpunit_config_path.replace（'  '，' \ '）
        phpunit_bin =  self .find_phpunit_bin（phpunit_config_path）

        active_view =  self .window.active_view（）

        return file_name，phpunit_config_path，phpunit_bin，active_view，directory

    def  get_current_function（self，view）：
        sel = view.sel（）[ 0 ]
        function_regions = view.find_by_selector（' entity.name.function '）
        cf =  无
        for r in  reversed（function_regions）：
            如果 ra < sel.a：
                cf = view.substr（r）
                打破
        返回 cf

    def  find_phpunit_config（self，file_name）：
        phpunit_config_path = file_name
        found =  False
        虽然发现==  错误：
            phpunit_config_path = os.path.abspath（os.path.join（phpunit_config_path，os.pardir））
            found = os.path.isfile（phpunit_config_path +  '/ phpunit.xml '）或 os.path.isfile（phpunit_config_path +  '/ phpunit.xml.dist '）或 phpunit_config_path ==  ' / '
        返回 phpunit_config_path

    def  find_phpunit_bin（self，directory）：
        search_paths = [
            ' vendor / bin / phpunit '，
            ' vendor / bin / phpunit / phpunit / phpunit '，
        ]

        found =  False ;
        为路径在 search_paths：
            如果 False  ==发现：
                binpath = os.path.realpath（目录+  “ / ”  +路径）

                如果 os.path.isfile（binpath.replace（“ \\ ”，“ ”））：
                    found =  True

        如果 False  ==发现：
            binpath =  ' phpunit '

        返回 binpath

    def  run_in_terminal（self，command）：
        osascript_command =  ' osascript '

        if  self .get_setting（' phpunit-sublime-terminal '，' Term '）==  ' iTerm '：
            osascript_command + =  ' “ '  + os.path.dirname（os.path.realpath（__file__））+  ' /open_iterm.applescript” '
            osascript_command + =  ' “ '  + command +  ' ” '
        否则：
            osascript_command + =  ' “ '  + os.path.dirname（os.path.realpath（__file__））+  ' /run_command.applescript” '
            osascript_command + =  ' “ '  + command +  ' ” '
            osascript_command + =  ' “PHPUnit Tests” '

        self .lastTestCommand =命令
        使用os.system（osascript_command）

class  RunPhpunitTestCommand（PhpunitTestCommand）：

    def  run（self，* args，** kwargs）：
        file_name，phpunit_config_path，phpunit_bin，active_view，directory =  self .get_paths（）

        self .run_in_terminal（' cd '  + phpunit_config_path +  self .get_cmd_connector（）+ phpunit_bin +  '  '  + file_name）

class  RunAllPhpunitTestsCommand（PhpunitTestCommand）：

    def  run（self，* args，** kwargs）：
        file_name，phpunit_config_path，phpunit_bin，active_view，directory =  self .get_paths（）

        self .run_in_terminal（' cd '  + phpunit_config_path +  self .get_cmd_connector（）+ phpunit_bin）


class  RunSinglePhpunitTestCommand（PhpunitTestCommand）：

    def  run（self，* args，** kwargs）：
        file_name，phpunit_config_path，phpunit_bin，active_view，directory =  self .get_paths（）

        current_function =  self .get_current_function（active_view）

        self .run_in_terminal（' cd '  + phpunit_config_path +  self .get_cmd_connector（）+ phpunit_bin +  '  '  + file_name +  “ - filter'/ :: ”  + current_function +  “ $ /' ”）

class  RunLastPhpunitTestCommand（PhpunitTestCommand）：

    def  run（self，* args，** kwargs）：
        file_name，phpunit_config_path，phpunit_bin，active_view，directory =  self .get_paths（）

        如果 “测试” 在 FILE_NAME：
            RunSinglePhpunitTestCommand.run（self，args，kwargs）;
        elif  self .lastTestCommand：
            self .run_in_terminal（self .lastTestCommand）

class  RunPhpunitTestsInDirCommand（PhpunitTestCommand）：

    def  run（self，* args，** kwargs）：
        file_name，phpunit_config_path，phpunit_bin，active_view，directory =  self .get_paths（）

        self .run_in_terminal（' cd '  + phpunit_config_path +  self .get_cmd_connector（）+ phpunit_bin +  '  '  +目录）

class  RunSingleDuskTestCommand（PhpunitTestCommand）：

    def  run（self，* args，** kwargs）：
        file_name，phpunit_config_path，active_view，directory =  self .get_paths（）

        current_function =  self .get_current_function（active_view）

        self .run_in_terminal（' cd '  + phpunit_config_path +  self .get_cmd_connector（）+  ' php artisan dusk '  + file_name +  '-- filter '  + current_function）

class  RunAllDuskTestsCommand（PhpunitTestCommand）：

    def  run（self，* args，** kwargs）：
        file_name，phpunit_config_path，active_view，directory =  self .get_paths（）

        self .run_in_terminal（' cd '  + phpunit_config_path +  self .get_cmd_connector（）+  ' php artisan dusk '）

class  RunDuskTestsInDirCommand（PhpunitTestCommand）：

    def  run（self，* args，** kwargs）：
        file_name，phpunit_config_path，active_view，directory =  self .get_paths（）

        self .run_in_terminal（' cd '  + phpunit_config_path +  self .get_cmd_connector（）+  ' php artisan dusk '  +目录）


类 FindMatchingTestCommand（sublime_plugin。WindowCommand）：

    def  path_leaf（self，path）：
        head，tail = ntpath.split（path）
        return tail 或 ntpath.basename（head）

    def  run（self，* args，** kwargs）：
        file_name =  self .window.active_view（）。file_name（）
        file_name =  self .path_leaf（file_name）
        file_name = file_name [ 0：file_name.find（'。'）]]
        tab_target =  0

        如果 “测试” 未 在 FILE_NAME：
            file_name = file_name +  ' Test '
        否则：
            ＃剥离'测试'并添加'。' 强制匹配非测试文件
            file_name = file_name [ 0：file_name.find（' Test '）] +  '。“
            tab_target =  1

        ＃大脏宏 - 黑客攻击。最后我应该以某种方式打开文件
        ＃合乎逻辑的方法。
        自 .window.run_command（ “ set_layout ”，{ “细胞”：[[ 0，0，1，1 ]，[ 1，0，2，1 ]]， “ COLS ”：[ 0.0，0.5，1.0 ]， “行“：[ 0.0，1.0 ]}）
        self .window.run_command（“ focus_group ”，{ “ group ”：tab_target}）
        self .window.run_command（“ show_overlay ”，{ “ overlay ”：“ goto ”，“ text ”：file_name，“ show_files ”：“ true ” }）
        self .window.run_command（“ move ”，{ “ by ”：“ lines ”，“ forward ”：False }）

        ＃这是一个肮脏的黑客，让它切换文件...无法模拟'回车'
        ＃但是再次触发叠加以关闭它似乎具有相同的效果。
        self .window.run_command（“ show_overlay ”，{ “ overlay ”：“ goto ”，“ show_files ”：“ true ” }）
        self .window.run_command（“ focus_group ”，{ “ group ”：0 }）
        self .window.run_command（“ focus_group ”，{ “ group ”：tab_target}）

