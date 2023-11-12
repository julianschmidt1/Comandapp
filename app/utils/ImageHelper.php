<?php

class ImageHelper
{
    private $_extensiones = ['jpg', 'png', 'jpeg'];
    private $_sizeLimit = 6 * 1024 * 1024;

    public function saveImage($image, $name, $path)
    {
        $extension = pathinfo($image->getClientFilename(), PATHINFO_EXTENSION);
        $fullPath = $path . $name . '.' . $extension;
        $size = $image->getSize();
        $tmpName = $image->getStream()->getMetadata('uri');

        if (in_array(strtolower($extension), $this->_extensiones) && $size < $this->_sizeLimit) {
            if (move_uploaded_file($tmpName, $fullPath)) {
                return $fullPath;
            }
        }

        return false;
    }
}

?>