<?php
require_once("config.php");
if(isset($_POST["submitButton"])) {
    $firstName = $_POST["firstName"];
    echo $firstName;
}
?>

<!DOCTYPE html>

<html lang="en-us">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>MeTube</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="css/signUp.css">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

</head>
<body>
<div class="signUpContainer">

    <div class="column">

        <div class="logoContainer">
            <img src="images/icons/MeTubeLogo.png" alt="MeTube Logo" title="MeTube Logo">
        </div>

        <div class="header">
            <header class="page-header">
                <h2 class="text-white">Welcome to MeTube!</h2>
            </header>

        </div>

        <div class="signUpFormContainer">

            <h4>Create a new MeTube account</h4>

        </div>

        <div class="signUpForm">
            <form action="signUp.php" method="POST">
                <label>
                    First Name
                    <input type="text" name="firstName" placeholder="First Name" required autocomplete="off">
                </label>
                <label>
                    Last Name
                    <input type="text" name="lastName" placeholder="Last Name" required autocomplete="off">
                </label>
                <label>
                    User Name
                    <input type="text" name="userName" placeholder="User name" required autocomplete="off">
                </label>
                <label>
                    Email
                    <input type="email" name="email" placeholder="Enter your email" required autocomplete="off" style="width: 200px">
                </label>
                <label>
                    Password
                    <input type="password" name="password" placeholder="Enter your password" required autocomplete="off">
                </label>
                <p>By creating an account you agree to our <span><a href="">Terms & Privacy.</a></span></p>
                <input type="submit" name="submitButton" value="Create My Account" style="max-width: 450px;align-self: center;margin-bottom: 15px;background-color: #a44cfb;color: #fafafa">
            </form>
        </div>

        <div class="signInContainer">
            <a class="signInMessage" href="signIn.php">Already have an account? Sign In here!</a>
        </div>

    </div>

</div>
</body>
</html>