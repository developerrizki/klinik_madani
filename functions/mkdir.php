<?php
function createDirectory($path,$include_filename=false){
  
    $dir = explode('/',$path);  // Array direktori
    $total = (int) count($dir);  // Total array
  
    if($include_filename == true){
        unset($dir[($total - 1)]);  // Unset array terakhir (filename)
    }
    
    $cur_dir = '';
       
    foreach($dir as $key){   // Membuat direktori
        if(!is_dir($cur_dir.$key)){
            mkdir($cur_dir.$key,'777');
        }
        $cur_dir .= $key.'/';
    }
}
?>