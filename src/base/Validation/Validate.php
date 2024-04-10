<?php
namespace Base\Validation;

use Illuminate\Validation\Factory;
use Illuminate\Translation\Translator;
use Illuminate\Translation\FileLoader;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Validation\ValidationException;

class Validate
{
    private static $instance;

    protected $translationLoader;
    protected $translator;
    protected $validationFactory;

    public function __construct()
    {
        $this->translationLoader = new FileLoader(new Filesystem(), 'path/to/language/files');
        $this->translator = new Translator($this->translationLoader, 'en');
        $this->validationFactory = new Factory($this->translator);
    }

    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new Validate();
        }

        return self::$instance;
    }

    public function getValidation() {
        return $this->validationFactory;
    }
}
