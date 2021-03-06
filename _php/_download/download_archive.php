<?php
if(!defined('MAIN_DIR')){define('MAIN_DIR',dirname('__FILENAME__'));}
require_once(MAIN_DIR.'/../../_php/_config/session.php');
require_once(MAIN_DIR.'/../../_php/_config/connection.php');
require_once(MAIN_DIR.'/../../_php/_config/functions.php');
require_once(MAIN_DIR.'/../../_plugins/pclzip/pclzip.lib.php');
confirm_logged_in();
require_priv('priv_orders_download');

if (isset($_GET['order']) && isset($_GET['size'])) 
{

    $order = $_GET['order'];
    $size = $_GET['size'];

    // Initialize an array to hold image filenames 
    
    $imagesToArchive = array();
    $images = array();

    // Build query to find image ids associated with the current order number
    $sql = "SELECT * 
            FROM $DB_NAME.image 
            WHERE order_id = '{$order}' ";

    $result = db_query($mysqli, $sql);

    while ($row = $result->fetch_assoc())
    {

        $image = $row['legacy_id'].".jpg";
        $images[] =$image; 
    }
    if (preg_match('/http:/i', $image_dir))
    { 

        chdir(MAIN_DIR.'/../../temp');

         if ($size = 'medium')
            {
                foreach ($images as $image)
                {  
                    $url =IMAGE_DIR.$image;
                    $ch = curl_init();
                    $fh = fopen($image, 'wb');
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                    curl_setopt($ch, CURLOPT_FILE, $fh);
                    $result = curl_exec($ch);
                    fclose($fh);
                    curl_close($ch); 

                    $width = 1500;
                    $height = 1500;

                    list($width_orig, $height_orig) = getimagesize($image);

                    $ratio_orig = $width_orig/$height_orig;

                    if ($width/$height > $ratio_orig) 
                    {
                        $width = $height*$ratio_orig;
                    } 
                    else 
                    {
                        $height = $width/$ratio_orig;
                    }
    
                    $image_p = imagecreatetruecolor($width, $height);
                    $source = imagecreatefromjpeg($image);
                    
                    imagecopyresampled($image_p, $source, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);

                    @imagejpeg($image_p, $image);

                    $imagesToArchive[] = $image;         
                }
            }
    }


//Unfinished Below    

 //       else 
    //    {
//            if ($size = 'full')
     //       {  
      //          foreach ($images as $image)
        //       {
         //           $fileName = IMAGE_DIR.$image;
                  
          //          $imagesToArchive[] = $fileName; 
         //       }
       //     }

          //  elseif ($size = 'medium')
          //  {  
            //    foreach ($images as $image)
              //  {
         
                
                     
            //    }
          //  }
        //}




    // Temporarily increase PHP memory limit
    ini_set('memory_limit', '1024M');

    // Define filepath/name of archive file
    $file = $order.'.zip';

    // Create zip acrhive
    $archive = new PclZip($file);
    $v_list = $archive->create($imagesToArchive, PCLZIP_OPT_REMOVE_ALL_PATH);

    // Error handling (supplied by pclzip documentation)
    if (($v_result_list = $archive->extract()) == 0) 
    {
        die("Error : ".$archive->errorInfo(true));
    }

    // PHP troubleshooting error handling
    if (headers_sent()) 
    {
        echo 'HTTP header already sent';
    } 
    else 
    {
        if (!is_file($file)) 
        {
            // header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
            // echo 'File not found';
        } 
        elseif (!is_readable($file)) 
        {
            // header($_SERVER['SERVER_PROTOCOL'].' 403 Forbidden');
            // echo 'File not readable';
        } 
        else 
        {
            // Download the archive of images
            header($_SERVER['SERVER_PROTOCOL'].' 200 OK');
            header("Content-Type: application/zip");
            header("Content-Transfer-Encoding: Binary");
            header("Content-Length: ".filesize($file));
            header("Content-Disposition: attachment; filename=\"".basename($file)."\"");
            ob_clean();
            flush();
            readfile($file);
            ini_set('memory_limit', '128M');

            foreach ($imagesToArchive as $image)
            {    
                unlink($webroot.'/_php/_download/'.basename($image));
                unlink($image);
            }
        }
    }

   unlink($file);   
}
