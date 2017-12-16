<?php
require_once('bot_functions.php');

$post_json = file_get_contents("php://input");
$post_array = json_decode(strtolower($post_json), true);

user_ids($post_array);

$text_array = explode(" ", $post_array['text']);

if( trim($text_array[0]) === 'bot' ){
	bot_commands($post_array);
}

?>