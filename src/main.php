<?php
require_once 'Player.php';
require_once 'Page.php';
//TODO напрашивается класс навроде Game
Player::set_route_pages();

//TODO вынести логику из конструкторов
//TODO скрыть конструкторы за статическими фабричными методами

//TODO сделать класс - Presentator/View - печатающий всё в консоль (классы модели при этом только отдают данные)
//TODO смысл View класса - смена консоли на веб (например) производится ТОЛЬКО переписыванием этого класса и больше ничего

//TODO вынести все числа и строковые повторяющиеся значения в константы с говорящими именами

while (true) {
    print "Количество игроков>>";
    $player_count = readline();
    if ((is_numeric($player_count)) and ($player_count>0)) {
        break;
    }
}

$players = [];
for ($i=1; $i<=$player_count; $i++){
    //TODO мб имена тут вводить
    $players[] = new Player($i);
}

$finished_players = 0;
while ($finished_players < $player_count){
    foreach ($players as $current_player){
        if ($current_player->is_still_playing()) {
            if (!($current_player->make_move())){ //TODO странный нейминг
                $finished_players++;
            }
        }
    }
}

print "----- Результаты -----\n";
print "Игрок - Число переходов\n";
foreach ($players as $player){
    $player->print_result();
}


