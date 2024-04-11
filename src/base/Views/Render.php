<?php

namespace Base\Views;

use Base\Notification\MessageManager;
use Base\Session\Session;

class Render
{
    public static function views($view, $data = [])
    {
        $path = BP . '/src/app/' . $view . '.phtml';

        if (file_exists($path)) {
            $messageManager = new MessageManager();
            $data = [
                'data' => [
                    ...$data,
                    MessageManager::VIEWS_KEY => $messageManager,
                    Session::VIEWS_KEY => [
                        Session::USER_KEY => Session::get(Session::USER_KEY)
                    ]
                ]
            ];
            extract($data);

            require $path;
        } else {
            throw new \Exception("Views not found.");
        }
    }
}
