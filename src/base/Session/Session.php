<?php
namespace Base\Session;

class Session {
    const USER_KEY = 'user';

    const VIEWS_KEY = 'session';

    public static function start() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function set($key, $value) {
        $_SESSION[$key] = $value;
    }

    public static function get($key) {
        if (isset($_SESSION[$key])) {
            return $_SESSION[$key];
        }

        return null;
    }

    public static function has($key) {
        return isset($_SESSION[$key]);
    }

    public static function forget($key) {
        unset($_SESSION[$key]);
    }

    public static function destroy() {
        session_unset();
        session_destroy();
    }
}
