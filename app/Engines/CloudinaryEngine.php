<?php

namespace App\Engines;

class CloudinaryEngine
{
    public function getUrl(string $filename): string
    {
        // Your logic here
        return "https://res.cloudinary.com/dl4y5pap8/image/upload/" . $filename;
    }
}