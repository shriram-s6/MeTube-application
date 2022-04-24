<?php
error_reporting(E_ERROR | E_PARSE);
class Constants {
    public static $firstNameTooShort = "Your first name is too short, first name should be at least 2 characters";
    public static $firstNameTooLong = "Your first name is too long, first name should be maximum 25 characters";
    public static $LastNameTooShort = "Your last name is too short, first name should be at least 2 characters";
    public static $LastNameTooLong = "Your last name is too long, first name should be maximum 25 characters";
    public static $UserNameTooShort = "Your username is too short, it should be at least 5 characters";
    public static $UserNameTooLong = "Your username is too long, it should be maximum 25 characters";
    public static $UserNameExists = "This username already exists";
    public static $invalidEmail = "Please enter a valid email address";
    public static $emailExists = "This email already has an account";
    public static $passwordNotAlphaNumeric = "Password must be alpha-numeric";
    public static $passwordTooShort = "Your password should be at least 5 characters";
    public static $passwordTooLong = "Your password should be maximum 30 characters";
    public static $loginFailed = "Email or password was incorrect";
    public static $ProfilePictureWrongType = "Profile picture must be a jpg, jpeg, or png";
}