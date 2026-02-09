<?php
require_once 'config.session.inc.php';
class view
{
    public function display_errors($key = 'errors')
    {
        if (!empty($_SESSION[$key])) {
            foreach ($_SESSION[$key] as $msg) {
                echo '<br>';
                echo "<p class='alert alert-danger'>{$msg}</p>";
            }
            unset($_SESSION[$key]);
        }
    }

}
?>