<?php

function directoryToArray($directory) {
  $array_items = array();
  if ($handle = opendir($directory)) {
    while (false !== ($file = readdir($handle))) {
      if ($file != "." && $file != "..") {
        if (is_dir($directory. "/" . $file)) {  } else { 
          $file = $directory . "/" . $file; 
          $array_items[] = preg_replace("/\/\//si", "/", $file); 
        }
      }
    }
    closedir($handle);
  }
  return $array_items;
}

$includes_directory = __DIR__.'/includes';
$includes_array = directoryToArray($includes_directory);
foreach($includes_array as $i=>$include_file){
  require_once($include_file);
}

function bot_commands($post){
  $return = false;
  $text_string = strtolower(clean($post['text']));
  $text_array = explode(" ", $text_string);
  $command = "bot_".$text_array[1];

  if( function_exists($command) ){
    $return = call_user_func($command, $post);
  }

  return $return;
}

function clean($in){
  return preg_replace("/[^A-Za-z0-9 ]/", '', $in);
}

function array_to_html_table($a, $level=0){ ?>
  <table width="100%" class="table_level_<?php echo $level; ?>">
    <tbody>
      <?php foreach($a as $f=>$v){ ?>
        <tr class="row_level_<?php echo $level; ?>">
          <td class="cell_level_<?php echo $level; ?>" nowrap width="25%"><?php echo $f; ?></td>
          <td class="cell_level_<?php echo $level; ?>" nowrap width="75%">
            <?php 
              if(is_array($v)){ 
                $level++;
                array_to_html_table($v, $level);
                $level--;
              }else{ 
                echo $v;
              } 
            ?>
          </td>
        </tr>
      <?php } ?>
    </tbody>
  </table>
<?php }

function user_ids($post){
  $save_users = false;
  $json_filepath = __DIR__."/logs/users.json";
  $users_json = file_get_contents($json_filepath);
  $users = json_decode($users_json, true);

  if( isset($users[ $post['user_id'] ]) ){
    $u = $users[ $post['user_id'] ];
    
    if( isset($u['groups']) ){
      if( !in_array($post['group_id'], $u['groups']) ){
        $u['groups'][] = $post['group_id'];
        $save_users = true;
      }
    }else{
      $u['groups'] = array( $post['group_id'] );
      $save_users = true;
    }

    if( isset($u['names']) ){
      if( !in_array($post['name'], $u['names']) ){
        $u['names'][] = $post['name'];
        $save_users = true;
      }
    }else{
      $u['names'] = array( $post['name'] );
      $save_users = true;
    }

    if( isset($u['sender_ids']) ){
      if( !in_array($post['sender_id'], $u['sender_ids']) ){
        $u['sender_ids'][] = $post['sender_id'];
        $save_users = true;
      }
    }else{
      $u['sender_ids'] = array( $post['sender_id'] );
      $save_users = true;
    }

  }else{
    $users[ $post['user_id'] ] = array(
      "groups" => array( $post['group_id'] ),
      "names" => array( $post['name'] ),
      "sender_ids" => array( $post['sender_id'] )
    );
    $save_users = true;
  }

  if( $save_users ){
    $f = fopen($json_filepath, 'w');
    fwrite($f, json_encode($users));
    fclose($f);
  }
}

function giphy_search($term){
  global $giphy_api_key;
  $giphy_endpoint = "https://api.giphy.com/v1/gifs/translate";  
  $giphy_query = $giphy_endpoint.'?s='.urlencode($term).'&api_key='.$giphy_api_key;
  $giphy = file_get_contents($giphy_query);
  $giphy = json_decode($giphy, true);
  $status = $giphy['meta']['status'];
  if( $status === 200 && isset($giphy['data']['images']['downsized_medium']['url']) ){
    return $giphy['data']['images']['downsized_medium']['url'];
  }else{
    return false;
  }
}

function GM_post($gm_bot_id, $image_url){
  $gm_endpoint = "https://api.groupme.com/v3/bots/post";
  $post_arr = array("bot_id"=>$gm_bot_id, "text"=>$image_url);
  $post_json = json_encode($post_arr);
  $c = curl_init();   
  curl_setopt($c, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
  curl_setopt($c, CURLOPT_URL, $gm_endpoint);
  curl_setopt($c, CURLOPT_POST, true);
  curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($c, CURLOPT_FOLLOWLOCATION, false);
  curl_setopt($c, CURLOPT_POSTFIELDS, $post_json);
  $info = curl_getinfo($c);
  $response = curl_exec($c);
  curl_close($c);
  return $response;
}

?>