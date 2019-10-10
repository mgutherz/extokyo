<?php
/*
KOIVI JavaScript Image Cropper Copyright (C) 2004 Justin Koivisto
Version 3.1
Last Modified: 5/11/2005

    This library is free software; you can redistribute it and/or modify it
    under the terms of the GNU Lesser General Public License as published by
    the Free Software Foundation; either version 2.1 of the License, or (at
    your option) any later version.

    This library is distributed in the hope that it will be useful, but
    WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
    or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public
    License for more details.

    You should have received a copy of the GNU Lesser General Public License
    along with this library; if not, write to the Free Software Foundation,
    Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA 

    Full license agreement notice can be found in the LICENSE file contained
    within this distribution package.

    Justin Koivisto
    justin.koivisto@gmail.com
    http://www.koivi.com
*/

    // get the configuration data
    $CFG=parse_ini_file(dirname(__FILE__).'/../config.ini.php',TRUE);
    $CFG['paths']['sys_path']=realpath($_SERVER['DOCUMENT_ROOT'].$CFG['paths']['site_path']);
    foreach($CFG as $k=>$v){
        if(is_array($v)){
            foreach($v as $k2=>$v2){
                $CFG[$k][$k2]=str_replace('sys_path',$CFG['paths']['sys_path'],$CFG[$k][$k2]);
                $CFG[$k][$k2]=str_replace('site_path',$CFG['paths']['site_path'],$CFG[$k][$k2]);
            }
        }else{
            $CFG[$k]=str_replace('sys_path',$CFG['paths']['sys_path'],$CFG[$k]);
            $CFG[$k]=str_replace('site_path',$CFG['paths']['site_path'],$CFG[$k]);
        }
    }
    echo '<?xml version="1.0" encoding="iso-8859-1"?>',"\n";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
  <title>Defining An Image Crop Area With Javascript</title>
  <meta name="keywords" content="javascript image crop selection code download cross-browser cross-platform" />
  <meta name="description" content="A demonstration how to use JavaScript to select and show the selection of a portion of an image visually." />
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
  <link rel="stylesheet" href="/styles.css" />
  <!--[if lt IE 7]><script src="/ie7/ie7-standard.js" type="text/javascript"></script><![endif]-->

  <style type="text/css">
    #imgJSselbox{
        position: absolute;
        visibility: hidden;
        width: 0px;
        height: 0px;
        border: 1px solid #006;
        color: #fff;
        background-image: url(selection_area.gif);
        z-index: 500; /* make sure it's on top of everything in this container */
    }
  </style>
  <script src="image_cropper.js" type="text/javascript"></script>
 </head>

 <body>
  <div id="container">
   <div id="intro">
    <h1>Defining An Image Crop Area With Javascript</h1>
    <p>
     Ever want to allow your web development clients to crop their own images as they upload them through your powerful CMS? If so, you've found
     a solution! No more explaining to them how to use an image manipulation program just to crop photos from their digital camera before they
     post them on their website! This Javascript code gives you a cross-browser, cross-platform simple way of defining a crop selection on an uploaded image.
    </p>
    
    <p><a href="/image-crop-selection.zip">Source Code Download</a></p>
    
    <p>
     The code included in this source download contains the JavaScript, CSS and PHP files used to create ths demo. The PHP functions for this task are
     nearly identical to my <a href="/php-gd-image-watermark/">Image Watermarking</a> functions, except instead
     of using <a href="http://www.php.net/imagecopyresampled">imagecopyresampled</a>, you'd use <a href="http://www.php.net/imagecopy">imagecopy</a>
     (and adjust a few function call parameters).
    </p>
   </div>

