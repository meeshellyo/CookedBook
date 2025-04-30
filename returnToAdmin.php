<?php
session_start();

if (isset($_SESSION['viewasuser']) && $_SESSION['viewasuser']) {
    $_SESSION['role'] = $_SESSION['original_role'] ?? 'admin'; 
    unset($_SESSION['user_id']);    
    unset($_SESSION['viewasuser']); 
    unset($_SESSION['original_role']); 
}

// back to admin landing page
header("Location: landingAdminPage.php");
exit();
?>
