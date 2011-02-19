<?php
    $page->TITLE = "503" ;


    $loginForm = '<form action="#" method="post">';
    $loginForm .= 'Login : <input type="text" value="" name="login" />';
    $loginForm .= 'Password : <input type="password" value="" name="password" />';
    $loginForm .= '<input type="submit" name="submit" value="Submit" />';
    $loginForm .= '</form>';

    $page->CONTENT = "Acces non autorisé.<br />" . $loginForm ;
?>
