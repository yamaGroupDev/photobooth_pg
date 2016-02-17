<?php

ini_set('post_max_size', '1000M');
ini_set('upload_max_filesize', '1000M');
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: text/html; charset=utf-8');

require_once("database.php");

function base64_to_jpeg( $base64_string, $output_file ) 
{  
  $filename = dirname(__FILE__) . "/uploads/" . $output_file;  
  $ifp = fopen( $filename , "ab+" );   
  $decoded = base64_decode($base64_string);  
  $r = fwrite( $ifp,  $decoded) ;     
  fclose( $ifp ); 
}

if ($db)
{
  $db->query("INSERT INTO photos VALUES()");
  $id = mysqli_insert_id($db);
    
  $writeB64 = $_POST['writeImage'];
  $photoB64 = $_POST['photoImage'];

  base64_to_jpeg( $writeB64, $id.'write.jpeg' );
  base64_to_jpeg( $photoB64, $id.'photo.jpeg' );
  
  $path = dirname(__FILE__) . "/uploads/" .$id;
  
  $dst = imagecreatefromjpeg($path.'write.jpeg');
  $src = imagecreatefromjpeg($path.'photo.jpeg');
  $canvas = imagecreatefromjpeg('back-write-card.png');
  
  list($dst_width, $dst_height) = getimagesize($path.'write.jpeg');
  list($src_width, $src_height) = getimagesize($path.'photo.jpeg');  
  
  // Copiar y fusionar
  
  imagealphablending($canvas, false);
  imagesavealpha($canvas, true);
  
  imagecopymerge_alpha($canvas, $src, 1, 0, 0, 0, $src_width, $src_height, 100);
  imagecopymerge_alpha($canvas, $dst, $dst_width - 9, 40, 0, 0, $dst_width, $dst_height, 100);
  
  imagepng( $canvas, $path."final.png") ;
  
  imagedestroy($dst);
  imagedestroy($canvas);
  imagedestroy($src);
}

function imagecopymerge_alpha($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct){ 
        // creating a cut resource 
        $cut = imagecreatetruecolor($src_w, $src_h); 

        // copying relevant section from background to the cut resource 
        imagecopy($cut, $dst_im, 0, 0, $dst_x, $dst_y, $src_w, $src_h); 
        
        // copying relevant section from watermark to the cut resource 
        imagecopy($cut, $src_im, 0, 0, $src_x, $src_y, $src_w, $src_h); 
        
        // insert cut resource to destination image 
        imagecopymerge($dst_im, $cut, $dst_x, $dst_y, 0, 0, $src_w, $src_h, $pct); 
}



?>