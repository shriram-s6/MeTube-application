<?php
error_reporting(E_ERROR | E_PARSE);
require_once("config.php");
require_once("classes/Account.php");
require_once("classes/Constants.php");
require_once("classes/FormSanitizer.php");

$account = new Account($connect);

if(isset($_POST["signInSubmitButton"])) {

    $email = FormSanitizer::sanitizerFormEmail($_POST["email"]);
    $password = FormSanitizer::sanitizerFormPassword($_POST["password"]);

    $registerSuccessful = $account->login($email, $password);

    if($registerSuccessful) {
        $_SESSION["userLoggedIn"] = $email;
        header("Location: index.php");
    }
}
function getInputValue($name) {
    if(isset($_POST[$name])) {
        echo $_POST[$name];
    }
}
?>
<!DOCTYPE html>

<html lang="en-us">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>MeTube</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="css/signIn.css">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

</head>
<body>
    <div class="signInContainer">

        <div class="column">

            <div class="logoContainer">
                <img src="images/icons/MeTubeLogo.png" alt="MeTube Logo" title="MeTube Logo">
            </div>

            <div class="header">
                <header class="page-header">
                    <h2 class="text-white">Welcome to MeTube!</h2>
                </header>

            </div>

            <div class="signInFormContainer">

                <h4>Sign in to your account</h4>

            </div>

            <div class="loginForm">
                <form action="signIn.php" method="POST">
                    <?php echo $account-> getError(Constants::$loginFailed);?>
                    <label>
                        Email address
                        <input type="email" name="email" placeholder="Email address" value="<?php getInputValue('email');?>" required autocomplete="off" style="width: 200px">
                    </label>

                    <label>
                        Password

                        <input type="password" name="password" placeholder="Password" required autocomplete="off">
                    </label>
                    <input type="submit" name="signInSubmitButton" value="SIGN IN" style="max-width: 450px;align-self: center;margin-bottom: 15px;background-color: #a44cfb;color: #fafafa;">
                </form>
            </div>

            <div class="signUpContainer">
                <a class="signUp" href="signUp.php">Don't have an account yet? Sign Up here!</a>
            </div>

        </div>

    </div>
</body>
</html>