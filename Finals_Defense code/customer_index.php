<?php
// customer_index.php - Final: Added Privacy & Support Features
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Ichiraku Ramen</title>

  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Nanum+Brush+Script&family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            brand: { 
                red: '#ef2a39', 
                darkRed:'#bd1e2a', 
                pink:'#FFF0F3', 
                lightPink:'#FFCDD2' 
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

  <style>
    body { font-family: 'Poppins', sans-serif; background:#f9fafb; color: #1f2937; }
    
    /* Animations */
    .animate-fade-in { animation: fadeIn 0.3s ease-out forwards; }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .animate-slide-in { animation: slideIn 0.3s ease-out forwards; }
    @keyframes slideIn {
        from { transform: translateX(-100%); }
        to { transform: translateX(0); }
    }

    /* Star Rating */
    .star-icon { cursor: pointer; transition: color 0.2s, transform 0.1s; font-size: 2rem; color: #D1D5DB; }
    .star-icon:hover { transform: scale(1.1); }
    .star-icon.active { color: #FBBF24; }

    /* Custom Scrollbar - Hidden but scrollable */
    ::-webkit-scrollbar { display: none; }
    * { -ms-overflow-style: none; scrollbar-width: none; }
  </style>
</head>
<body class="bg-gray-50">

  <!-- ================= HEADER ================= -->
  <header class="flex justify-between items-center px-4 md:px-6 py-3 bg-white shadow-sm fixed w-full top-0 z-40 border-b border-gray-100">
    <div class="flex items-center gap-4">
      <img src="https://github.com/arjiannahcarmelle/Ichiraku_Ramen/blob/main/Ichiraku_Ramen_Assets/Logo.png?raw=true" alt="Logo" class="w-16 h-16 object-contain">
      <div class="flex flex-col">
        <span class="font-brush text-3xl md:text-4xl text-black leading-none">Ichiraku Ramen</span>
        <span class="text-xs text-gray-500 hidden sm:block tracking-wide">Authentic Japanese Flavor</span>
      </div>
    </div>

    <div onclick="app.openDashboard('menu')" class="flex items-center gap-3 text-lg text-brand-red font-semibold cursor-pointer px-4 py-2 rounded-full border border-transparent hover:bg-brand-pink hover:border-brand-pink transition-all duration-300 relative">
      <div id="header-user-icon-container" class="w-10 h-10 flex items-center justify-center">
          <i id="header-user-icon" class="fa-solid fa-circle-user text-3xl"></i>
      </div>
      <span id="header-user-name" class="hidden sm:inline">Account</span>
      
      <!-- Global Notification Dot -->
      <div id="header-notif-dot" class="hidden absolute top-2 right-2 w-3 h-3 bg-red-600 rounded-full border-2 border-white animate-pulse"></div>
    </div>
  </header>

  <!-- ================= MAIN CONTENT ================= -->
  <main class="pt-28 pb-20 px-4 max-w-6xl mx-auto min-h-screen">
    
    <!-- Search & Filter Section -->
    <div class="text-center mb-10">
      <div class="relative max-w-xl mx-auto mb-6">
        <i class="fa-solid fa-magnifying-glass absolute left-5 top-1/2 -translate-y-1/2 text-brand-red text-lg"></i>
        <input id="search-input" type="text" placeholder="Search for ramen, drinks, sides..." 
               onkeyup="app.renderDishes()" 
               class="w-full pl-14 pr-6 py-4 rounded-full border-none shadow-lg focus:ring-4 focus:ring-brand-pink outline-none text-lg">
      </div>
      
      <p class="text-gray-500 mb-6 font-medium">Choose a category</p>
      
      <div class="flex flex-wrap justify-center gap-4 text-sm md:text-base">
        <button onclick="app.setCategory('All')" class="category-btn active px-8 py-3 rounded-full bg-brand-red text-white shadow-lg shadow-brand-red/30 font-semibold transition-transform hover:-translate-y-1">All</button>
        <button onclick="app.setCategory('Food')" class="category-btn px-8 py-3 rounded-full bg-white text-gray-700 shadow-sm border border-gray-100 font-semibold transition-transform hover:-translate-y-1 hover:shadow-md">Food</button>
        <button onclick="app.setCategory('Drinks')" class="category-btn px-8 py-3 rounded-full bg-white text-gray-700 shadow-sm border border-gray-100 font-semibold transition-transform hover:-translate-y-1 hover:shadow-md">Drinks</button>
      </div>
    </div>

    <!-- Dish Grid -->
    <div class="mb-10">
      <h2 class="text-2xl md:text-3xl font-bold text-gray-800 mb-8 border-l-4 border-brand-red pl-4">Menu Recommendations</h2>
      <div id="dish-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 md:gap-8">
        <!-- JS will populate this -->
      </div>
    </div>
  </main>

  <!-- ================= DASHBOARD SIDEBAR OVERLAY ================= -->
  <div id="dashboard-overlay" class="fixed inset-0 z-50 flex hidden">
    <!-- Backdrop -->
    <div onclick="app.closeDashboard()" class="absolute inset-0 bg-black/40 backdrop-blur-sm transition-opacity"></div>

    <!-- Sidebar -->
    <aside class="w-80 md:w-96 bg-brand-pink h-full flex flex-col border-r border-gray-100 shadow-2xl shrink-0 relative z-10 animate-slide-in">
      
      <!-- User Profile Header -->
      <div class="p-6 bg-brand-pink/30 border-b border-red-100 flex items-center gap-4">
        <!-- Avatar -->
        <div id="dash-user-avatar-container" class="w-14 h-14 bg-white rounded-full flex-shrink-0 flex items-center justify-center text-brand-red shadow-sm border-2 border-white">
            <i class="fa-solid fa-utensils text-2xl"></i>
        </div>
        
        <!-- User Info Flex Column -->
        <div class="flex-1 min-w-0">
          <div class="flex items-center gap-2">
              <h2 id="dash-username" class="font-bold text-gray-900 text-lg truncate">Guest</h2>
              <!-- GREEN CUSTOMER BADGE (Correctly placed next to name) -->
              <span id="dash-badge" class="hidden bg-green-100 text-green-700 text-[10px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wider border border-green-200">Customer</span>
          </div>
          <!-- Status / Emoji / Login Link -->
          <p class="text-xs text-gray-500 font-medium truncate mt-0.5">
              🍜 <span id="dash-status-text" class="cursor-pointer hover:text-brand-red hover:underline" onclick="app.openAuth(true)">Login to order</span>
          </p>
        </div>
      </div>

      <!-- Navigation -->
      <nav class="flex-1 px-6 py-6 overflow-y-auto">
        <ul class="space-y-3">
          <!-- ADMIN LINK (Hidden by default, shown via JS) -->
          <li id="nav-admin" class="hidden dash-nav-item flex items-center gap-4 px-5 py-4 rounded-2xl cursor-pointer transition-all bg-gray-800 text-white shadow-lg">
            <i class="fa-solid fa-lock w-6 text-center"></i> <span class="font-bold">Go to Admin Dashboard</span>
          </li>

          <li onclick="app.switchTab('menu')" id="nav-menu" class="dash-nav-item flex items-center gap-4 px-5 py-4 rounded-2xl cursor-pointer transition-all bg-brand-red text-white shadow-lg shadow-brand-red/20">
            <i class="fa-solid fa-book-open w-6 text-center"></i> <span class="font-bold">Browse Menu</span>
          </li>
          <li onclick="app.switchTab('cart')" id="nav-cart" class="dash-nav-item flex items-center gap-4 px-5 py-4 rounded-2xl cursor-pointer transition-all text-gray-600 hover:bg-white hover:text-brand-red">
            <i class="fa-solid fa-cart-shopping w-6 text-center"></i> <span class="font-bold">My Cart</span>
            <span id="nav-cart-count" class="ml-auto bg-brand-red text-white text-xs font-bold px-2 py-0.5 rounded-full hidden">0</span>
          </li>
          <li onclick="app.switchTab('orders')" id="nav-orders" class="dash-nav-item flex items-center gap-4 px-5 py-4 rounded-2xl cursor-pointer transition-all text-gray-600 hover:bg-white hover:text-brand-red">
            <i class="fa-solid fa-bag-shopping w-6 text-center"></i> <span class="font-bold">My Orders</span>
          </li>
          
          <!-- Notification Tab -->
          <li onclick="app.switchTab('notifications')" id="nav-notifications" class="dash-nav-item flex items-center gap-4 px-5 py-4 rounded-2xl cursor-pointer transition-all text-gray-600 hover:bg-white hover:text-brand-red relative">
            <i class="fa-solid fa-bell w-6 text-center"></i> <span class="font-bold">Notifications</span>
            <span id="nav-notif-count" class="hidden absolute top-4 right-4 bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">0</span>
          </li>

          <li onclick="app.switchTab('settings')" id="nav-settings" class="dash-nav-item flex items-center gap-4 px-5 py-4 rounded-2xl cursor-pointer transition-all text-gray-600 hover:bg-white hover:text-brand-red">
            <i class="fa-solid fa-gear w-6 text-center"></i> <span class="font-bold">Settings</span>
          </li>
        </ul>
      </nav>

      <!-- Logout / Auth Footer -->
      <div class="p-6 border-t border-brand-red/10 bg-white/50" id="sidebar-footer">
        <div class="flex gap-4 justify-center text-sm font-bold text-gray-500">
          <span class="cursor-pointer hover:text-brand-red transition-colors" onclick="app.openAuth(true)">Login</span>
          <span>|</span>
          <span class="cursor-pointer hover:text-brand-red transition-colors" onclick="app.openAuth(false)">Sign Up</span>
        </div>
      </div>
    </aside>

    <!-- Right Content Area -->
    <main class="flex-1 bg-gray-50 h-full overflow-y-auto relative p-4 md:p-10 w-full">
      <button onclick="app.closeDashboard()" class="absolute top-6 right-6 w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-md hover:bg-brand-red hover:text-white transition-colors z-20">
          <i class="fa-solid fa-xmark text-lg"></i>
      </button>

      <!-- TAB: MENU -->
      <div id="dash-content-menu" class="dash-content animate-fade-in">
        <h2 class="text-3xl font-bold mb-2 text-gray-800">Our Menu</h2>
        <p class="text-gray-500 mb-6">Select items to add to your cart.</p>
        <div id="dash-menu-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 pb-20"></div>
      </div>

      <!-- TAB: CART -->
      <div id="dash-content-cart" class="dash-content hidden pb-20 animate-fade-in">
        <h2 class="text-3xl font-bold mb-6 text-gray-800">Your Cart</h2>
        <div id="cart-items-container" class="space-y-4"></div>
        <div id="cart-summary-container" class="mt-8"></div>
        <!-- Note: Checkout button inside cart-summary-container is generated by JS -->
      </div>

      <!-- TAB: ORDERS -->
      <div id="dash-content-orders" class="dash-content hidden pb-20 animate-fade-in">
        <h2 class="text-3xl font-bold mb-6 text-gray-800">Order History</h2>
        <div id="orders-login-msg" class="hidden bg-white p-10 rounded-3xl border border-gray-100 text-center shadow-sm">
            <i class="fa-solid fa-lock text-4xl text-gray-300 mb-4"></i>
            <p class="text-gray-500">Please log in to track your orders.</p>
            <button onclick="app.openAuth(true)" class="mt-4 text-brand-red font-bold hover:underline">Log In Now</button>
        </div>
        <div id="orders-container" class="space-y-6"></div>
      </div>
      
      <!-- TAB: NOTIFICATIONS -->
      <div id="dash-content-notifications" class="dash-content hidden pb-20 animate-fade-in">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-3xl font-bold text-gray-800">Notification Center</h2>
            <button onclick="app.fetchNotifications()" class="text-xs text-brand-red font-bold hover:underline">Refresh</button>
        </div>
        <div id="notif-list" class="space-y-4"></div>
      </div>

      <!-- TAB: SETTINGS -->
      <div id="dash-content-settings" class="dash-content hidden animate-fade-in">
        <h2 class="text-3xl font-bold mb-6 text-gray-800">Settings</h2>

        <!-- Main Settings View -->
        <div id="settings-main-view" class="bg-white rounded-3xl shadow-sm border border-gray-100 divide-y divide-gray-100 overflow-hidden">
          
          <!-- Notifications Toggle -->
          <div class="p-6 flex justify-between items-center hover:bg-gray-50 transition-colors">
            <div class="flex gap-4 items-center">
                <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center"><i class="fa-solid fa-bell"></i></div>
                <div>
                    <div class="font-bold text-gray-800">Notifications</div>
                    <div class="text-xs text-gray-500">Receive alerts on order status</div>
                </div>
            </div>
            <div onclick="app.toggleNotifications()" class="w-12 h-7 bg-gray-300 rounded-full relative cursor-pointer transition-colors duration-300" id="notif-bg">
                <div id="notif-dot" class="w-5 h-5 bg-white rounded-full absolute top-1 left-1 shadow-sm transition-transform duration-300"></div>
            </div>
          </div>

          <!-- Address -->
          <div class="p-6">
            <div class="flex justify-between items-start mb-3">
               <div class="flex gap-4 items-center">
                    <div class="w-10 h-10 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center"><i class="fa-solid fa-location-dot"></i></div>
                    <div>
                        <div class="font-bold text-gray-800">Shipping Address</div>
                        <div class="text-xs text-gray-500">Default for delivery</div>
                    </div>
                </div>
              <button onclick="app.openSettingsAddress()" class="text-sm font-bold text-brand-red hover:underline bg-red-50 px-3 py-1 rounded-full">Edit</button>
            </div>
            <div id="settings-address-display" class="ml-14 text-sm text-gray-600 bg-gray-50 p-4 rounded-xl border border-gray-100 italic">
                No address set.
            </div>
          </div>

          <!-- Privacy Policy Link -->
          <div class="p-6 flex justify-between items-center cursor-pointer hover:bg-gray-50 transition-colors" onclick="app.openPrivacy()">
             <div class="flex gap-4 items-center">
                <div class="w-10 h-10 rounded-full bg-gray-100 text-gray-600 flex items-center justify-center"><i class="fa-solid fa-shield-halved"></i></div>
                <span class="font-bold text-gray-700">Privacy Policy</span>
             </div>
             <i class="fa-solid fa-chevron-right text-gray-300"></i>
          </div>
          
          <!-- Help & Support Link -->
          <div class="p-6 flex justify-between items-center cursor-pointer hover:bg-gray-50 transition-colors" onclick="app.openSupport()">
             <div class="flex gap-4 items-center">
                <div class="w-10 h-10 rounded-full bg-gray-100 text-gray-600 flex items-center justify-center"><i class="fa-solid fa-circle-question"></i></div>
                <span class="font-bold text-gray-700">Help & Support</span>
             </div>
             <i class="fa-solid fa-chevron-right text-gray-300"></i>
          </div>

        </div>

        <!-- Address Editor (Sub-view) -->
        <div id="settings-address-editor" class="hidden animate-fade-in">
            <button onclick="app.closeSettingsAddress()" class="mb-6 text-brand-red font-bold flex items-center gap-2 hover:bg-white px-3 py-2 rounded-lg w-fit"><i class="fa-solid fa-arrow-left"></i> Back to Settings</button>
            <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
                <h3 class="text-xl font-bold mb-6 text-gray-800 border-b pb-4">Edit Shipping Address</h3>
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-600 mb-2">Location Label</label>
                        <input id="set-addr-label" class="w-full bg-gray-50 border border-gray-200 rounded-xl p-4 focus:ring-2 focus:ring-brand-red focus:bg-white outline-none transition-all" placeholder="e.g. Home, Office, Dorm">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-600 mb-2">Full Address Details</label>
                        <textarea id="set-addr-details" class="w-full bg-gray-50 border border-gray-200 rounded-xl p-4 h-32 resize-none focus:ring-2 focus:ring-brand-red focus:bg-white outline-none transition-all" placeholder="House Number, Street Name, Barangay, City..."></textarea>
                    </div>
                    <button onclick="app.saveSettingsAddress()" class="w-full bg-brand-red text-white py-4 rounded-xl font-bold shadow-lg shadow-brand-red/30 hover:bg-brand-darkRed transition-all">Save Changes</button>
                </div>
            </div>
        </div>

      </div>
    </main>
  </div>

  <!-- ================= MODALS ================= -->

  <!-- Privacy Policy Modal -->
  <div id="privacy-modal" class="fixed inset-0 z-[80] flex items-center justify-center bg-black/60 p-4 hidden backdrop-blur-sm">
    <div class="bg-white w-full max-w-lg rounded-3xl p-8 relative shadow-2xl animate-fade-in max-h-[80vh] flex flex-col">
        <button onclick="document.getElementById('privacy-modal').classList.add('hidden')" class="absolute top-4 right-4 text-gray-400 hover:text-brand-red"><i class="fa-solid fa-xmark text-2xl"></i></button>
        <h2 class="text-2xl font-bold mb-4">Privacy Policy</h2>
        <div class="overflow-y-auto text-sm text-gray-600 space-y-3 flex-1 pr-2">
            <p><strong>1. Data Collection:</strong> We collect your name, contact number, and address to facilitate delivery.</p>
            <p><strong>2. Usage:</strong> Your data is used solely for order processing and improving our service.</p>
            <p><strong>3. Security:</strong> We implement standard security measures to protect your information.</p>
            <p><strong>4. Contact:</strong> For privacy concerns, contact admin@ichiraku.com.</p>
        </div>
        <button onclick="document.getElementById('privacy-modal').classList.add('hidden')" class="w-full mt-6 bg-brand-red text-white py-3 rounded-xl font-bold">Close</button>
    </div>
  </div>

  <!-- Help & Support Modal -->
  <div id="support-modal" class="fixed inset-0 z-[80] flex items-center justify-center bg-black/60 p-4 hidden backdrop-blur-sm">
    <div class="bg-white w-full max-w-lg rounded-3xl p-8 relative shadow-2xl animate-fade-in">
        <button onclick="document.getElementById('support-modal').classList.add('hidden')" class="absolute top-4 right-4 text-gray-400 hover:text-brand-red"><i class="fa-solid fa-xmark text-2xl"></i></button>
        <h2 class="text-2xl font-bold mb-2">Help & Support</h2>
        <p class="text-gray-500 text-sm mb-6">How can we help you today?</p>
        
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Topic</label>
                <select id="support-subject" class="w-full bg-gray-50 border p-3 rounded-xl">
                    <option>General Inquiry</option>
                    <option>Order Issue</option>
                    <option>Feedback</option>
                    <option>Other</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Message</label>
                <textarea id="support-msg" class="w-full bg-gray-50 border p-3 rounded-xl h-32 resize-none" placeholder="Describe your issue or feedback..."></textarea>
            </div>
            <button onclick="app.submitSupport()" class="w-full bg-brand-red text-white py-3 rounded-xl font-bold hover:bg-brand-darkRed transition">Submit Ticket</button>
        </div>
    </div>
  </div>

  <!-- Product Modal -->
  <div id="product-modal" class="fixed inset-0 z-[60] flex items-center justify-center bg-black/60 p-4 hidden backdrop-blur-sm">
    <div class="bg-white w-full max-w-md rounded-3xl overflow-hidden shadow-2xl animate-fade-in relative flex flex-col max-h-[90vh]">
      <!-- Image Header -->
      <div class="relative h-56 bg-gray-200">
          <img id="pm-img" class="w-full h-full object-cover">
          <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
          <button onclick="app.closeProductModal()" class="absolute top-4 right-4 bg-white/20 hover:bg-white text-white hover:text-gray-800 w-10 h-10 rounded-full flex items-center justify-center backdrop-blur-md transition-all">
              <i class="fa-solid fa-xmark text-lg"></i>
          </button>
          <div class="absolute bottom-4 left-6 text-white">
              <h2 id="pm-title" class="text-2xl font-bold shadow-black drop-shadow-md"></h2>
          </div>
      </div>

      <!-- Content -->
      <div class="p-6 flex-1 overflow-y-auto">
        <p id="pm-desc" class="text-gray-600 text-sm mb-6 leading-relaxed bg-gray-50 p-3 rounded-lg"></p>
        
        <div class="flex justify-between items-center mb-6 p-4 border border-gray-100 rounded-xl">
            <div>
                <div class="text-xs text-gray-500 uppercase font-bold tracking-wider">Price</div>
                <div id="pm-price" class="text-2xl font-bold text-brand-red"></div>
            </div>
            <div class="flex items-center gap-3 bg-gray-100 rounded-full p-1">
                <button onclick="app.modQty(-1)" class="w-8 h-8 rounded-full bg-white shadow text-gray-600 hover:text-brand-red"><i class="fa-solid fa-minus"></i></button>
                <span id="pm-qty" class="font-bold w-6 text-center text-lg">1</span>
                <button onclick="app.modQty(1)" class="w-8 h-8 rounded-full bg-white shadow text-gray-600 hover:text-brand-red"><i class="fa-solid fa-plus"></i></button>
            </div>
        </div>

        <h3 class="font-bold text-gray-800 mb-3 text-sm uppercase tracking-wide">Extras</h3>
        <div id="pm-toppings" class="space-y-2 mb-4"></div>
      </div>

      <!-- Footer Action -->
      <div class="p-6 border-t border-gray-100 bg-gray-50 flex flex-col gap-3">
          <div class="flex justify-between items-center mb-2">
              <span class="text-gray-500 font-bold">Total Amount</span>
              <span id="pm-total" class="text-xl font-bold text-gray-800">₱ 0.00</span>
          </div>
          <button onclick="app.addToCart()" class="w-full bg-brand-red text-white py-3.5 rounded-xl font-bold shadow-lg shadow-brand-red/30 hover:bg-brand-darkRed transition-all flex justify-center items-center gap-2">
              <i class="fa-solid fa-cart-plus"></i> Add To Cart
          </button>
          <!-- ORDER NOW BUTTON -->
          <button onclick="app.orderNowFromModal()" class="w-full bg-white border-2 border-brand-red text-brand-red py-3.5 rounded-xl font-bold hover:bg-brand-pink transition-all">
              Order Now
          </button>
      </div>
    </div>
  </div>

  <!-- Rating Modal -->
  <div id="rating-modal" class="fixed inset-0 z-[80] flex items-center justify-center bg-black/70 p-4 hidden backdrop-blur-sm">
    <div class="bg-white w-full max-w-sm rounded-3xl p-8 text-center shadow-2xl animate-fade-in transform scale-100">
      <div class="w-16 h-16 bg-yellow-100 text-yellow-500 rounded-full flex items-center justify-center mx-auto mb-4 text-3xl">
          <i class="fa-solid fa-face-smile-beam"></i>
      </div>
      <h3 class="text-2xl font-bold mb-2 text-gray-800">Rate Your Order</h3>
      <p class="text-gray-500 text-sm mb-8">How was the taste? We'd love to hear your feedback!</p>
      
      <div class="flex justify-center gap-3 mb-8" id="rating-stars-container">
        <i class="fa-solid fa-star star-icon" onclick="app.setRating(1)"></i>
        <i class="fa-solid fa-star star-icon" onclick="app.setRating(2)"></i>
        <i class="fa-solid fa-star star-icon" onclick="app.setRating(3)"></i>
        <i class="fa-solid fa-star star-icon" onclick="app.setRating(4)"></i>
        <i class="fa-solid fa-star star-icon" onclick="app.setRating(5)"></i>
      </div>
      
      <button onclick="app.submitRating()" class="w-full bg-brand-red text-white py-3 rounded-xl font-bold hover:bg-brand-darkRed shadow-lg transition-colors">Submit Feedback</button>
      <button onclick="document.getElementById('rating-modal').classList.add('hidden')" class="mt-4 text-gray-400 text-xs font-bold hover:text-gray-600">Skip for now</button>
    </div>
  </div>

  <!-- Auth Overlay -->
  <div id="auth-overlay" class="fixed inset-0 z-[70] bg-black/50 flex items-center justify-center p-4 hidden backdrop-blur-sm">
      <div class="bg-white p-8 rounded-3xl w-full max-w-sm relative shadow-2xl animate-fade-in">
          <button onclick="document.getElementById('auth-overlay').classList.add('hidden')" class="absolute top-5 right-5 text-gray-400 hover:text-brand-red transition-colors"><i class="fa-solid fa-xmark text-xl"></i></button>
          
          <div class="text-center mb-6">
              <h2 id="auth-title" class="text-2xl font-bold text-gray-800">Log In</h2>
              <p class="text-sm text-gray-500 mt-1">Access your account to order.</p>
          </div>
          
          <div id="auth-signup-fields" class="hidden space-y-4 mb-4">
              <input id="auth-name" placeholder="Full Name" class="w-full bg-gray-50 border border-gray-200 p-3 rounded-xl focus:ring-2 focus:ring-brand-red outline-none">
              <input id="auth-mobile" placeholder="Mobile Number" class="w-full bg-gray-50 border border-gray-200 p-3 rounded-xl focus:ring-2 focus:ring-brand-red outline-none">
          </div>

          <div class="space-y-4 mb-6">
              <input id="auth-email" placeholder="Email Address" class="w-full bg-gray-50 border border-gray-200 p-3 rounded-xl focus:ring-2 focus:ring-brand-red outline-none">
              <input id="auth-pass" type="password" placeholder="Password" class="w-full bg-gray-50 border border-gray-200 p-3 rounded-xl focus:ring-2 focus:ring-brand-red outline-none">
          </div>
          
          <button id="auth-btn" onclick="app.submitAuth(true)" class="w-full bg-brand-red text-white py-3 rounded-xl font-bold shadow-lg shadow-brand-red/30 hover:bg-brand-darkRed transition-all">Log In</button>
          
          <div class="mt-6 text-center text-sm text-gray-600">
              <span id="auth-switch-text">Don't have an account? </span>
              <button id="auth-switch-btn" onclick="app.toggleAuthMode()" class="text-brand-red font-bold hover:underline">Sign Up</button>
          </div>
      </div>
  </div>

  <!-- Checkout Overlay -->
  <div id="checkout-overlay" class="fixed inset-0 z-[70] flex items-center justify-center bg-gray-100 p-4 hidden">
    <div class="w-full max-w-md bg-white h-auto max-h-[90vh] rounded-3xl shadow-xl flex flex-col overflow-hidden relative">
        <div class="bg-brand-red p-6 text-white text-center relative shrink-0">
            <h2 class="text-xl font-bold">Checkout</h2>
            <button onclick="document.getElementById('checkout-overlay').classList.add('hidden')" class="absolute left-6 top-1/2 -translate-y-1/2 hover:scale-110"><i class="fa-solid fa-arrow-left"></i></button>
        </div>
        
        <div class="p-6 overflow-y-auto flex-1">
            <!-- Address Section -->
            <div class="mb-6">
                <h3 class="font-bold text-gray-800 mb-2">Delivering To</h3>
                <div class="mb-4">
                   <textarea id="checkout-addr" class="w-full border rounded-lg p-3 h-24 bg-gray-50 focus:ring-2 focus:ring-brand-red outline-none transition-all" placeholder="Enter your full address..."></textarea>
                </div>
            </div>

            <!-- Payment -->
            <div class="mb-6">
                <h3 class="font-bold text-gray-800 mb-2">Payment Method</h3>
                <div class="border border-brand-red bg-red-50 p-4 rounded-xl flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-money-bill-wave text-brand-red"></i>
                        <span class="font-bold text-sm">Cash On Delivery</span>
                    </div>
                    <i class="fa-solid fa-circle-check text-brand-red"></i>
                </div>
            </div>

            <!-- Summary -->
            <h3 class="font-bold text-gray-800 mb-3">Order Items</h3>
            <div id="co-items-list" class="space-y-3 mb-6"></div>
        </div>

        <div class="p-6 border-t border-gray-100 bg-gray-50">
            <div class="flex justify-between mb-2 text-sm text-gray-500"><span>Subtotal</span><span id="co-subtotal"></span></div>
            <div class="flex justify-between mb-4 text-sm text-gray-500"><span>Delivery Fee</span><span>₱ 75.00</span></div>
            <div class="flex justify-between mb-6 text-xl font-bold text-brand-red"><span>Total</span><span id="co-total"></span></div>
            <button onclick="app.submitOrder()" class="w-full bg-brand-red text-white py-4 rounded-xl font-bold shadow-lg hover:bg-brand-darkRed transition-all">Place Order</button>
        </div>
    </div>
  </div>

  <!-- INVOICE MODAL -->
  <div id="invoice-modal" class="fixed inset-0 z-[90] flex items-center justify-center bg-black/60 p-4 hidden backdrop-blur-sm">
    <div class="bg-white w-full max-w-2xl rounded-lg shadow-2xl overflow-hidden animate-fade-in relative max-h-[90vh] flex flex-col">
        <button onclick="document.getElementById('invoice-modal').classList.add('hidden')" class="absolute top-4 right-4 text-gray-500 hover:text-gray-800"><i class="fa-solid fa-xmark text-2xl"></i></button>
        <div class="p-10 overflow-y-auto flex-1">
            <div class="flex justify-between items-start mb-8 border-b pb-6">
                <div>
                    <h1 class="text-4xl font-bold text-gray-900 mb-2">Invoice</h1>
                    <div class="text-gray-600 text-sm">
                        <p class="font-bold text-gray-800">Ichiraku Ramen</p>
                        <p>123 Konoha St, Hidden Leaf</p>
                        <p>123-456-7890</p>
                    </div>
                </div>
                <div class="w-16 h-16 bg-brand-pink rounded-tr-3xl"></div> 
            </div>
            <div class="flex flex-col md:flex-row justify-between bg-blue-50 p-6 rounded-lg mb-8">
                <div class="mb-4 md:mb-0">
                    <h3 class="font-bold text-gray-900 mb-2">Bill To</h3>
                    <p class="text-gray-700 text-sm" id="inv-customer">Loading...</p>
                    <p class="text-gray-600 text-xs mt-1" id="inv-address">Loading...</p>
                </div>
                <div class="text-right">
                    <h3 class="font-bold text-gray-900 mb-2">Invoice Details</h3>
                    <div class="text-sm text-gray-600">
                        <p><strong>Invoice #:</strong> <span id="inv-id">---</span></p>
                        <p><strong>Date:</strong> <span id="inv-date">---</span></p>
                        <p><strong>Terms:</strong> COD</p>
                    </div>
                </div>
            </div>
            <table class="w-full text-left mb-8">
                <thead>
                    <tr class="border-b-2 border-gray-200">
                        <th class="py-2 text-sm font-bold text-gray-800">Product/Services</th>
                        <th class="py-2 text-sm font-bold text-gray-800 text-center">Qty</th>
                        <th class="py-2 text-sm font-bold text-gray-800 text-right">Rate</th>
                        <th class="py-2 text-sm font-bold text-gray-800 text-right">Amount</th>
                    </tr>
                </thead>
                <tbody id="inv-items"></tbody>
            </table>
            <div class="flex justify-end">
                <div class="w-full md:w-1/2 space-y-2">
                    <div class="flex justify-between text-sm text-gray-600"><span>Subtotal</span><span id="inv-subtotal">₱ 0.00</span></div>
                    <div class="flex justify-between text-sm text-gray-600"><span>Delivery Fee</span><span>₱ 75.00</span></div>
                    <div class="flex justify-between text-xl font-bold text-gray-900 border-t pt-2 mt-2"><span>Total</span><span id="inv-total">₱ 0.00</span></div>
                </div>
            </div>
        </div>
        <div class="bg-gray-100 p-4 text-center text-gray-500 text-xs font-bold border-t">Thank you for dining with Ichiraku Ramen!</div>
    </div>
  </div>

  <!-- ================= JAVASCRIPT LOGIC ================= -->
  <script>
    const DESCRIPTION_FALLBACK = {
      "Ramen stir-fries": "A quick and customizable option with noodles stir-fried with sauces, and protein.",
      "Hiyashi chuka": "A refreshing chilled noodle dish topped with crisp vegetables, savory ham, and a tangy soy dressing.",
      "Ramen snack mix": "Spicy seasoned ramen wrapped in chewy rice paper for a fun snack.",
      "Spicy noodle roll": "A savory and crunchy party mix featuring toasted ramen bits and nuts."
    };

    const TOPPINGS = [
        { name: 'Soft-boiled Egg', price: 40 },
        { name: 'Chashu Pork', price: 80 },
        { name: 'Nori Seaweed', price: 15 },
        { name: 'Extra Noodles', price: 50 }
    ];

    const app = {
      user: null, cart: [], dishes: [], orders: [], notifications: [],
      activeDish: null, modalQty: 1, modalToppings: [],
      currentRatingStars: 0, ratingOrderId: 0, 
      notificationsEnabled: false, selectedAddress: null,
      checkoutList: [], 
      
      init: function() {
        this.fetchMenu();
        this.checkSession();
        this.loadLocalPreferences();
        setInterval(() => {
            this.fetchNotifications();
            if(!document.getElementById('dash-content-orders').classList.contains('hidden')) this.loadOrders();
        }, 5000);
      },

      fetchMenu: function() {
        fetch('api_menu.php').then(r=>r.json()).then(data => {
          this.dishes = data.map((p,i) => ({
            id: p.id, title: p.title, price: Number(p.price), image: p.image,
            category: p.category == 2 ? 'Drinks' : 'Food',
            description: DESCRIPTION_FALLBACK[p.title] || p.description || "Authentic Japanese flavor."
          }));
          this.renderDishes();
        }).catch(e => console.error(e));
      },

      renderDishes: function() {
        const grid = document.getElementById('dish-grid'); const dGrid = document.getElementById('dash-menu-grid');
        grid.innerHTML = ''; dGrid.innerHTML = '';
        const cat = document.querySelector('.category-btn.active').innerText;
        const search = document.getElementById('search-input').value.toLowerCase();

        this.dishes.forEach(d => {
          if ((cat !== 'All' && d.category !== cat) || !d.title.toLowerCase().includes(search)) return;
          const cardHtml = `
            <div onclick="app.openProductModal(${d.id})" class="bg-white rounded-2xl shadow-sm overflow-hidden cursor-pointer hover:-translate-y-2 transition-all duration-300 group border border-gray-100 hover:shadow-xl hover:border-brand-pink h-full flex flex-col">
              <div class="h-48 overflow-hidden relative">
                  <img src="${d.image}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                  <div class="absolute bottom-2 right-2 bg-white/90 backdrop-blur px-3 py-1 rounded-full text-xs font-bold text-gray-700 shadow-sm"><i class="fa-solid fa-clock text-brand-red"></i> 15m</div>
              </div>
              <div class="p-5 flex flex-col flex-1">
                <h3 class="font-bold text-lg mb-2 text-gray-800 group-hover:text-brand-red transition-colors">${d.title}</h3>
                <p class="text-xs text-gray-500 mb-4 line-clamp-2 flex-1">${d.description}</p>
                <div class="flex justify-between items-center mt-auto pt-4 border-t border-gray-50">
                    <span class="font-bold text-lg text-gray-900">₱${d.price.toFixed(2)}</span>
                    <button class="bg-brand-red text-white w-10 h-10 rounded-full shadow-lg shadow-brand-red/40 hover:bg-brand-darkRed hover:scale-110 transition-all flex items-center justify-center"><i class="fa-solid fa-plus"></i></button>
                </div>
              </div>
            </div>`;
          grid.innerHTML += cardHtml; dGrid.innerHTML += cardHtml;
        });
      },

      setCategory: function(c) {
        document.querySelectorAll('.category-btn').forEach(b => {
          b.className = (b.innerText === c) ? "category-btn active px-8 py-3 rounded-full bg-brand-red text-white shadow-lg shadow-brand-red/30 font-semibold transition-transform transform scale-105" : "category-btn px-8 py-3 rounded-full bg-white text-gray-700 shadow-sm border border-gray-100 font-semibold transition-transform hover:-translate-y-1 hover:shadow-md";
        });
        this.renderDishes();
      },

      openProductModal: function(id) {
        this.activeDish = this.dishes.find(d => d.id == id); this.modalQty = 1; this.modalToppings = [];
        document.getElementById('pm-title').innerText = this.activeDish.title;
        document.getElementById('pm-desc').innerText = this.activeDish.description;
        document.getElementById('pm-img').src = this.activeDish.image;
        document.getElementById('pm-qty').innerText = 1;
        const tDiv = document.getElementById('pm-toppings'); tDiv.innerHTML = '';
        TOPPINGS.forEach((t, idx) => {
             tDiv.innerHTML += `<div id="top-${idx}" onclick="app.toggleTopping(${idx})" class="flex justify-between items-center p-3 rounded-xl border border-gray-100 cursor-pointer hover:bg-gray-50 transition-colors">
                    <div class="flex items-center gap-3"><div class="w-5 h-5 rounded border border-gray-300 flex items-center justify-center top-check text-white text-xs"><i class="fa-solid fa-check hidden"></i></div><span class="text-sm font-medium text-gray-700">${t.name}</span></div><span class="text-xs font-bold text-brand-red">+₱${t.price}</span></div>`;
        });
        this.updateModalPrice();
        document.getElementById('product-modal').classList.remove('hidden');
      },
      closeProductModal: function() { document.getElementById('product-modal').classList.add('hidden'); },
      modQty: function(n) { this.modalQty = Math.max(1, this.modalQty + n); document.getElementById('pm-qty').innerText = this.modalQty; this.updateModalPrice(); },
      toggleTopping: function(idx) {
          const t = TOPPINGS[idx]; const div = document.getElementById('top-'+idx); const icon = div.querySelector('.fa-check'); const box = div.querySelector('.top-check');
          if(this.modalToppings.includes(t)){ this.modalToppings = this.modalToppings.filter(x => x !== t); div.classList.remove('border-brand-red', 'bg-red-50'); box.classList.remove('bg-brand-red', 'border-brand-red'); icon.classList.add('hidden'); } else { this.modalToppings.push(t); div.classList.add('border-brand-red', 'bg-red-50'); box.classList.add('bg-brand-red', 'border-brand-red'); icon.classList.remove('hidden'); }
          this.updateModalPrice();
      },
      updateModalPrice: function() {
          const base = this.activeDish.price; const topTotal = this.modalToppings.reduce((a,b) => a + b.price, 0); const final = (base + topTotal) * this.modalQty;
          document.getElementById('pm-price').innerText = '₱' + (base + topTotal).toFixed(2); document.getElementById('pm-total').innerText = '₱' + final.toFixed(2);
      },
      addToCart: function(silent=false) {
        const topTotal = this.modalToppings.reduce((a,b) => a + b.price, 0); const finalPrice = (this.activeDish.price + topTotal) * this.modalQty;
        this.cart.push({ ...this.activeDish, qty: this.modalQty, toppings: [...this.modalToppings], totalPrice: finalPrice });
        this.renderCart();
        if(!silent) { this.closeProductModal(); this.openDashboard('cart'); const badge = document.getElementById('nav-cart-count'); badge.classList.add('scale-125'); setTimeout(() => badge.classList.remove('scale-125'), 200); }
      },

      // --- ORDER NOW & CHECKOUT LOGIC ---
      orderNowFromModal: function() {
          const topTotal = this.modalToppings.reduce((a,b) => a + b.price, 0); 
          const finalPrice = (this.activeDish.price + topTotal) * this.modalQty;
          
          this.checkoutList = [{ 
              ...this.activeDish, 
              qty: this.modalQty, 
              toppings: [...this.modalToppings], 
              totalPrice: finalPrice 
          }];
          
          document.getElementById('product-modal').classList.add('hidden');
          setTimeout(() => this.openCheckout(), 100);
      },

      initiateCartCheckout: function() {
          if(this.cart.length === 0) return;
          this.checkoutList = [...this.cart];
          this.openCheckout();
      },

      renderCart: function() {
        const c = document.getElementById('cart-items-container'); const badge = document.getElementById('nav-cart-count'); c.innerHTML = ''; let total = 0;
        if (this.cart.length > 0) {
          badge.innerText = this.cart.length; badge.classList.remove('hidden');
          this.cart.forEach((item, idx) => { total += item.totalPrice; const tops = item.toppings.map(t=>t.name).join(', ');
            c.innerHTML += `<div class="flex gap-4 bg-white p-4 rounded-2xl border border-gray-100 items-center shadow-sm relative overflow-hidden group">
                <img src="${item.image}" class="w-16 h-16 rounded-xl object-cover"><div class="flex-1"><h4 class="font-bold text-sm text-gray-800">${item.title}</h4><p class="text-xs text-gray-500 truncate">${tops || 'No Toppings'}</p><div class="text-xs font-bold text-brand-red mt-1">₱${item.totalPrice.toFixed(2)}</div></div>
                <div class="flex flex-col items-end gap-1"><span class="font-bold text-gray-600">x${item.qty}</span><button onclick="app.remCart(${idx})" class="text-xs text-red-400 bg-red-50 px-2 py-1 rounded hover:bg-brand-red hover:text-white transition-colors">Remove</button></div></div>`;
          });
          document.getElementById('cart-summary-container').innerHTML = `<div class="bg-gray-50 p-6 rounded-3xl border border-gray-100"><div class="flex justify-between mb-2 text-sm text-gray-500"><span>Subtotal</span><span>₱${total.toFixed(2)}</span></div><div class="flex justify-between mb-4 text-sm text-gray-500"><span>Delivery</span><span>₱75.00</span></div><div class="flex justify-between font-bold text-xl mb-6 text-gray-800 border-t pt-4"><span>Total</span><span>₱${(total+75).toFixed(2)}</span></div><button onclick="app.initiateCartCheckout()" class="w-full bg-brand-red text-white py-4 rounded-xl font-bold shadow-lg shadow-brand-red/30 hover:bg-brand-darkRed transition-all">Proceed to Checkout</button></div>`;
        } else {
          badge.classList.add('hidden'); c.innerHTML = `<div class="text-center py-10 opacity-50"><i class="fa-solid fa-cart-arrow-down text-6xl mb-4 text-gray-300"></i><p>Your cart is empty.</p><button onclick="app.switchTab('menu')" class="mt-4 text-brand-red font-bold underline">Browse Menu</button></div>`; document.getElementById('cart-summary-container').innerHTML = '';
        }
      },
      remCart: function(i) { this.cart.splice(i, 1); this.renderCart(); },

      openCheckout: function() {
          if(!this.user) { app.openAuth(true); return; }
          if(!this.selectedAddress) { 
              alert("Please add a shipping address in Settings first."); 
              this.openDashboard('settings'); 
              this.openSettingsAddress(); 
              return; 
          }
          if(this.checkoutList.length === 0) return;

          document.getElementById('checkout-addr').value = this.selectedAddress.details;
          const list = document.getElementById('co-items-list'); list.innerHTML = '';
          this.checkoutList.forEach(i => { list.innerHTML += `<div class="flex justify-between text-sm"><span class="text-gray-600">${i.qty}x ${i.title}</span><span class="font-bold">₱${i.totalPrice.toFixed(2)}</span></div>`; });
          
          const safeSubtotal = this.checkoutList.reduce((sum, item) => sum + item.totalPrice, 0);
          document.getElementById('co-subtotal').innerText = '₱' + safeSubtotal.toFixed(2); document.getElementById('co-total').innerText = '₱' + (safeSubtotal + 75).toFixed(2);
          document.getElementById('checkout-overlay').classList.remove('hidden');
      },

      submitOrder: function() {
          const addr = document.getElementById('checkout-addr').value; 
          if(!addr.trim()) { alert('Address required'); return; }
          
          const total = this.checkoutList.reduce((a,b) => a + b.totalPrice, 0) + 75;
          const itemsPayload = this.checkoutList.map(c => ({ dish: { id:c.id, title:c.title, price:c.price }, qty: c.qty, totalPrice: c.totalPrice }));
          
          fetch('api_checkout.php', { method: 'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify({ items: itemsPayload, total: total, address: addr }) })
            .then(r=>r.json()).then(res => { 
                if (res.success) { 
                    document.getElementById('checkout-overlay').classList.add('hidden'); 
                    if(this.checkoutList.length === this.cart.length) { this.cart = []; this.renderCart(); }
                    this.openDashboard('orders'); this.loadOrders(); alert('Order Placed Successfully! Waiting for admin confirmation.'); 
                } 
                else { alert('Order Failed: ' + (res.message || 'Unknown Error')); } 
            }).catch(err => { console.error(err); alert('Network Error'); });
      },

      loadOrders: function() {
        if (!this.user) { document.getElementById('orders-login-msg').classList.remove('hidden'); return; }
        document.getElementById('orders-login-msg').classList.add('hidden');
        fetch('api_orders.php').then(r=>r.json()).then(data => { this.orders = data; this.renderOrdersUI(); });
      },

      renderOrdersUI: function() {
        const container = document.getElementById('orders-container'); container.innerHTML = '';
        if (this.orders.length === 0) { container.innerHTML = '<div class="text-center text-gray-400 py-10 opacity-60"><i class="fa-solid fa-receipt text-5xl mb-3"></i><p>No order history found.</p></div>'; return; }
        
        const getStatusId = (s) => { const st = (s||'').toLowerCase(); if(st.includes('pending')) return 1; if(st.includes('preparing')) return 2; if(st.includes('out')) return 3; if(st.includes('delivered')) return 4; if(st.includes('cancel')) return 5; return 1; };

        this.orders.forEach(o => {
          const statusId = getStatusId(o.status);
          let progress = 10; let color = 'bg-yellow-400'; let icon = 'fa-clock'; let statusText = o.status;
          if (statusId == 1) { progress = 10; icon = 'fa-hourglass-start'; } 
          else if (statusId == 2) { progress = 40; color = 'bg-orange-500'; statusText="Preparing"; icon = 'fa-fire-burner'; }
          else if (statusId == 3) { progress = 75; color = 'bg-blue-500'; icon = 'fa-motorcycle'; } 
          else if (statusId == 4) { progress = 100; color = 'bg-green-500'; icon = 'fa-check-circle'; } 
          else if (statusId == 5) { progress = 100; color = 'bg-red-500'; icon = 'fa-ban'; }

          let actionBtn = '';
          if (statusId == 4) {
             if(o.rating && o.rating > 0) {
                 let stars = ''; for(let i=1;i<=5;i++) stars += `<i class="fa-solid fa-star ${i<=o.rating?'text-yellow-400':'text-gray-300'} text-xs"></i>`;
                 actionBtn = `<div class="mt-4 text-center bg-gray-50 py-2 rounded-xl border border-gray-100">${stars}</div>`;
             } else { actionBtn = `<button onclick="app.openRatingModal(${o.id})" class="mt-4 w-full border border-brand-red text-brand-red font-bold py-2 rounded-xl hover:bg-brand-red hover:text-white transition-colors">Rate Order</button>`; }
          }

          const itemsHtml = (o.items||[]).map(i => `<span class="inline-block bg-gray-100 rounded px-2 py-1 text-xs text-gray-700 mr-2 mb-2">${i.qty}x ${i.dish.title}</span>`).join('');
          
          container.innerHTML += `
            <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 relative overflow-hidden">
              <div class="absolute top-0 right-0 p-4 opacity-10 text-6xl text-gray-800"><i class="fa-solid ${icon}"></i></div>
              <div class="flex justify-between items-start mb-4 relative z-10">
                <div><h3 class="font-bold text-lg text-gray-800">Order #${String(o.id).padStart(4,'0')}</h3><p class="text-xs text-gray-500">${o.date}</p></div>
                <div class="text-right"><div class="font-bold text-brand-red text-lg">₱${o.total.toFixed(2)}</div><div class="text-xs text-gray-400">COD</div></div>
              </div>
              <div class="mb-4">${itemsHtml}</div>
              <div class="relative z-10">
                 <div class="flex justify-between text-xs font-bold text-gray-600 mb-2 uppercase tracking-wide"><span>${statusText}</span><span>${progress}%</span></div>
                 <div class="w-full bg-gray-100 rounded-full h-3 overflow-hidden shadow-inner"><div class="h-full ${color} transition-all duration-1000 relative" style="width:${progress}%"><div class="absolute inset-0 bg-white/20 animate-pulse"></div></div></div>
              </div>
              ${actionBtn}
            </div>`;
        });
      },

      fetchNotifications: function() {
        if(!this.user) return;
        fetch('api_notifications.php').then(r=>r.json()).then(data => {
            const list = document.getElementById('notif-list');
            const unread = data.filter(n => !n.is_read).length;
            document.getElementById('header-notif-dot').classList.toggle('hidden', unread === 0);
            const navBadge = document.getElementById('nav-notif-count');
            navBadge.innerText = unread > 9 ? '9+' : unread; navBadge.classList.toggle('hidden', unread === 0);

            if(data.length === 0) { list.innerHTML = '<div class="text-center text-gray-400 py-10">No notifications</div>'; return; }
            
            list.innerHTML = data.map(n => {
                const bg = n.is_read ? 'bg-white border-gray-100' : 'bg-red-50 border-red-200'; 
                const iconColor = n.is_read ? 'text-gray-400' : 'text-brand-red';
                const textStyle = n.is_read ? 'text-gray-500' : 'text-gray-900 font-bold';
                const newBadge = !n.is_read ? '<span class="bg-red-500 text-white text-[10px] px-2 py-0.5 rounded-full ml-auto shadow">NEW</span>' : '';
                
                const actionHtml = n.order_id 
                    ? `<div class="mt-3"><button onclick="event.stopPropagation(); app.openInvoice(${n.order_id})" class="bg-white border border-brand-red text-brand-red text-xs font-bold px-4 py-2 rounded-lg hover:bg-brand-red hover:text-white transition flex items-center gap-2 shadow-sm"><i class="fa-solid fa-eye"></i> View Invoice</button></div>`
                    : '';

                return `<div onclick="app.readNotif(${n.id})" class="${bg} p-5 rounded-xl border border-dashed shadow-sm cursor-pointer transition hover:shadow-md relative overflow-hidden group">
                    <div class="absolute top-0 right-0 p-2 opacity-10 text-6xl ${iconColor}"><i class="fa-solid fa-file-invoice"></i></div>
                    <div class="flex items-center justify-between mb-3 relative z-10"><span class="font-bold text-xs uppercase tracking-wider text-gray-500">${n.date}</span>${newBadge}</div>
                    <h4 class="${textStyle} text-lg mb-1 relative z-10 font-mono">${n.title}</h4>
                    <p class="text-sm text-gray-600 whitespace-pre-line font-mono leading-relaxed relative z-10 bg-white/50 p-2 rounded">${n.message}</p>
                    ${actionHtml}
                    <div class="mt-2 text-right"><span class="text-[10px] font-bold text-brand-red uppercase tracking-widest">Ichiraku Official</span></div></div>`;
            }).join('');
        });
      },

      readNotif: function(id) { const fd = new FormData(); fd.append('action','read'); fd.append('id',id); fetch('api_notifications.php',{method:'POST',body:fd}).then(() => this.fetchNotifications()); },
      
      openInvoice: function(orderId) {
          const fd = new FormData(); fd.append('action', 'get_details'); fd.append('order_id', orderId);
          fetch('api_orders.php', { method: 'POST', body: fd })
            .then(r=>r.json()).then(res => {
                if(res.success) {
                    const o = res.order; const items = res.items;
                    document.getElementById('inv-customer').innerText = o.customer_name;
                    document.getElementById('inv-address').innerText = o.shipping_address || 'Pick Up';
                    document.getElementById('inv-id').innerText = o.order_id;
                    const date = new Date(o.order_date); document.getElementById('inv-date').innerText = date.toLocaleDateString();
                    const tbody = document.getElementById('inv-items');
                    tbody.innerHTML = items.map(i => `<tr class="border-b border-gray-100"><td class="py-2 text-sm text-gray-700">${i.product_name}</td><td class="py-2 text-sm text-gray-700 text-center">${i.quantity}</td><td class="py-2 text-sm text-gray-700 text-right">₱${Number(i.price).toFixed(2)}</td><td class="py-2 text-sm font-bold text-gray-800 text-right">₱${Number(i.subtotal).toFixed(2)}</td></tr>`).join('');
                    const subtotal = items.reduce((sum, i) => sum + Number(i.subtotal), 0);
                    document.getElementById('inv-subtotal').innerText = '₱' + subtotal.toFixed(2);
                    document.getElementById('inv-total').innerText = '₱' + Number(o.total_amount).toFixed(2);
                    document.getElementById('invoice-modal').classList.remove('hidden');
                } else { alert('Could not load invoice.'); }
            });
      },

      loadLocalPreferences: function() { const addr = localStorage.getItem('ichiraku_address'); if (addr) { try { this.selectedAddress = JSON.parse(addr); } catch(e) { this.selectedAddress = null; } } this.updateAddressDisplay(); const notif = localStorage.getItem('ichiraku_notifications') === 'true'; this.notificationsEnabled = notif; this.updateNotifUI(); },
      toggleNotifications: function() { this.notificationsEnabled = !this.notificationsEnabled; localStorage.setItem('ichiraku_notifications', this.notificationsEnabled); this.updateNotifUI(); if(this.notificationsEnabled) alert("Notifications Enabled!"); },
      updateNotifUI: function() { const bg = document.getElementById('notif-bg'); const dot = document.getElementById('notif-dot'); if (this.notificationsEnabled) { bg.classList.remove('bg-gray-300'); bg.classList.add('bg-brand-red'); dot.classList.add('translate-x-5'); } else { bg.classList.add('bg-gray-300'); bg.classList.remove('bg-brand-red'); dot.classList.remove('translate-x-0', 'translate-x-5'); } },
      openSettingsAddress: function() { document.getElementById('settings-main-view').classList.add('hidden'); document.getElementById('settings-address-editor').classList.remove('hidden'); if (this.selectedAddress) { document.getElementById('set-addr-label').value = this.selectedAddress.name; document.getElementById('set-addr-details').value = this.selectedAddress.details; } },
      closeSettingsAddress: function() { document.getElementById('settings-address-editor').classList.add('hidden'); document.getElementById('settings-main-view').classList.remove('hidden'); },
      saveSettingsAddress: function() { const label = document.getElementById('set-addr-label').value; const details = document.getElementById('set-addr-details').value; if (!label || !details) { alert('Please fill all fields'); return; } this.selectedAddress = { name: label, details: details }; localStorage.setItem('ichiraku_address', JSON.stringify(this.selectedAddress)); this.updateAddressDisplay(); this.closeSettingsAddress(); },
      updateAddressDisplay: function() { const disp = document.getElementById('settings-address-display'); if (this.selectedAddress) { disp.innerHTML = `<div class="flex items-center gap-2 mb-1"><i class="fa-solid fa-house text-brand-red"></i> <span class="font-bold text-gray-800">${this.selectedAddress.name}</span></div><div class="text-gray-600">${this.selectedAddress.details}</div>`; disp.classList.remove('italic'); } else { disp.innerHTML = "No address set. Tap 'Edit' to add one."; disp.classList.add('italic'); } },
      
      openRatingModal: function(oid) { this.ratingOrderId = oid; this.currentRatingStars = 0; this.updateStars(); document.getElementById('rating-modal').classList.remove('hidden'); },
      setRating: function(n) { this.currentRatingStars = n; this.updateStars(); },
      updateStars: function() { const stars = document.getElementById('rating-stars-container').children; for(let i=0; i<5; i++) { if (i < this.currentRatingStars) stars[i].classList.add('active'); else stars[i].classList.remove('active'); } },
      submitRating: function() { const fd = new FormData(); fd.append('action','rate'); fd.append('order_id', this.ratingOrderId); fd.append('rating', this.currentRatingStars); fetch('api_orders.php',{method:'POST',body:fd}).then(r=>r.json()).then(d=>{ document.getElementById('rating-modal').classList.add('hidden'); this.loadOrders(); }); },

      // NEW: Open Privacy Modal
      openPrivacy: function() { document.getElementById('privacy-modal').classList.remove('hidden'); },
      
      // NEW: Open Support Modal
      openSupport: function() { 
          if(!this.user) { app.openAuth(true); return; }
          document.getElementById('support-modal').classList.remove('hidden'); 
      },
      
      // NEW: Submit Support
      submitSupport: function() {
          const subject = document.getElementById('support-subject').value;
          const msg = document.getElementById('support-msg').value;
          if(!msg.trim()) return alert("Please enter a message");
          
          const fd = new FormData();
          fd.append('subject', subject);
          fd.append('message', msg);
          
          fetch('api_support.php', { method:'POST', body:fd })
            .then(r=>r.json()).then(res => {
                if(res.success) {
                    alert('Ticket Submitted! Support team will contact you.');
                    document.getElementById('support-modal').classList.add('hidden');
                    document.getElementById('support-msg').value = '';
                } else {
                    alert('Error: ' + res.message);
                }
            });
      },

      openDashboard: function(tab) { document.getElementById('dashboard-overlay').classList.remove('hidden'); this.switchTab(tab); },
      closeDashboard: function() { document.getElementById('dashboard-overlay').classList.add('hidden'); },
      switchTab: function(tab) { document.querySelectorAll('.dash-content').forEach(e => e.classList.add('hidden')); document.getElementById('dash-content-'+tab).classList.remove('hidden'); document.querySelectorAll('.dash-nav-item').forEach(e => e.classList.remove('bg-brand-red','text-white','shadow-lg','shadow-brand-red/20')); document.getElementById('nav-'+tab).classList.add('bg-brand-red','text-white','shadow-lg','shadow-brand-red/20'); if (tab === 'orders') this.loadOrders(); if (tab === 'notifications') this.fetchNotifications(); },

      openAuth: function(isLogin) { document.getElementById('auth-overlay').classList.remove('hidden'); this.toggleAuthMode(isLogin); },
      toggleAuthMode: function(forceLogin) { const isLogin = forceLogin !== undefined ? forceLogin : document.getElementById('auth-title').innerText !== 'Log In'; const title = document.getElementById('auth-title'); const btn = document.getElementById('auth-btn'); const signup = document.getElementById('auth-signup-fields'); const swBtn = document.getElementById('auth-switch-btn'); const swTxt = document.getElementById('auth-switch-text'); if(isLogin){ title.innerText='Log In'; btn.innerText='Log In'; btn.onclick=()=>this.submitAuth(true); signup.classList.add('hidden'); swTxt.innerText='Don\'t have an account? '; swBtn.innerText='Sign Up'; } else { title.innerText='Sign Up'; btn.innerText='Sign Up'; btn.onclick=()=>this.submitAuth(false); signup.classList.remove('hidden'); swTxt.innerText='Already have an account? '; swBtn.innerText='Log In'; } },
      submitAuth: function(isLogin) { const e = document.getElementById('auth-email').value; const p = document.getElementById('auth-pass').value; const fd = new FormData(); if(isLogin) { fd.append('action','login'); fd.append('email',e); fd.append('password',p); } else { const n = document.getElementById('auth-name').value; const m = document.getElementById('auth-mobile').value; fd.append('action','register'); fd.append('email',e); fd.append('password',p); fd.append('name',n); fd.append('mobile',m); } fetch('api_auth.php', { method:'POST', body:fd }).then(r=>r.json()).then(res => { if (res.success) { this.user = { name: res.name || e.split('@')[0], role: res.role }; if(res.role === 'admin') { window.location.href = 'admin_dashboard.php'; return; } document.getElementById('auth-overlay').classList.add('hidden'); this.updateUserUI(); if(this.cart.length > 0) this.openDashboard('cart'); } else { alert(res.message); } }); },
      checkSession: function() { const fd = new FormData(); fd.append('action', 'check_session'); fetch('api_auth.php', { method:'POST', body:fd }).then(r=>r.json()).then(res => { if(res.success) { this.user = { name: res.name, role: res.role }; if(res.role === 'admin') { window.location.href = 'admin_dashboard.php'; return; } this.updateUserUI(); } }); },
      
      // FIXED: USER UI UPDATE (Correct Badge Placement)
      updateUserUI: function() { 
          const name = this.user ? this.user.name : 'Account'; 
          document.getElementById('dash-username').innerText = "Hello, " + name + "!"; 
          
          if(this.user) { 
              document.getElementById('dash-badge').classList.remove('hidden'); 
              document.getElementById('dash-status-text').innerText = "Welcome back!";
              document.getElementById('dash-status-text').onclick = null;
              document.getElementById('dash-status-text').classList.remove('cursor-pointer', 'hover:text-brand-red', 'underline');
              document.getElementById('sidebar-footer').innerHTML = `<button onclick="app.logout()" class="w-full bg-red-100 text-brand-red py-3 rounded-xl font-bold hover:bg-red-200 transition-colors">Log Out</button>`; 
          } else {
              document.getElementById('dash-badge').classList.add('hidden');
              document.getElementById('dash-status-text').innerText = "Login to order";
              document.getElementById('dash-status-text').onclick = () => this.openAuth(true);
              document.getElementById('dash-status-text').classList.add('cursor-pointer', 'hover:text-brand-red', 'underline');
              document.getElementById('sidebar-footer').innerHTML = ''; 
          } 
      },
      
      logout: function() { fetch('api_auth.php', { method:'POST', body: new URLSearchParams({action:'logout'}) }).then(()=>{ window.location.reload(); }); }
    };
    window.onload = () => app.init();
  </script>
</body>
</html>