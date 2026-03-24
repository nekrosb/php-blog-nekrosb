<?php
if (isset($_SESSION["flash_msg"])) {
        echo htmlspecialchars($_SESSION["flash_msg"]);
        unset($_SESSION["flash_msg"]);
}
