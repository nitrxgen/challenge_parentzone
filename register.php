<?php

// gender presets, index 'other' enables use of an extra field
$select_gender = [
  'male'   => 'Male',
  'female' => 'Female',
  'other'  => 'Other...',
  'void'   => 'Prefer not to say'
];

// if a form submission is likely...
if(isset($_POST)) {
  // then collect the data
  $user_firstname  = isset($_POST['firstname'])?$_POST['firstname']:'';
  $user_lastname   = isset($_POST['lastname'])?$_POST['lastname']:'';
  $user_email      = isset($_POST['email'])?$_POST['email']:'';
  $user_password   = isset($_POST['password'])?$_POST['password']:'';
  $user_password2  = isset($_POST['password2'])?$_POST['password2']:'';
  $user_gender     = isset($_POST['gender'])?$_POST['gender']:'';
  $user_usergender = isset($_POST['usergender'])?$_POST['usergender']:'';
  $user_tac        = isset($_POST['tac'])?$_POST['tac']:'';

  // and validate it because JavaScript validation is easy to bypass
  $valid_firstname = preg_match('/[A-Za-z]{3}/', $user_firstname);
  $valid_lastname = preg_match('/[A-Za-z]{3}/', $user_lastname);
  $valid_email = preg_match('/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/', $user_email);
  $valid_password = preg_match('/[a-z]/', $user_password) && preg_match('/[A-Z]/', $user_password) && preg_match('/[0-9]/', $user_password);
  $valid_password2 = $user_password===$user_password2;
  $valid_tac = $user_tac==='on';

  // notes: it's bad practice to produce the form again with preset password
  // fields - so if the password fields do not match, we'll have to present the
  // form again with empty fields for the user to fill out again - if all fields
  // are valid and the passwords don't match, i've put in a rudamentary
  // implementation to show the password mismatch error using inline CSS, it's
  // quick and dirty but it works, this is only visible if the form is submitted
  // and having bypassed the JavaScript validation

  // deal with gender options, may be a bit over-engineered but let's go
  // if gender is not 'other' then set usergender to gender (usergender is used)
  if($user_gender==='other')
  $user_gender = $user_usergender;

  // check if all the inputs are valid, except gender
  if($valid_firstname &&
     $valid_lastname &&
     $valid_email &&
     $valid_password &&
     $valid_password2 &&
     $valid_tac) {

    // then we can create a database connection
    // note: all the die() calls can be made to look better, but for the sake of
    // a technical challenge, i'm keeping it simple, the task criteria is still met

    if(extension_loaded('mysqli')) {
      $mysqli = @new mysqli('', 'root', '9LKH+Nt+*R%7gpYB', 'parentzone', 3306);
      if($mysqli->connect_error)
        die('The MySQL object was unable to connect: '.$mysqli->connect_error);

      // do a quick check to see if the email address is already in use, make a
      // query to see if any records with the same email address exists
      $result = $mysqli->query('SELECT * FROM registrants WHERE email = \''.$mysqli->real_escape_string($user_email).'\';');

      // if no records found, we can make it!
      if($result->num_rows==0) {

        // let's make a password hash using bcrypt, it's a slow function hence
        // i will generate it only when it's passed all the checks and actually needed
        // it should be known by everyone at this point saving a user's plain text
        // password is a very, very bad idea should anything get compromised, though
        // for this challenge, nothing was mentioned about creating a login form
        // so these password hashes don't get used, they can be verified with password_verify
        $user_passwordhash = password_hash($user_password, PASSWORD_BCRYPT, ['cost'=>12]);

        // use a prepare and bind technique to save the hassle of escaping all the inputs
        $stmt = $mysqli->prepare('INSERT INTO registrants (firstname, lastname, email, password, gender, created, lastlogin) VALUES (?, ?, ?, ?, ?, ?, ?);');

        // not going to lie, binding with mysqli is new to me, it's simpler with sqlite
        $stmt->bind_param('sssssii', ...[$user_firstname, $user_lastname, $user_email, $user_passwordhash, $user_gender, time(), 0]);

        // execute, since this writes to a database let's be aware an error could occur
        if((@$stmt->execute())!==false) {
          echo "User successfully registered with the following data:<pre>\n\n";

          // just get the data that was inserted
          $result = $mysqli->query('SELECT * FROM registrants WHERE email = \''.$mysqli->real_escape_string($user_email).'\';');

          // using print_r(..., true) let's us escape any xss put into the form, this is
          // super lazy i know, nobody would show results like this, consider it debugging
          $rawoutput = print_r($result->fetch_assoc(), true);
          echo htmlentities($rawoutput);
          die('Consider viewing <a href="userlist.php">the user list</a>.');
        }
        else
          die('Something went wrong when executing database instructions.');
      }
      else
        die('This email address is already registered! Consider viewing <a href="userlist.php">the user list</a>.');
    }
    else
      die('The server is not correctly configured with MySQL.');

    die();
  }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Parent Zone</title>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="style.css"/>
  <script src="register.js"></script>
</head>
<body>

<form id="userreg" method="POST">
<div class="mainform">
  <h3>User Registration</h3>

  <div style="margin: 0px 24px;display:inline-flex;">
    <div style="margin:0px; width: 50%">
      <span>First name:</span>
      <input type="text" name="firstname" placeholder="First Name" value="<?php echo htmlspecialchars(@$user_firstname);?>"/>
      <span id="hint_firstname" class="hidehint">Please enter a valid first name!</span>
    </div>
    <div style="margin:0px; width: 50%; padding: 0px 0px 0px 1em;">
      <span>Last name:</span>
      <input type="text" name="lastname" placeholder="Last Name" value="<?php echo htmlspecialchars(@$user_lastname);?>"/>
      <span id="hint_lastname" class="hidehint">Please enter a valid last name!</span>
    </div>
  </div>

  <div>
    <span>Email:</span>
    <input type="text" name="email" placeholder="example@domain.com" value="<?php echo htmlspecialchars(@$user_email);?>"/>
    <span id="hint_email" class="hidehint">Please enter a valid email address!</span>
  </div>
  <div>
    <span>Password:</span>
    <input type="password" name="password" placeholder="Password"/>
    <span class="pw_rules">Must be <span id="pw_len">at least 8 bytes long</span>, and contain <span id="pw_uc">at least 1 uppercase</span>, <span id="pw_lc">at least 1 lowercase</span>, and <span id="pw_dig">at least 1 digit</span>.</span>
  </div>
  <div>
    <span>Retype password:</span>
    <input type="password" name="password2" placeholder="Password"/>
    <span id="hint_password2" class="hidehint"<?php echo !$valid_password2?' style="visibility:visible;"':'';?>>Your passwords do not match!</span>
  </div>

  <div>
  <span>Gender (optional):</span>
  <select name="gender">
    <option disabled<?php echo !isset($user_gender)?' SELECTED':'';?>>Please choose...</option>
<?php
foreach($select_gender as $key=>$value)
  printf("    <option value=\"%s\"%s>%s</option>\n", $key, @$user_gender===$key?' SELECTED':'', $value);
?>
  </select>
  </div>
  <div id="usergender"><span>Please specify gender (optional):</span><input type="text" name="usergender" value="<?php echo htmlspecialchars(@$user_usergender);?>"/></div>

  <div>
    <input type="checkbox" name="tac" id="tac"<?php echo @$valid_tac?' CHECKED':'';?>/>
    <label for="tac">Do you accept the terms and conditions?</label>
    <span id="hint_tac" class="hidehint">You must accept the terms and conditions!</span>
  </div>
  <div><input type="submit" value="Register"/></div>
</div>
</form>

</body>
</html>