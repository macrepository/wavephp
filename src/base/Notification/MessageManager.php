<?php

namespace Base\Notification;

use Base\Session\Session;
use Base\Views\Render;

class MessageManager
{

    const STORAGE_KEY = 'message_manager';
    const SUCCESS = 'success';
    const ERROR = 'error';
    const WARNING = 'warning';
    const NOTIFICATION = 'notification';

    const VIEWS = 'Page/view/notification';

    protected $session;

    public function __construct()
    {
        $this->session = new Session();
    }

    public function setSuccessMessage(string $message): void
    {
        $messages = $this->session->get(self::STORAGE_KEY) ?? [];

        $successMessage = $messages[self::SUCCESS] ?? [];
        array_push($successMessage, $message);

        $messages[self::SUCCESS] = $successMessage;

        $this->session->set(self::STORAGE_KEY, $messages);
    }

    public function setErrorMessage(string $message): void
    {
        $messages = $this->session->get(self::STORAGE_KEY) ?? [];

        $successMessage = $messages[self::ERROR] ?? [];
        array_push($successMessage, $message);

        $messages[self::ERROR] = $successMessage;

        $this->session->set(self::STORAGE_KEY, $messages);
    }

    public function setWarningMessage(string $message): void
    {
        $messages = $this->session->get(self::STORAGE_KEY) ?? [];

        $successMessage = $messages[self::WARNING] ?? [];
        array_push($successMessage, $message);

        $messages[self::WARNING] = $successMessage;

        $this->session->set(self::STORAGE_KEY, $messages);
    }

    public function setNotificationMessage(string $message): void
    {
        $messages = $this->session->get(self::STORAGE_KEY) ?? [];

        $successMessage = $messages[self::NOTIFICATION] ?? [];
        array_push($successMessage, $message);

        $messages[self::NOTIFICATION] = $successMessage;

        $this->session->set(self::STORAGE_KEY, $messages);
    }

    public function getNotifications()
    {
        return $this->session->get(self::STORAGE_KEY) ?? [];
    }

    public function destroyStorageAfterDisplay()
    {
        if ($this->session->has(self::STORAGE_KEY)) {
            $this->session->forget(self::STORAGE_KEY);
        }
    }

    public function render()
    {
        return Render::views(self::VIEWS, ["notifications" => $this]);
    }
}
