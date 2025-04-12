<?php
session_start();
header('Content-Type: application/json');

try {
    $conn = new mysqli("localhost", "root", "", "tira_space");
    
    if ($conn->connect_error) {
        throw new Exception("فشل الاتصال بقاعدة البيانات");
    }

    if (isset($_SESSION["expert_id"]) && isset($_SESSION["session_id"])) {
        $user_id = $_SESSION["expert_id"];
        $session_id = $_SESSION["session_id"];
        
        // تحديث سجل الجلسة
        $sql = "UPDATE user_sessions 
                SET logout_time = NOW(), 
                    status = 'logged_out' 
                WHERE id = ? AND user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $session_id, $user_id);
        
        if (!$stmt->execute()) {
            throw new Exception("فشل تحديث سجل الجلسة");
        }
        
        // تحديث حالة المستخدم
        $sql = "UPDATE users SET is_online = 0, last_logout = NOW() WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        
        if (!$stmt->execute()) {
            throw new Exception("فشل تحديث حالة المستخدم");
        }
    }

    // تدمير الجلسة
    session_unset();
    session_destroy();
    
    $conn->close();
    
    echo json_encode([
        'success' => true,
        'message' => 'تم تسجيل الخروج بنجاح'
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
