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
    <title>Admin Dashboard | Ichiraku</title>
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
                <span class="text-xs text-brand-red font-bold tracking-widest uppercase ml-1">Dashboard</span>
            </div>
        </div>
        <div>
            <a href="logout.php" class="flex items-center gap-2 px-6 py-2.5 rounded-full bg-brand-red text-white font-bold shadow-lg shadow-brand-red/30 hover:bg-brand-darkRed transition-all transform hover:-translate-y-0.5">
                <i class="fa-solid fa-right-from-bracket"></i> Logout
            </a>
        </div>
    </div>
</header>

<!-- MAIN CONTENT -->
<div class="max-w-7xl mx-auto p-6 mt-8 flex-1">
    <div class="mb-10 text-center md:text-left">
        <h1 class="text-3xl font-bold text-gray-800">Overview</h1>
        <p class="text-gray-500 mt-2">Select a module to manage your restaurant operations.</p>
    </div>

    <!-- Dashboard Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 xl:grid-cols-4 gap-6">
        
        <!-- Orders -->
        <a href="admin_orders.php" class="group bg-white rounded-3xl p-8 shadow-sm border border-gray-100 hover:shadow-xl hover:border-brand-pink transition-all duration-300 transform hover:-translate-y-2 relative overflow-hidden">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity"><i class="fa-solid fa-utensils text-8xl text-brand-red"></i></div>
            <div class="relative z-10">
                <div class="w-14 h-14 rounded-2xl bg-brand-pink text-brand-red flex items-center justify-center text-2xl mb-6 shadow-sm group-hover:scale-110 transition-transform"><i class="fa-solid fa-clipboard-list"></i></div>
                <h2 class="text-xl font-bold text-gray-800 mb-2 group-hover:text-brand-red transition-colors">Orders</h2>
                <p class="text-gray-500 text-xs mb-4 leading-relaxed">Manage incoming orders & deliveries.</p>
                <div class="flex items-center text-brand-red font-bold text-xs">Access Orders <i class="fa-solid fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i></div>
            </div>
        </a>

        <!-- Inventory -->
        <a href="admin_inventory.php" class="group bg-white rounded-3xl p-8 shadow-sm border border-gray-100 hover:shadow-xl hover:border-blue-100 transition-all duration-300 transform hover:-translate-y-2 relative overflow-hidden">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity"><i class="fa-solid fa-box-open text-8xl text-blue-500"></i></div>
            <div class="relative z-10">
                <div class="w-14 h-14 rounded-2xl bg-blue-50 text-blue-500 flex items-center justify-center text-2xl mb-6 shadow-sm group-hover:scale-110 transition-transform"><i class="fa-solid fa-boxes-stacked"></i></div>
                <h2 class="text-xl font-bold text-gray-800 mb-2 group-hover:text-blue-600 transition-colors">Inventory</h2>
                <p class="text-gray-500 text-xs mb-4 leading-relaxed">Monitor ingredient stock levels.</p>
                <div class="flex items-center text-blue-600 font-bold text-xs">Manage Stock <i class="fa-solid fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i></div>
            </div>
        </a>

        <!-- Sales Report -->
        <a href="admin_sales_report.php" class="group bg-white rounded-3xl p-8 shadow-sm border border-gray-100 hover:shadow-xl hover:border-green-100 transition-all duration-300 transform hover:-translate-y-2 relative overflow-hidden">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity"><i class="fa-solid fa-chart-pie text-8xl text-green-500"></i></div>
            <div class="relative z-10">
                <div class="w-14 h-14 rounded-2xl bg-green-50 text-green-500 flex items-center justify-center text-2xl mb-6 shadow-sm group-hover:scale-110 transition-transform"><i class="fa-solid fa-chart-line"></i></div>
                <h2 class="text-xl font-bold text-gray-800 mb-2 group-hover:text-green-600 transition-colors">Sales Report</h2>
                <p class="text-gray-500 text-xs mb-4 leading-relaxed">View daily revenue analytics.</p>
                <div class="flex items-center text-green-600 font-bold text-xs">View Analytics <i class="fa-solid fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i></div>
            </div>
        </a>

        <!-- Feedback & Support (NEW) -->
        <a href="admin_feedback.php" class="group bg-white rounded-3xl p-8 shadow-sm border border-gray-100 hover:shadow-xl hover:border-orange-100 transition-all duration-300 transform hover:-translate-y-2 relative overflow-hidden">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity"><i class="fa-solid fa-comments text-8xl text-orange-500"></i></div>
            <div class="relative z-10">
                <div class="w-14 h-14 rounded-2xl bg-orange-50 text-orange-500 flex items-center justify-center text-2xl mb-6 shadow-sm group-hover:scale-110 transition-transform"><i class="fa-solid fa-headset"></i></div>
                <h2 class="text-xl font-bold text-gray-800 mb-2 group-hover:text-orange-600 transition-colors">Feedback</h2>
                <p class="text-gray-500 text-xs mb-4 leading-relaxed">Read customer inquiries & issues.</p>
                <div class="flex items-center text-orange-600 font-bold text-xs">Read Messages <i class="fa-solid fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i></div>
            </div>
        </a>

    </div>
</div>

<footer class="text-center py-8 text-gray-400 text-sm">
    &copy; <?php echo date('Y'); ?> Ichiraku Ramen Admin Panel
</footer>

</body>
</html>