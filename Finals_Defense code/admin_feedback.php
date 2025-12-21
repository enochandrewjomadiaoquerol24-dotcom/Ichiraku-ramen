<?php
require_once 'config.php';
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_account_id']) || ($_SESSION['role'] ?? '') !== 'admin') { 
    header('Location: customer_index.php'); 
    exit; 
}

// Handle Status Updates (Mark as Resolved)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ticket_id'])) {
    $tid = intval($_POST['ticket_id']);
    $conn->query("UPDATE support_tickets SET status = 'Resolved' WHERE ticket_id = $tid");
    header("Location: admin_feedback.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback & Support | Ichiraku Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Nanum+Brush+Script&family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script>
    tailwind.config = {
      theme: { extend: { colors: { brand: { red: '#ef2a39', darkRed:'#bd1e2a', pink:'#FFF0F3' } }, fontFamily: { brush:['"Nanum Brush Script"','cursive'], poppins:['Poppins','sans-serif'] } } }
    }
    </script>
    <style>body { font-family: 'Poppins', sans-serif; }</style>
</head>
<body class="bg-gray-50 flex flex-col min-h-screen">

<!-- HEADER -->
<header class="bg-white shadow-sm border-b border-gray-100 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
        <div class="flex items-center gap-4">
            <img src="https://github.com/arjiannahcarmelle/Ichiraku_Ramen/blob/main/Ichiraku_Ramen_Assets/Logo.png?raw=true" alt="Logo" class="w-12 h-12 object-contain hover:scale-110 transition-transform">
            <div class="flex flex-col">
                <span class="font-brush text-4xl text-black leading-none">Ichiraku Admin</span>
                <span class="text-xs text-brand-red font-bold tracking-widest uppercase ml-1">Feedback & Support</span>
            </div>
        </div>
        <div class="flex gap-4">
            <a href="admin_dashboard.php" class="hidden md:flex items-center gap-2 px-5 py-2.5 rounded-full bg-brand-pink text-brand-red font-bold hover:bg-red-100 transition-colors">
                <i class="fa-solid fa-chart-pie"></i> Dashboard
            </a>
            <a href="logout.php" class="flex items-center gap-2 px-5 py-2.5 rounded-full bg-brand-red text-white font-bold shadow-lg shadow-brand-red/30 hover:bg-brand-darkRed transition-all">
                <i class="fa-solid fa-right-from-bracket"></i> Logout
            </a>
        </div>
    </div>
</header>

<!-- CONTENT -->
<div class="max-w-6xl mx-auto p-6 mt-6 flex-1">
    
    <div class="flex justify-between items-end mb-8">
        <div>
            <h2 class="text-3xl font-bold text-gray-800">Customer Inbox</h2>
            <p class="text-gray-500 text-sm mt-1">Review feedback and support requests.</p>
        </div>
        <div class="bg-white px-4 py-2 rounded-lg border border-gray-200 shadow-sm text-sm text-gray-600">
            <i class="fa-solid fa-inbox text-brand-red mr-2"></i>Recent Messages
        </div>
    </div>

    <div class="space-y-4">
        <?php
        $sql = "SELECT s.*, c.customer_name, c.contact_number 
                FROM support_tickets s 
                JOIN customers_info c ON s.customer_id = c.customer_id 
                ORDER BY s.created_at DESC";
        $res = $conn->query($sql);

        if ($res && $res->num_rows > 0) {
            while($r = $res->fetch_assoc()) {
                $status = $r['status'];
                $isOpen = ($status === 'Open');
                
                // Visuals
                $borderClass = $isOpen ? 'border-orange-400' : 'border-green-400';
                $bgStatus = $isOpen ? 'bg-orange-100 text-orange-700' : 'bg-green-100 text-green-700';
                $icon = $isOpen ? 'fa-envelope-open-text' : 'fa-check-circle';

                echo '<div class="bg-white p-6 rounded-2xl shadow-sm border-l-8 '.$borderClass.' border-t border-r border-b border-gray-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 transition hover:shadow-md">';
                
                // Left: Info
                echo '<div class="flex-1">';
                    echo '<div class="flex items-center gap-3 mb-2">';
                        echo '<span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide '.$bgStatus.'"><i class="fa-solid '.$icon.' mr-1"></i> '.$status.'</span>';
                        echo '<span class="text-xs text-gray-400 font-bold"><i class="fa-regular fa-clock mr-1"></i> '.date('M d, Y h:i A', strtotime($r['created_at'])).'</span>';
                    echo '</div>';
                    
                    echo '<h3 class="text-lg font-bold text-gray-800 mb-1">'.htmlspecialchars($r['subject']).'</h3>';
                    echo '<p class="text-gray-600 text-sm leading-relaxed mb-3 bg-gray-50 p-3 rounded-lg border border-gray-100">'.nl2br(htmlspecialchars($r['message'])).'</p>';
                    
                    echo '<div class="flex items-center gap-4 text-xs font-bold text-gray-500">';
                        echo '<span><i class="fa-solid fa-user mr-1 text-gray-400"></i> '.htmlspecialchars($r['customer_name']).'</span>';
                        echo '<span><i class="fa-solid fa-phone mr-1 text-gray-400"></i> '.htmlspecialchars($r['contact_number']).'</span>';
                    echo '</div>';
                echo '</div>';

                // Right: Action
                if($isOpen) {
                    echo '<form method="POST">';
                    echo '<input type="hidden" name="ticket_id" value="'.$r['ticket_id'].'">';
                    echo '<button class="px-6 py-2 bg-green-500 text-white rounded-xl font-bold shadow-sm hover:bg-green-600 transition flex items-center gap-2 whitespace-nowrap">';
                    echo '<i class="fa-solid fa-check"></i> Mark Resolved';
                    echo '</button>';
                    echo '</form>';
                } else {
                    echo '<div class="text-green-500 opacity-50 text-4xl"><i class="fa-solid fa-circle-check"></i></div>';
                }

                echo '</div>';
            }
        } else {
            echo '<div class="p-16 text-center bg-white rounded-3xl border-2 border-dashed border-gray-200">';
            echo '<i class="fa-solid fa-comments text-6xl text-gray-200 mb-4"></i>';
            echo '<h3 class="text-xl font-bold text-gray-400">No feedback messages yet.</h3>';
            echo '</div>';
        }
        ?>
    </div>
</div>

<footer class="text-center py-8 text-gray-400 text-sm mt-auto">
    &copy; <?php echo date('Y'); ?> Ichiraku Ramen Admin
</footer>

</body>
</html>