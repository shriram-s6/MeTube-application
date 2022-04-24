<?php
error_reporting(E_ERROR | E_PARSE);
class FormSanitizer {

    public static function sanitizerFormString($inputText) {
        $inputText = strip_tags($inputText);
        $inputText = trim($inputText);
        $inputText = strtolower($inputText);
        return ucfirst($inputText);
    }

    public static function sanitizerFormUsername($inputText) {
        $inputText = strip_tags($inputText);
        return trim($inputText);
    }

    public static function sanitizerFormPassword($inputText) {
        return strip_tags($inputText);
    }

    public static function sanitizerFormEmail($inputText) {
        $inputText = strip_tags($inputText);
        return trim($inputText);
    }

}