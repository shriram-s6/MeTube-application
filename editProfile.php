<?php
require_once('config.php');
require_once('classes/Account.php');
require_once('classes/Constants.php');
require_once('classes/FormSanitizer.php');

$account = new Account($connect);

if(isset($_POST['submitButton'])) {
    $newFirstName = FormSanitizer::sanitizerFormString($_POST['firstName']);
    $newLastName = FormSanitizer::sanitizerFormString($_POST['lastName']);

    $newUserName = FormSanitizer::sanitizerFormUsername($_POST['userName']);

    $newEmail = FormSanitizer::sanitizerFormEmail($_POST['email']);
    $newPassword = FormSanitizer::sanitizerFormPassword($_POST['password']);

    $updateSuccessful = $account->update($_SESSION["userLoggedIn"], $newFirstName, $newLastName, $newUserName, $newEmail, $newPassword);

    $_SESSION["userLoggedIn"] = $newEmail;

    if($updateSuccessful) {
        header('Location: profile.php?email='.$newEmail);
    }
}

function getInputValue($name) {
    if(isset($_POST[$name])) {
        echo $_POST[$name];
    }
}
?>
<!DOCTYPE html>

<html lang='en-us'>
<head>
    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1'>

    <title>MeTube</title>
    <link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css' integrity='sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm' crossorigin='anonymous'>
    <link rel='stylesheet' type='text/css' href='css/signUp.css'>

    <script src='https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js' integrity='sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q' crossorigin='anonymous'></script>
    <script src='https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js' integrity='sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl' crossorigin='anonymous'></script>

</head>
<body>
<div class='signUpContainer'>

    <div class='column'>

        <div class='logoContainer'>
            <img src='images/icons/MeTubeLogo.png' alt='MeTube Logo' title='MeTube Logo'>
        </div>

        <div class='header'>
            <header class='page-header'>
                <h2 class='text-white'>Edit Profile</h2>
            </header>

        </div>

        <div class='signUpFormContainer'>

            <h4>Update your information:</h4>

        </div>

        <div class='signUpForm'>
            <form action='editProfile.php' method='POST'>
                <?php echo $account-> getError(Constants::$firstNameTooShort);?>
                <?php echo $account-> getError(Constants::$firstNameTooLong);?>
                <label>
                    First Name
                    <input type='text' name='firstName' placeholder='First Name' value='<?php getInputValue('firstName');?>' required autocomplete='off'>
                </label>
                <?php echo $account-> getError(Constants::$LastNameTooShort);?>
                <?php echo $account-> getError(Constants::$LastNameTooLong);?>
                <label>
                    Last Name
                    <input type='text' name='lastName' placeholder='Last Name' value='<?php getInputValue('lastName');?>' required autocomplete='off'>
                </label>
                <?php echo $account-> getError(Constants::$UserNameTooShort);?>
                <?php echo $account-> getError(Constants::$UserNameTooLong);?>
                <?php echo $account-> getError(Constants::$UserNameExists);?>
                <label>
                    User Name
                    <input type='text' name='userName' placeholder='User name' value='<?php getInputValue('userName');?>' required autocomplete='off'>
                </label>
                <?php echo $account-> getError(Constants::$invalidEmail);?>
                <?php echo $account-> getError(Constants::$emailExists);?>
                <label>
                    Email
                    <input type='email' name='email' placeholder='Enter your email' value='<?php getInputValue('email');?>' required autocomplete='off' style='width: 200px'>
                </label>
                <?php echo $account-> getError(Constants::$passwordNotAlphaNumeric);?>
                <?php echo $account-> getError(Constants::$passwordTooShort);?>
                <?php echo $account-> getError(Constants::$passwordTooLong);?>
                <label>
                    Password
                    <input type='password' name='password' placeholder='Enter your password' required autocomplete='off'>
                </label>
                <input type='submit' name='submitButton' value='Update My Account' style='max-width: 450px;align-self: center;margin-bottom: 15px;background-color: #a44cfb;color: #fafafa'>
            </form>
        </div>

    </div>

</div>
</body>
</html>