<?php
    if(isset($_GET['x']) && isset($_GET['width']) && isset($_GET['y']) && isset($_GET['height']) && isset($_GET['image'])){
        // time do get to the cropping - include the PHP functions
        require_once 'image_cropper.php';
        
        // remove any query string that was added to prevent browser caching
        $filename=substr(preg_replace('`\?.*`','',$_GET['image']),1);
        
        // make sure there are no "../" or "//" in the file name - 8/30/2005 (finally fixed!)
        $filename=str_replace('//','/',str_replace('../','',$filename));

        $imgFile=$CFG['paths']['sys_path'].'/'.$filename;
        $result=crop_image($imgData,$_GET['x'],$_GET['y'],$_GET['width'],$_GET['height'],$imgFile);
        $size=getimagesize($CFG['paths']['sys_path'].'/'.$filename);
        if(!$result){
            echo '   <div>',"\n",
                 '    <h1>Image Cropping Error</h1>',"\n";
            foreach($imgData['errors'] as $err)
                echo '<p class="errorMsg">',$err,'</p>',"\n";
            echo '   </div>',"\n";
        }else{
            echo '   <div>',"\n",
                 '    <h1>Image Cropping Successful</h1>',"\n",
                 '    <p>Below is the resulting image from your crop selection.</p>',"\n",
                 '    <p><img src="/',$filename,'?id=',md5(time()),'" alt="Your Uploaded Image" ',$size[3],' /></p>',"\n",
                 '   </div>',"\n";
        }
    }else if(isset($_FILES['image'])){
        // file upload form was posted, first set a filename
        $filename='images/'.date('YmdHi').preg_replace('`[^a-z0-9.]`i','',$_FILES['image']['name']);
        
        // now move the uploaded file to its new destination
        if($_FILES['image']['error']==0 && move_uploaded_file($_FILES['image']['tmp_name'],dirname(__FILE__).'/'.$filename)){
            // file was moved successfully, we'll want to show the cropping thing now along with the uploaded image
            $size=getimagesize(dirname(__FILE__).'/'.$filename);

            // be sure this is an IMAGE and not some kind of script or other bad thing!
            if(!is_array($size)){
                echo '   <div>',"\n",
                     '    <h1>ERROR Uploading File</h1>',"\n",
                     '    <p>',"\n",
                     '     You did not upload a valid image file. Please note the errors listed below and try again or report',"\n",
                     '     them to <a href="mailto:justin.koivisto@gmail.com?subject=Crop Page Error">Justin</a>.',"\n",
                     '    </p>',"\n",
                     '   </div>',"\n";
            }else{
                echo '   <div>',"\n",
                     '    <h1>STEP 2: Define Your Image Cropping Selection</h1>',"\n",
                     '    <p>',"\n",
                     '     To select your image crop area, click the opposite diagonal corners of the area you want to keep.',"\n",
                     '     A blue selection box should appear. If the selection isn\'t where you want it, click the image again to clear the selection and try again.',"\n",
                     '     When you have your selection in the correct position, click the "Crop Image" button and the image will then be cropped to your selection.',"\n",
                     '    </p>',"\n",
                     '    <p><img src="',$filename,'?id=',md5(time()),'" alt="" ',$size[3],' id="crpImg" onclick="getImageCropSelectionPoint(\'crpImg\',event);" /></p>',"\n",
                     '    <div id="imgJSselbox"></div>',"\n",
                     '    <div>',"\n",
                     '     <input type="button" onclick="setImageCropAreaSubmit(\'',$_SERVER['REQUEST_URI'],'\',\'crpImg\');" value="Crop Image" />',"\n",
                     '    </div>',"\n",
                     '   </div>',"\n";
            }
        }else{
            // give the user reasons for failure
            echo '   <div>',"\n",
                 '    <h1>ERROR Uploading Image</h1>',"\n",
                 '    <p>',"\n",
                 '     There was an error encountered while trying to upload your image file. Please note the errors listed below and try again or report',"\n",
                 '     them to <a href="mailto:justin.koivisto@gmail.com?subject=Crop Page Error">Justin</a> if asked to do so.',"\n",
                 '    </p>',"\n";

            $ERR=FALSE;
            
            // check if we can write to the target
            if(!is_writable(dirname(__FILE__).'/'.$filename) || !is_writable(dirname(__FILE__).'/images')){
                echo '    <p>Server configuration error... In other words, Justin forgot to set file permissions for the directory where images are stored. Please tell him about it.</p>',"\n";
                $ERR=TRUE;
            }
            
            // check if there was an upload error and report it
            switch($_FILES['image']['error']){
                case UPLOAD_ERR_INI_SIZE:
                    printf('<p>The uploaded file exceeds the maximum filesize for file uploads of %d MB.</p>'."\n",ini_get('upload_max_filesize')/1048576);
                    $ERR=TRUE;
                    break;
                case UPLOAD_ERR_FORM_SIZE:
                    printf('<p>The uploaded file exceeds the maximum filesize of %d MB for this demonstration.</p>'."\n",$_POST['MAX_FILE_SIZE']/1048576);
                    $ERR=TRUE;
                    break;
                case UPLOAD_ERR_PARTIAL:
                    echo '<p>The uploaded file was only partially uploaded.</p>',"\n";
                    $ERR=TRUE;
                    break;
                case UPLOAD_ERR_NO_FILE:
                    echo '<p>No file was uploaded.</p>',"\n";
                    $ERR=TRUE;
                    break;
            }
            
            if(!$ERR){
                echo '<p>I actually don\'t know what went wrong! Everything looks OK from this end. Try it again, if it still fails, then tell Justin.</p>';
            }
        
            echo '   </div>',"\n";
        }
    }else{
        // show the file upload form
?>
   <div>
    <h1>Step 1: Uploading The Image To Crop</h1>
    <p>
     First, upload a JPEG or PNG image file from your hard drive that you want to crop. This image will be used in the demonstration for defining the
     image crop selection area with the Javascript code.
    </p>
    <form method="post" action="<?php echo $_SERVER['REQUEST_URI'] ?>" enctype="multipart/form-data">
     <input type="file" name="image" /><br />
     <input type="submit" />
    </form>                 
    <p>
     Note that if you upload an image that is wider than this display area (between menus, borders, etc.) then the crop selection may not appear in the 
     correct position on the image. I have not ben able to find a way to detect position changes due to floated elements like the column to the right.',
    </p>
   </div>
   <div>
    <h1>Updates</h1>
    <p>
     As I use this script more and more, I may find bugs or decide to implement something in a different way. When such is the case,
     I will add a note to this page about the differences since last version (don't expect a detailed changelog though).
    </p>
    <ul>
     <li>
      <p>
       <b>5/11/2005:</b>
       Bugs dealing with multiple absolute-positioned elements containing the image have been fixed. <b>New feature added</b> you no longer have to define the crop area from top-left to bottom-right; you can define your bounding points in any order.
       Additional bugs dealing with the absolute-positioned exist when selection is not made from top-left to bottom-right. Those will be fixed in a future release.
      </p>
     </li>
     <li>
      <p>
       <b>3/8/2005:</b>
       Big changes here. Positioning of the selection area and calculation of the clicks in relation to the image is now calculated
       differently. It seems to be fairly accurate now, even when you have to scroll to make a click! In the process, the javascript
       function names were changed as well as the resulting url query string. The PHP function arguements were also changed to match with
       the new query string.
      </p>
     </li>
    </ul>
   </div>
<?php
    }
?>
<script type="text/javascript"><!--
google_ad_client = "pub-6879264339756189";
google_alternate_ad_url = "http://koivi.com/google_adsense_script.html";
google_ad_width = 728;
google_ad_height = 90;
google_ad_format = "728x90_as";
google_ad_type = "text";
google_ad_channel ="";
google_color_border = "6E6057";
google_color_bg = "DFE0D0";
google_color_link = "313040";
google_color_url = "0000CC";
google_color_text = "000000";
//--></script>
<script type="text/javascript"
  src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script>

  </div>

<?php include_once 'site_menu.php'; ?>

 </body>
</html>
