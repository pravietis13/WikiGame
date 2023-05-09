<?php
//TODO класс Page не должен отвечать за вывод данных (в консоль например)
//TODO в идеале - только за хранение и преобразование данных Page
class Page{
    private $title;
    private $html;
    private $links;

    public function __construct($url){
        $this->get_html($url);
        $this->links = [];
        $this->parse_title();
        $this->parse_links();
    }

    private function get_html($url){
        $attempt = 0;
        while (true) {
            $attempt++;
            print $url;
            $file_get_contents = null;
            try {
                $file_get_contents = file_get_contents($url);
            } catch (Exception $e) {
                print $e->getMessage();
            }
            if(($this->html = $file_get_contents) !== false){
                break;
            }
            if ($attempt == 1){
                print "\nПодключение. Попытка";
            }
            print "->" . $attempt;
            if($attempt == 5){
                print "\nНе удалось подключиться. Повторить попытку? (y/n)>>";
                switch (readline()){
                    case "Y":
                    case "y": $attempt = 0; break;
                    case "N":
                    case "n":
                    default: exit();
                }
            }
        }
    }

    private function parse_title(){
        $title_position = strpos($this->html,"mw-page-title-main") + 20;
        $title = "";
        while(substr($this->html,$title_position,1) != "<"){
            $title = $title . substr($this->html,$title_position,1);
            $title_position++;
        }
        $this->title = $title;
    }

    private function parse_links(){ //TODO чето сделть с этой функцией)
        //TODO МБ заменить на поиск по регулярке - <a (.*)href="(.*)"(.*)>(.*)</a>
        $content_position = strpos($this->html, "<div id=\"mw-content-text\"")+30;
        $link_number = 1;
        while(strpos($this->html,"<noscript>", $content_position) > strpos($this->html, "<a href=\"/wiki/", $content_position)) {

            $content_position = strpos($this->html, "<a href=\"/wiki/", $content_position)+15;
            $current_link = "https://ru.wikipedia.org/wiki/";
            while (substr($this->html,$content_position,1) != "\""){
                $current_link = $current_link . substr($this->html, $content_position, 1);
                $content_position++;
            }

            if((strpos($this->html, "title=\"", $content_position)) < strpos($this->html, ">", $content_position)){

                if((strpos($this->html, "class=\"", $content_position)) < strpos($this->html, ">", $content_position)){
                    if ((substr($this->html, strpos($this->html, "class=\"", $content_position)+7, 8) == "internal")
                    or (substr($this->html, strpos($this->html, "class=\"", $content_position)+7, 5) == "image")){
                        continue;
                    }
                }

                $content_position = strpos($this->html, "title=\"", $content_position)+7;
                $current_link_title = "";
                //TODO
                while (substr($this->html,$content_position,1) != "\""){
                    $current_link_title = $current_link_title . substr($this->html, $content_position, 1);
                    $content_position++;
                }
            }
            else{
                continue;
            }

            $link = array("title"=>$current_link_title, "url"=>$current_link);
            if(!(in_array($link, $this->links))) {
                $this->links[$link_number] = $link;
                $link_number++;
            }
        }
    }

    public function print_links(){
        //TODO foreach ($this->links as $link_number => $link){
        for ($link_number = 1; $link_number<=count($this->links); $link_number++){
            print $link_number . ". " . $this->links[$link_number]['title'] . "\n";
        }
        print "\n";
        return count($this->links);
    }

    public function get_title(){
        return $this->title;
    }

    public function get_link_by_number($link_number){
        return $this->links[$link_number]['url'];
    }
}