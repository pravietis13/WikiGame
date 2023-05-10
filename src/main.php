<?php
require_once 'Player.php';
require_once 'Page.php';
Player::set_route_pages();

while (true) {
    print "Количество игроков>>";
    $player_count = readline();
    if ((is_numeric($player_count)) and ($player_count>0)) {
        break;
    }
}

$players = [];
for ($i=1; $i<=$player_count; $i++){
    while (true) {
        print "Имя игрока " . $i . ">>";
        $player_name = readline();
        if($player_name != ""){
            break;
        }
    }
    $players[] = new Player($player_name);
}

$finished_players = 0;
while ($finished_players < $player_count){
    foreach ($players as $current_player){
        if ($current_player->is_still_playing()) {
            if (!($current_player->make_move())){
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


