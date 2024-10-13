<?php
require_once __DIR__ . '/../src/php/functions.php';
require_once __DIR__ . '/../src/php/db.php';
?>

<!DOCTYPE html>
<html>
  <head>
    <title>Signup / Login</title>
    <link rel="stylesheet" type="text/css" href="css/index.css"/>
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@500&display=swap" rel="stylesheet"/>
  </head>
  <body>
    <div class="main">
      <input type="checkbox" id="chk" aria-hidden="true"/>

      <div class="signup">
        <form action="index.php" method="post">
          <label for="chk" aria-hidden="true">Sign up</label>
          <input type="txt" name="username" placeholder="Username" required="" maxlength="20" minlength="4"/>
          <input type="password" name="password" placeholder="Password" required="" maxlength="40" minlength="8"/>
          <button>
            <input type="submit" value="Sign up" style="display: none"/>Sign up
          </button>
          <?php
          if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['username']) && isset($_POST['password'])) {
              $login = $_POST['username'];
              $password = $_POST['password'];
              if (userCredentialsValid($login, $password)) {
                addUser($db, $login, $password);
                echo "<p class='success'>You have been successfully registered</p>\n";
                echo "<p class='success'>You can now login</p>\n";
              }
            }
          }
          ?>
        </form>
      </div>

      <div class="login">
        <form action="index.php" method="post">
          <label for="chk" aria-hidden="true">Login</label>
          <input type="txt" name="username" placeholder="Username" required="" maxlength="20" minlength="4"/>
          <input type="password" name="password" placeholder="Password" required="" maxlength="40" minlength="8"/>
          <button>
            <input type="submit" value="Login" style="display: none"/>Login
          </button>
          <?php
          if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['username']) && isset($_POST['password'])) {
              $login = $_POST['username'];
              $password = $_POST['password'];
              $stmt = $db->prepare('SELECT password FROM users WHERE username = :username');
              $stmt->bindValue(':username', $login);
              $stmt->execute();
              $hash = $stmt->fetchColumn();
              if (password_verify($password, $hash)) {
                $hmac_password = hash_hmac('sha256', $password, $passkey);
                setcookie('username', $login, time() + 60 * 60 * 24 * 30, '/', '', false, true);
                setcookie('password', $hmac_password, time() + 60 * 60 * 24 * 30, '/', '', false, true);
                header('Location: /planning.php');
              } else {
                echo "<p class='error'>Invalid username or password</p>\n";
              }
            }
          }
          ?>
        </form>
      </div>
    </div>
  </body>
</html>

