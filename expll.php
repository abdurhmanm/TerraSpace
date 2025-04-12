<?php
session_start();
session_unset();
session_destroy();
header("Location: index.html"); // توجيه المستخدم إلى صفحة تسجيل الدخول
exit();
?>