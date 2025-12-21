<?php
require_once 'config.php';
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_account_id']) || ($_SESSION['role'] ?? '') !== 'admin') { 
    header('Location: customer_index.php'); 
    exit; 
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Report | Ichiraku Admin</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nanum+Brush+Script&family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

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
<body class="bg-gray-50 flex flex-col min-h-screen">

<!-- ================= HEADER ================= -->
<header class="bg-white shadow-sm border-b border-gray-100 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
        <div class="flex items-center gap-4">
            <img src="https://github.com/arjiannahcarmelle/Ichiraku_Ramen/blob/main/Ichiraku_Ramen_Assets/Logo.png?raw=true" alt="Logo" class="w-12 h-12 object-contain hover:scale-110 transition-transform">
            <div class="flex flex-col">
                <span class="font-brush text-4xl text-black leading-none">Ichiraku Admin</span>
                <span class="text-xs text-brand-red font-bold tracking-widest uppercase ml-1">Financial Reports</span>
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

<!-- ================= CONTENT ================= -->
<div class="max-w-6xl mx-auto p-6 mt-6 flex-1">
    
    <div class="mb-8">
        <h2 class="text-3xl font-bold text-gray-800">Sales Overview</h2>
        <p class="text-gray-500 text-sm mt-1">Revenue tracking for delivered and shipped orders.</p>
    </div>

    <?php
    // Logic: Only sum orders where delivery_status is 3 (Out) or 4 (Delivered)
    $sql = "SELECT DATE(o.order_date) as d, COUNT(*) as orders, SUM(o.total_amount) as total 
            FROM orders o 
            JOIN delivery d ON o.order_id = d.order_id 
            WHERE d.delivery_status_id IN (3, 4) 
            GROUP BY DATE(o.order_date) 
            ORDER BY DATE(o.order_date) DESC LIMIT 30";
            
    $res = $conn->query($sql);
    
    // Store data to use for Summary Cards + Table
    $rows = [];
    $grandTotal = 0;
    $totalOrders = 0;
    
    if ($res) {
        while($r = $res->fetch_assoc()){
            $rows[] = $r;
            $grandTotal += $r['total'];
            $totalOrders += $r['orders'];
        }
    }
    ?>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex items-center gap-6">
            <div class="w-16 h-16 rounded-2xl bg-green-100 text-green-600 flex items-center justify-center text-3xl">
                <i class="fa-solid fa-sack-dollar"></i>
            </div>
            <div>
                <p class="text-gray-500 text-sm font-bold uppercase tracking-wide">Total Revenue (Last 30 Days)</p>
                <h3 class="text-4xl font-bold text-gray-800 mt-1">₱ <?php echo number_format($grandTotal, 2); ?></h3>
            </div>
        </div>

        <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex items-center gap-6">
            <div class="w-16 h-16 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center text-3xl">
                <i class="fa-solid fa-receipt"></i>
            </div>
            <div>
                <p class="text-gray-500 text-sm font-bold uppercase tracking-wide">Successful Orders</p>
                <h3 class="text-4xl font-bold text-gray-800 mt-1"><?php echo number_format($totalOrders); ?></h3>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-brand-red text-white text-sm uppercase tracking-wider">
                    <th class="p-5 font-semibold"><i class="fa-regular fa-calendar mr-2"></i> Date</th>
                    <th class="p-5 text-center font-semibold">Orders Completed</th>
                    <th class="p-5 text-right font-semibold">Daily Revenue</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php
                if (!empty($rows)) {
                    foreach($rows as $r){
                        echo '<tr class="hover:bg-gray-50 transition-colors">';
                        echo '<td class="p-5 font-medium text-gray-700">'.date('F d, Y', strtotime($r['d'])).'</td>';
                        echo '<td class="p-5 text-center"><span class="bg-blue-50 text-blue-700 px-3 py-1 rounded-full font-bold text-sm">'.intval($r['orders']).'</span></td>';
                        echo '<td class="p-5 text-right font-bold text-gray-800">₱ '.number_format($r['total'],2).'</td>';
                        echo '</tr>';
                    }
                } else {
                    echo '<tr><td colspan="3" class="p-12 text-center text-gray-400 flex flex-col items-center justify-center">
                        <i class="fa-solid fa-chart-area text-4xl mb-4 text-gray-200"></i>
                        No confirmed sales data found yet.
                    </td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<footer class="text-center py-8 text-gray-400 text-sm mt-auto">
    &copy; <?php echo date('Y'); ?> Ichiraku Ramen Admin
</footer>

</body>
</html>