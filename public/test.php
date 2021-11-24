<?php


// Позднее статическое связывание, может быть использована для того чтобы
// получить ссылку на вызываемый класс в контексте статического наследования

class A
{
    public static function who()
    {
        echo __CLASS__;
    }

    public static function test()
    {
        static::who();
    }
}

class B extends A
{
    public static function who()
    {
        echo __CLASS__;
    }
}

B::test();
