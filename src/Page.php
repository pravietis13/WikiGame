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
        $article_start = strpos($this->html, "<div id=\"mw-content-text\"");
        $article_end = strpos($this->html,"<noscript>", $article_start);
        $this->html = substr($this->html, $article_start,$article_end-$article_start); //получили html, включающий только статью

        preg_match_all("<a href=\"/wiki/(.*?)\" title=\"(.*?)\">", $this->html,$temp, 0);

        foreach ($temp[0] as $num=>$item){
            if (!(str_contains($item, "class=\"internal\"") or str_contains($item, "class=\"image\""))){ //отсеиваем не статьи
                if (($link_end = strpos($temp[1][$num], "\"")) !== false){ //обрезаем остальные атрибуты кроме href
                    $temp[1][$num] = substr($temp[1][$num], 0, $link_end);
                }

                $link = array("title"=>$temp[2][$num], "url"=>"https://ru.wikipedia.org/wiki/" . $temp[1][$num]);
                if(!(in_array($link, $this->links))) {
                    $this->links[] = $link;
                }
            }
        }
    }

    public function print_links(){
        foreach ($this->links as $num=>$link){
            print $num+1 . ". " . $this->links[$num]['title'] . "\n";
        }
        print "\n";
    }

    public function get_links_count(){
        return count($this->links);
    }

    public function get_title(){
        return $this->title;
    }

    public function get_link_by_number($link_number){
        return $this->links[$link_number]['url'];
    }
}