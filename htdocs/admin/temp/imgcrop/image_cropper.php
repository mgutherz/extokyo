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

    /*
        The code in this file was stripped down from the original implementation that was put inside
        a class. The class code contained custom error messages and such that have been removed for
        the sake of simplicity. There may have been other lines of code removed in order to protect
        methods that are used on some development servers. If you have suggestions for this source
        code, feel free to mail them to justin.koivisto@gmail.com with the subject "PHP Image Cropping"
    */

    /**
    *   crop_image
    *
    *   Uses the GD library to crop an image.
    *
    *   @param  &$info  An array to store the resulting image data in for return.
    *                   Array is returned with the 'atts' (the HTML string for width and height),
    *                   'data' (binary data), and 'header' (what to use when sending to a browser),
    *                   'width' (result in px), 'height' (result in px).
    *   @param  $x1 The new left-most coordinate
    *   @param  $y1 The new top-most coordinate
    *   @param  $w The new width
    *   @param  $h The new height
    *   @param  $image  The system pat the the image to crop
    *   @param  $quality    The JPEG quality to save the image with
    *   @result A cropped image overwrites $image. Returns TRUE or FALSE & sets the &$info array
    */
    function crop_image(&$info,$x1,$y1,$w,$h,$image,$quality=95){

        $sys_path_name=preg_replace('`(.*)/[^/]*$`',"$1",$image);
        if(!is_writable($image) && !is_writable($sys_path_name)){
            $info['errors'][]=$image.' or '.$sys_path_name.' is not writable on the system.';
            return FALSE;
        }

        if(!file_exists($image)){
            $info['errors'][]=$image.' does not exist on the system.';
            return FALSE;
        }

        $info=getimagesize($image); $type=$info[2];
        switch($type){
            case 2: //JPG
                if(!function_exists('imagecreatefromjpeg')){
                    // image type not supported on this server's PHP/GD install
                    $info['errors'][]='Image type not supported on this server\'s PHP/GD install';
                    return FALSE;
                }else{
                    $rslt=_crop_jpeg_image($x1,$y1,$w,$h,$image,$quality);
                }
                break;
            case 3: //PNG
                if(!function_exists('imagecreatefrompng')){
                    // image type not supported on this server's PHP/GD install
                    $info['errors'][]='Image type not supported on this server\'s PHP/GD install';
                    return FALSE;
                }else{
                    $rslt=_crop_png_image($x1,$y1,$w,$h,$image);
                }
                break;
            default:
                $info['errors'][]='Image type not supported.';
                return FALSE; // this image type is not supported (yet?)
        }

        // the image has been created, let's get the rest of the information we need!
        $size=getimagesize($image);

        $info['header']=image_type_to_mime_type($size[2]);
        $info['width']=$size[0];
        $info['height']=$size[1];
        $info['atts']=$size[3];
        unset($size);

        $info['data']=file_get_contents($image);
        return TRUE;
    }

    /**
    *   _crop_jpeg_image
    *
    *   Uses the GD library to crop an image.
    *
    *   @param  $x1 The new left-most coordinate
    *   @param  $y1 The new top-most coordinate
    *   @param  $w The new width
    *   @param  $h The new height
    *   @param  $image  The system pat the the image to crop
    *   @param  $quality    The JPEG quality to save the image with
    *   @result Saves cropped image over the old one on success. Returns TRUE or FALSE.
    */
    function _crop_jpeg_image($x1,$y1,$w,$h,$image,$quality=95){
        $srcImage=imagecreatefromjpeg($image);
        if($srcImage==''){
            return FALSE;
        }

        $destImage=imagecreatetruecolor($w,$h);

        imagecopy($destImage,$srcImage,0,0,$x1,$y1,$w,$h);

        if(!imagejpeg($destImage,$image,$quality)){
            return FALSE;
        }

        imagedestroy($destImage);
        imagedestroy($srcImage);

        return TRUE;
    }

    /**
    *   _crop_png_image
    *
    *   Uses the GD library to crop an image.
    *
    *   @param  $x1 The new left-most coordinate
    *   @param  $y1 The new top-most coordinate
    *   @param  $w The new width
    *   @param  $h The new height
    *   @param  $image  The system pat the the image to crop
    *   @result Saves cropped image over the old one on success. Returns TRUE or FALSE.
    */
    function _crop_png_image($x1,$y1,$w,$h,$image){
        $srcImage=imagecreatefrompng($image);
        if($srcImage==''){
            return FALSE;
        }

        $destImage=imagecreatetruecolor($w,$h);

        if(!imagealphablending($destImage,FALSE)){
            return FALSE;
        }


        if(!imagesavealpha($destImage,TRUE)){
            return FALSE;
        }

        imagecopy($destImage,$srcImage,0,0,$x1,$y1,$w,$h);

        if(!imagepng($destImage,$image)){
            return FALSE;
        }

        imagedestroy($destImage);
        imagedestroy($srcImage);

        return TRUE;
    }
?>
