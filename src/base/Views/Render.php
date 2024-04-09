<?php

namespace Base\Views;

class Render
{
    public static function views($view, $data = [])
    {
        $path = BP . '/src/app/' . $view . '.phtml';

        if (file_exists($path)) {
            extract($data);
            
            require $path;

        } else {
            throw new \Exception("Views not found.");
        }
    }
}
