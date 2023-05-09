<?php
//require_once 'Page.php';

class Player{
    static private $start_page;
    static private $end_page;
    static private $max_name_length;
    private $name;
    private $redirect_count;
    private $current_page;

    public function __construct($player_number){
        $this->set_name($player_number);
        $this->redirect_count = 0;
        $this->current_page = self::$start_page;
    }

    private function set_name($player_number){
        while (true) {
            print "Имя игрока " . $player_number . ">>";
            $player_name = readline();
            if($player_name != ""){
                break;
            }
        }
        $this->name = $player_name;

        if (strlen($player_name)>self::$max_name_length){
            self::$max_name_length = strlen($player_name);
        }
    }

    public static function set_route_pages(){
        self::$start_page = new Page("https://ru.wikipedia.org/wiki/Служебная:Случайная_страница");
        self::$end_page = new Page("https://ru.wikipedia.org/wiki/Служебная:Случайная_страница");
        while (self::$end_page == self::$start_page){
            self::$end_page = new Page("https://ru.wikipedia.org/wiki/Служебная:Случайная_страница");
        }
        print "\n\nСтартовая страница: " . self::$start_page->get_title() . "\n";
        print "Финишная страница: " . self::$end_page->get_title() . "\n\n";
    }

    public function make_move(){
        print "\n----- " . $this->current_page->get_title() . " -----\n";
        $links_count = $this->current_page->print_links();

        while (true) {
            print "Ход игрока " . $this->name . ">>";
            $page_number = readline();
            if ((is_numeric($page_number)) and ($page_number >= 1) and ($page_number <= $links_count)) {
                break;
            }
        }

        $this->current_page = new Page($this->current_page->get_link_by_number($page_number));
        $this->redirect_count++;

        if (!($this->is_still_playing())){
            print "\n--> " . $this->name . " достиг финишной страницы\n";
            return false;
        }
        return true;
    }

    public function is_still_playing(){
        if ($this->current_page == self::$end_page){
            return false;
        }
        return true;
    }

    public function print_result(){
        print $this->name;
        for ($i=intdiv(strlen($this->name),4); $i<intdiv(self::$max_name_length,4)+1; $i++){
            print "\t";
        }
        print $this->redirect_count . "\n";
    }
}