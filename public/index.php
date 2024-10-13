<?php
require_once __DIR__ . '/../src/php/functions.php';
require_once __DIR__ . '/../src/php/db.php';

session_start();
if (isset($_SESSION['id'])) {
  header('Location: planning.php');
  exit;
}
?>

<!DOCTYPE html>
<html>

<head>
  <title>Signup / Login</title>
  <link rel="stylesheet" type="text/css" href="css/index.css" />
  <link href="https://fonts.googleapis.com/css2?family=Jost:wght@500&display=swap" rel="stylesheet" />
</head>

<body>
  <div class="main">
    <input type="checkbox" id="chk" aria-hidden="true" />
    <div class="signup">
      <form action="index.php" method="post">
        <label for="chk" aria-hidden="true">Sign up</label>
        <input type="text" name="username" placeholder="Username" required="" maxlength="20" minlength="4" />
        <input type="password" name="password" placeholder="Password" required="" maxlength="40" minlength="8" />
        <button type="submit" name="signup">Sign up</button>
      </form>

      <?php
      if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signup'])) {
        if (!isset($_POST['username']) || !isset($_POST['password'])) {
          echo "<p class='error'>Please fill in all fields</p>\n";
          return;
        }

        $login = $_POST['username'];
        $password = $_POST['password'];

        if (!userCredentialsValid($login, $password)) {
          echo "<p class='error'>Invalid credentials</p>\n";
          return;
        }

        if (userExists($db, $login)) {
          echo "<p class='error'>User already exists</p>\n";
          return;
        }

        addUser($db, $login, $password);
        echo "<p class='success'>You have been successfully registered</p>\n";
        echo "<p class='success' style='margin-top: 5px'>You can now login</p>\n";
      }
      ?>
    </div>

    <div class="login">
      <form action="index.php" method="post">
        <label for="chk" aria-hidden="true">Login</label>
        <input type="text" name="username" placeholder="Username" required="" maxlength="20" minlength="4" />
        <input type="password" name="password" placeholder="Password" required="" maxlength="40" minlength="8" />
        <button type="submit" name="login">Login</button>
      </form>

      <?php
      if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
        if (!isset($_POST['username']) || !isset($_POST['password'])) {
          echo "<p class='error'>Please fill in all fields</p>\n";
          return;
        }

        $login = $_POST['username'];
        $password = $_POST['password'];
        $stmt = $db->prepare('SELECT id, password FROM users WHERE username = :username');
        $stmt->bindValue(':username', $login);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $id = $row['id'];
        $hash = $row['password'];;

        if (!$hash || !password_verify($password, $hash)) {
          echo "<p class='error'>Invalid username or password</p>\n";
          return;
        }

        $_SESSION['id'] = $id;

        header('Location: planning.php');
        exit();
      }
      ?>
      </form>
    </div>
  </div>
</body>

</html>