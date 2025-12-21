<?php
require_once 'config.php';
// Ensure session is started and user is admin
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_account_id']) || ($_SESSION['role'] ?? '') !== 'admin') { 
    header('Location: customer_index.php'); 
    exit; 
}

// --- Handle Status Updates & Notifications ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $act = $_POST['action'] ?? '';
    $oid = intval($_POST['order_id'] ?? 0);
    $cid = intval($_POST['customer_id'] ?? 0);
    $total = floatval($_POST['total'] ?? 0);
    
    $new_status = 0;
    $title = "";
    $msg = "";

    // WORKFLOW LOGIC
    if ($act === 'confirm') {
        $new_status = 2; // Preparing
        $title = "INVOICE: Order #$oid Confirmed";
        $msg = "Your order has been confirmed by the kitchen.\nAmount: ₱".number_format($total,2)."\nStatus: Preparing";
    } elseif ($act === 'deliver') {
        $new_status = 3; // Out for Delivery
        $title = "DISPATCH: Order #$oid On the Way";
        $msg = "Rider has picked up your order.\nDestination: Your Address\nEst. Time: 15-20 mins";
    } elseif ($act === 'complete') {
        $new_status = 4; // Delivered
        $title = "RECEIPT: Order #$oid Completed";
        $msg = "Transaction Complete.\nPaid: ₱".number_format($total,2)."\nThank you for dining with Ichiraku!";
    } elseif ($act === 'cancel') {
        $new_status = 5; // Cancelled
        $title = "VOID: Order #$oid Cancelled";
        $msg = "This order has been cancelled by the admin.\nNo charges applied.";
    }

    if ($new_status > 0) {
        // 1. Update DB Status
        $stmt = $conn->prepare("UPDATE delivery SET delivery_status_id = ? WHERE order_id = ?");
        $stmt->bind_param("ii", $new_status, $oid);
        $stmt->execute();
        $stmt->close();

        // 2. Send Notification (Invoice Style)
        if ($cid > 0) {
            $stmtN = $conn->prepare("INSERT INTO notifications (customer_id, order_id, title, message, is_read, created_at) VALUES (?, ?, ?, ?, 0, NOW())");
            $stmtN->bind_param("iiss", $cid, $oid, $title, $msg);
            $stmtN->execute();
            $stmtN->close();
        }
    }
    // Refresh page
    header('Location: admin_orders.php'); 
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Orders | Ichiraku</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nanum+Brush+Script&family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- Config matching Customer UI -->
    <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            brand: { 
                red: '#ef2a39', 
                darkRed:'#bd1e2a', 
                pink:'#FFF0F3' 
            }
          },
          fontFamily: { 
            brush:['"Nanum Brush Script"','cursive'], 
            poppins:['Poppins','sans-serif'] 
          }
        }
      }
    }
    </script>
    <style>body { font-family: 'Poppins', sans-serif; }</style>
</head>
<body class="bg-gray-50">

<!-- ================= BRANDED HEADER ================= -->
<header class="bg-white shadow-sm border-b border-gray-100 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
        <!-- Logo Area -->
        <div class="flex items-center gap-4">
            <img src="https://github.com/arjiannahcarmelle/Ichiraku_Ramen/blob/main/Ichiraku_Ramen_Assets/Logo.png?raw=true" alt="Logo" class="w-12 h-12 object-contain hover:scale-110 transition-transform">
            <div class="flex flex-col">
                <span class="font-brush text-4xl text-black leading-none">Ichiraku Admin</span>
                <span class="text-xs text-brand-red font-bold tracking-widest uppercase ml-1">Order Management</span>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center gap-4">
            <a href="admin_dashboard.php" class="hidden md:flex items-center gap-2 px-5 py-2.5 rounded-full bg-brand-pink text-brand-red font-bold hover:bg-red-100 transition-colors">
                <i class="fa-solid fa-chart-pie"></i> Dashboard
            </a>
            <a href="logout.php" class="flex items-center gap-2 px-5 py-2.5 rounded-full bg-brand-red text-white font-bold shadow-lg shadow-brand-red/30 hover:bg-brand-darkRed transition-all transform hover:-translate-y-0.5">
                <i class="fa-solid fa-right-from-bracket"></i> Logout
            </a>
        </div>
    </div>
