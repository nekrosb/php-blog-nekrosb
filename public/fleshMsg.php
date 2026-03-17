        if (isset($_SESSION["flash_error"])) {
        echo htmlspecialchars($_SESSION["flash_error"]);
        unset($_SESSION["flash_error"]);
        }