<?php

function bot_help($post){
  $excl = array('bot_commands','robot_id');
  $func_all = get_defined_functions();
  $func = $func_all['user'];
  $text = "";
  foreach($func as $i=>$f){
    if( strpos($f, "bot_") !== false && !in_array($f, $excl) ){
      $text .= str_replace("bot_", "", $f);
      $text .= "\r\n";
    }
  }
  if( $text !== "" ){
    return GM_post( robot_id($post['group_id']), $text);
  }else{
    return false;
  }
}

function bot_wt($post){
  $text = "Current WT: Dave Barnes - 44.7 - 2017 Week 13";
  return GM_post( robot_id($post['group_id']), $text);
}

function bot_champ($post){
  $text = "Current Champion: Thomas Cox";
  return GM_post( robot_id($post['group_id']), $text);
}

function bot_sacko($post){
  $text = "Current Sacko Bitch: Kyle Kraft (2x)";
  return GM_post( robot_id($post['group_id']), $text);
}

function bot_giphy($post){
  $text = strtolower(clean($post['text']));
  $search_term = trim( ltrim($text, "bot giphy "));
  $search = giphy_search($search_term);
  if( $search !== false && $search !== NULL ){
    return GM_post( robot_id($post['group_id']), $search);
  }else{
    return false;
  }
}

function bot_tribute($post){
  $text = "David Barnes is from Lawrence, Kansas";
  return GM_post( robot_id($post['group_id']), $text);  
}

function bot_lmgtfy($post){
  $text = strtolower(clean($post['text']));
  $text = trim( ltrim($text, "bot lmgtfy "));
  $lmgtfy = "http://lmgtfy.com/?q=".urlencode($text);
  return GM_post( robot_id($post['group_id']), $lmgtfy);
}

function bot_greatercox($post){
  $text = "Tom Cox is the Greater Cox";
  return GM_post( robot_id($post['group_id']), $text);  
}

?>