</header>

<!-- ================= MAIN CONTENT ================= -->
<div class="max-w-6xl mx-auto p-6 mt-6">
    
    <!-- Page Title -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h2 class="text-3xl font-bold text-gray-800">Incoming Orders</h2>
            <p class="text-gray-500 text-sm mt-1">Manage delivery workflow and confirmations.</p>
        </div>
        <div class="bg-white px-4 py-2 rounded-lg border border-gray-200 shadow-sm text-sm font-medium text-gray-600">
            <i class="fa-solid fa-clock text-brand-red mr-2"></i>Live Updates
        </div>
    </div>

  <?php
    $sql = "SELECT o.order_id, o.total_amount, o.order_date, o.customer_id, 
                   IFNULL(d.delivery_status_id, 1) as status_id,
                   IFNULL(ds.status_name, 'Pending') as status_name,
                   c.customer_name
            FROM orders o 
            LEFT JOIN delivery d ON o.order_id = d.order_id 
            LEFT JOIN delivery_status ds ON d.delivery_status_id = ds.delivery_status_id
            LEFT JOIN customers_info c ON o.customer_id = c.customer_id
            ORDER BY o.order_id DESC";
    
    $res = $conn->query($sql);
    
    if ($res && $res->num_rows > 0) {
        while($r = $res->fetch_assoc()) {
            $sid = intval($r['status_id']);
            
            // Dynamic Styles based on Status
            $cardStyle = 'bg-white border-l-8 border-gray-200';
            $badgeStyle = 'bg-gray-100 text-gray-600';
            $icon = 'fa-circle-question';

            if($sid == 1) { // Pending
                $cardStyle = 'bg-white border-l-8 border-red-500'; 
                $badgeStyle = 'bg-red-50 text-red-600 border border-red-100';
                $icon = 'fa-hourglass-start text-red-500';
            } 
            elseif($sid == 2) { // Preparing
                $cardStyle = 'bg-white border-l-8 border-yellow-400'; 
                $badgeStyle = 'bg-yellow-50 text-yellow-700 border border-yellow-100';
                $icon = 'fa-fire-burner text-yellow-500';
            }
            elseif($sid == 3) { // Out
                $cardStyle = 'bg-white border-l-8 border-blue-500'; 
                $badgeStyle = 'bg-blue-50 text-blue-600 border border-blue-100';
                $icon = 'fa-motorcycle text-blue-500';
            }
            elseif($sid == 4) { // Delivered
                $cardStyle = 'bg-gray-50 border-l-8 border-green-500 opacity-80'; 
                $badgeStyle = 'bg-green-100 text-green-700 border border-green-200';
                $icon = 'fa-check-circle text-green-600';
            }
            elseif($sid == 5) { // Cancelled
                $cardStyle = 'bg-gray-50 border-l-8 border-gray-400 opacity-60'; 
                $badgeStyle = 'bg-gray-200 text-gray-600 border border-gray-300';
                $icon = 'fa-ban text-gray-400';
            }

            echo '<div class="'.$cardStyle.' p-6 rounded-xl shadow-sm mb-6 flex flex-col lg:flex-row justify-between items-center transition hover:shadow-md border-y border-r border-gray-100">';
            
            // Left: Order Info
            echo '<div class="w-full lg:w-1/2 mb-4 lg:mb-0">';
                echo '<div class="flex items-center gap-3 mb-2">';
                    echo '<span class="font-bold text-2xl text-gray-800">#'. str_pad($r['order_id'], 4, '0', STR_PAD_LEFT) .'</span>';
                    echo '<span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider '.$badgeStyle.'"><i class="fa-solid '.$icon.' mr-1"></i> '.$r['status_name'].'</span>';
                echo '</div>';
                echo '<div class="flex flex-col gap-1 pl-1">';
                    echo '<div class="text-sm text-gray-600 font-medium"><i class="fa-solid fa-user mr-2 text-gray-400 w-4"></i>'. ($r['customer_name'] ?: 'Guest Customer') .'</div>';
                    echo '<div class="text-sm text-gray-500"><i class="fa-regular fa-clock mr-2 text-gray-400 w-4"></i>'. date('M d, Y • h:i A', strtotime($r['order_date'])) .'</div>';
                echo '</div>';
            echo '</div>';
            
            // Right: Price & Actions
            echo '<div class="w-full lg:w-1/2 flex flex-col sm:flex-row items-center justify-end gap-6">';
                
                // Price
                echo '<div class="text-right">';
                    echo '<div class="text-xs text-gray-400 uppercase font-bold tracking-wide">Total Amount</div>';
                    echo '<div class="text-3xl font-bold text-brand-red">₱'. number_format($r['total_amount'], 2) .'</div>';
                echo '</div>';

                // Action Buttons
                echo '<div class="flex gap-2">';
                
                // 1. Pending -> Confirm OR Cancel
                if ($sid == 1) { 
                    echo renderBtn($r, 'confirm', 'Confirm', 'bg-blue-600 hover:bg-blue-700 text-white');
                    echo renderBtn($r, 'cancel', 'Cancel', 'bg-white border-2 border-red-100 text-red-500 hover:bg-red-50', true);
                } 
                // 2. Preparing -> Ship
                elseif ($sid == 2) { 
                    echo renderBtn($r, 'deliver', 'Ship Order', 'bg-yellow-400 hover:bg-yellow-500 text-black');
                }
                // 3. Out -> Complete
                elseif ($sid == 3) { 
                    echo renderBtn($r, 'complete', 'Mark Delivered', 'bg-green-600 hover:bg-green-700 text-white');
                }
                // 4. Delivered
                elseif ($sid == 4) {
                    echo '<div class="px-4 py-2 bg-green-100 text-green-700 rounded-lg font-bold text-sm"><i class="fa-solid fa-check mr-1"></i> Completed</div>';
                }
                // 5. Cancelled
                elseif ($sid == 5) {
                    echo '<div class="px-4 py-2 bg-gray-200 text-gray-500 rounded-lg font-bold text-sm"><i class="fa-solid fa-xmark mr-1"></i> Voided</div>';
                }

                echo '</div>'; // End Buttons
            echo '</div>'; // End Right Side

            echo '</div>'; // End Card
        }
    } else {
        echo '<div class="p-16 text-center bg-white rounded-2xl border-2 border-dashed border-gray-200">';
        echo '<i class="fa-solid fa-clipboard-list text-6xl text-gray-200 mb-4"></i>';
        echo '<h3 class="text-xl font-bold text-gray-400">No active orders found</h3>';
        echo '</div>';
    }

    // Button Helper
    function renderBtn($row, $act, $label, $classes, $confirm = false) {
        $confirmAttr = $confirm ? 'onsubmit="return confirm(\'Are you sure you want to cancel this order?\')"' : '';
        return '<form method="POST" '.$confirmAttr.' class="flex-1">
                    <input type="hidden" name="customer_id" value="'.$row['customer_id'].'">
                    <input type="hidden" name="order_id" value="'.$row['order_id'].'">
                    <input type="hidden" name="total" value="'.$row['total_amount'].'">
                    <input type="hidden" name="action" value="'.$act.'">
                    <button class="w-full sm:w-32 py-3 rounded-xl font-bold shadow-sm text-sm transition-all transform hover:scale-105 '.$classes.'">'.$label.'</button>
                </form>';
    }
  ?>
</div>
</body>
</html>