<?php
require_once 'config.php';
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_account_id']) || ($_SESSION['role'] ?? '') !== 'admin') { 
    header('Location: customer_index.php'); 
    exit; 
}

// Handle Stock Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $qty = intval($_POST['qty']);
    $conn->query("UPDATE ingredients SET stock_qty = $qty WHERE ingredient_id = $id");
    // Redirect to prevent form resubmission
    header("Location: admin_inventory.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory | Ichiraku Admin</title>
    
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
                <span class="text-xs text-brand-red font-bold tracking-widest uppercase ml-1">Inventory Management</span>
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
    
    <div class="flex justify-between items-end mb-8">
        <div>
            <h2 class="text-3xl font-bold text-gray-800">Ingredient Stock</h2>
            <p class="text-gray-500 text-sm mt-1">Monitor real-time levels. Stock updates immediately.</p>
        </div>
        <div class="bg-white px-4 py-2 rounded-lg border border-gray-200 shadow-sm text-sm text-gray-600">
            <i class="fa-solid fa-triangle-exclamation text-red-500 mr-2"></i>Rows in <span class="text-red-600 font-bold">Red</span> are below minimum level.
        </div>
    </div>

    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-brand-red text-white text-sm uppercase tracking-wider">
                    <th class="p-5 font-semibold">Ingredient Name</th>
                    <th class="p-5 text-center font-semibold">Current Stock</th>
                    <th class="p-5 text-center font-semibold">Min Level</th>
                    <th class="p-5 text-center font-semibold">Quick Update</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php
                $res = $conn->query("SELECT * FROM ingredients ORDER BY ingredient_name ASC");
                if ($res->num_rows > 0) {
                    while($r = $res->fetch_assoc()) {
                        // Alert Logic
                        $isLow = ($r['stock_qty'] <= $r['min_qty']);
                        $rowClass = $isLow ? 'bg-red-50 hover:bg-red-100' : 'hover:bg-gray-50';
                        $textClass = $isLow ? 'text-red-700 font-bold' : 'text-gray-700';
                        $icon = $isLow ? '<i class="fa-solid fa-circle-exclamation text-red-500 mr-2 animate-pulse"></i>' : '<i class="fa-solid fa-box text-gray-300 mr-2"></i>';

                        echo '<tr class="transition-colors '.$rowClass.'">';
                        
                        // Name
                        echo '<td class="p-5 '.$textClass.'">'.$icon . htmlspecialchars($r['ingredient_name']).'</td>';
                        
                        // Stock
                        echo '<td class="p-5 text-center">';
                        echo '<span class="inline-block px-3 py-1 rounded-full text-sm font-bold '. ($isLow ? 'bg-red-200 text-red-800' : 'bg-green-100 text-green-700') .'">'.intval($r['stock_qty']).'</span>';
                        echo '</td>';
                        
                        // Min
                        echo '<td class="p-5 text-center text-gray-400 font-mono text-sm">'.intval($r['min_qty']).'</td>';
                        
                        // Action
                        echo '<td class="p-5 text-center">';
                        echo '<form method="POST" class="flex justify-center items-center gap-2">';
                        echo '<input type="hidden" name="id" value="'.$r['ingredient_id'].'">';
                        echo '<input name="qty" type="number" value="'.$r['stock_qty'].'" class="w-20 bg-white border border-gray-300 rounded-lg px-3 py-2 text-center focus:ring-2 focus:ring-brand-red focus:outline-none transition-shadow shadow-sm">';
                        echo '<button class="bg-brand-red hover:bg-brand-darkRed text-white px-4 py-2 rounded-lg shadow-md transition-transform hover:scale-105"><i class="fa-solid fa-rotate"></i></button>';
                        echo '</form>';
                        echo '</td></tr>';
                    }
                } else {
                    echo '<tr><td colspan="4" class="p-8 text-center text-gray-400">No ingredients found in database.</td></tr>';
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