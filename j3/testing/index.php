<?php

define('_JEXEC', 1);
define('JPATH_BASE', realpath(dirname(__FILE__) . '/../'));

require_once(JPATH_BASE . '/includes/defines.php');
require_once(JPATH_BASE . '/includes/framework.php');

use Joomla3\Testing\TestingController;

$testing = TestingController::tesing();

require_once(JPATH_BASE . '/testing/testingcontroller.php');

$mainframe = JFactory::getApplication('site');
$mainframe->initialise();


if (empty($_SESSION['loggedin'])) {
    session_start();
}

// Create a database connection
$db = JFactory::getDbo();
// $session = JFactory::getSession();

// echo $session->get('loggedin');

?>


<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bootstrap demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
</head>

<body>
    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['login']) && $_POST['login'] === 'login') {


            // Get the username and password from the form
            $username = $_POST['username'];
            $password = $_POST['password'];

            // Create a new query object
            $query = $db->getQuery(true);

            $query->select('*')
                ->from($db->quoteName('#__users'))
                ->where($db->quoteName('username') . ' = ' . $db->quote($username));

            // Set the query for execution
            $db->setQuery($query);
            $result = $db->loadAssoc();

            if (empty($result)) {
                echo "No Record Found!";
                die;
            } else {
                if ($password === $result['password']) {
                    $_SESSION['loggedin'] = true;
                    $_SESSION['id'] = $result['id'];
                    $_SESSION['username'] = $result['username'];

                    $query = $db->getQuery(true);

                    $query->select('*')
                        ->from($db->quoteName('#__testing'))
                        ->where($db->quoteName('user_id') . ' = ' . $db->quote($result['id']));

                    $db->setQuery($query);
                    $result = $db->loadAssocList();

                    // print_r($result);
                    // die;
                    $_SESSION['data'] = !empty($result) ? $result : '';
                } else {
                    echo "Password Not Match!";
                    die;
                }
            }

            // Redirect back to index.php
            header("Location: index.php");
            exit;
        }

        if (isset($_POST['logout']) && $_POST['logout'] === 'logout') {
            // session_start();
            session_destroy();
            header("Location: index.php");
            exit;
        }
    }
    ?>




    <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) { ?>
        <div class="container">
            <h1><?= 'Welcome, ' . $_SESSION["username"] . '!' ?> </h1>
            <br><br>

            <?php


            $data = $_SESSION['data'];
            $pagination = new JPagination(count($data), 0, 10);

            $start = $_GET['start'] ?? 0;

            $dataSlice = array_slice($data, $start, 10);

            ?>

            <?php $data = $_SESSION['data']; ?>
            <?php if (!empty($data)) { ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Text</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($dataSlice as $index => $item) { ?>
                            <tr>
                                <th scope="row"><?= $index + 1 ?></th>
                                <td><?= $item['text'] ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php } ?>
            <div class="pagination">
                <ul>
                    <?php echo $pagination->getPagesLinks(); ?>
                </ul>
            </div>


            <form method="POST">
                <input class="btn btn-primary" type="submit" name="logout" value="logout">
            </form>
        </div>

        <br>

        <div class="container">
            <form action="upload_file.php" method="post" enctype="multipart/form-data">
                <input type="file" name="uploadFile">
                <input type="submit" value="Upload">
            </form>
        </div>

    <?php } ?>

    <?php if (empty($_SESSION['loggedin'])) { ?>
        <div class="container">
            <h1>Login Page</h1>
            <form method="POST">
                <input type="hidden" name="login" value="login">
                <label for="username">Username:</label>
                <input class="form-control" type="text" name="username" id="username" required><br>

                <label for="password">Password:</label>
                <input class="form-control" type="password" name="password" id="password" required><br>

                <input class="btn btn-primary" type="submit" value="Login">
            </form>
        </div>
    <?php } ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
</body>

</html>