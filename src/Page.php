<?php
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
            if(($this->html = @file_get_contents($url)) !== false){
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

    private function parse_links(){
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