<?php
include("includes/init.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="stylesheet" type="text/css" href="public/styles/styles.css" />
  <title>Login</title>
</head>

<body>
  <?php include("includes/header.php"); ?>
  <div class="login-div">
    <?php
    if (!is_user_logged_in()) {
    ?>
      <h2>LOG IN</h2>

    <?php
      echo_login_form("/explore", $session_messages);
    } ?>

  </div>

  <?php include("includes/footer.php"); ?>
</body>

</html>
