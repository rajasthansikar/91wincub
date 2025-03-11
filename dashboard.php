<?php
// dashboard.php
session_start();
require_once 'db_connection.php';

// Redirect to login if not authenticated
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
  header("Location: index.php");
  exit;
}

$userID = $_SESSION['user_id'];

// Retrieve wallet balance from database
$sql = "SELECT wallet FROM users WHERE user_id = '$userID'";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
$wallet = $row['wallet'] ?? 0.00;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard - Daman Club</title>
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" />
  <!-- Bootstrap 5 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" />

<!-- Font Awesome CSS (adjust the integrity and version as needed) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />

  <!-- Optional Custom CSS -->
  <link rel="stylesheet" href="style.css" />
</head>
<body class="bg-light">

  <!-- Navbar with Always Visible Wallet Info -->
  <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
    <div class="container">
      <!-- Site Brand -->
      <a class="navbar-brand fw-bold text-danger" href="#">Daman</a>
      
      <!-- Right Side: Wallet Info & Toggler -->
      <div class="d-flex align-items-center order-lg-2">
        <!-- Wallet always visible -->
        <span class="me-3 fw-bold">
          Wallet: ₹<?php echo number_format($wallet, 2); ?>
        </span>
        <!-- Navbar Toggler -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#damanNav" aria-controls="damanNav" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
      </div>
      
      <!-- Collapsible Navigation Links -->
      <div class="collapse navbar-collapse order-lg-1" id="damanNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
          <a class="nav-link" href="user_account.php">Account</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="user_account.php">Account</a>
          </li>
          <!-- Dropdown for additional options -->
           <br>
          <li><a class="dropdown-item" href="logout.php">Log Out</a></li>
          
        </ul>
      </div>
    </div>
  </nav>
<!-- Bootstrap Bundle with Popper.js (Required for Toggle) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  document.addEventListener("click", function (event) {
    const navbarCollapse = document.querySelector("#damanNav");
    const navbarToggler = document.querySelector(".navbar-toggler");

    // If clicked outside the navbar when open, close it
    if (navbarCollapse.classList.contains("show") && !navbarToggler.contains(event.target) && !navbarCollapse.contains(event.target)) {
      new bootstrap.Collapse(navbarCollapse, { toggle: true });
    }
  });
</script>

   <!-- Fixed Size & Scrollable Content Modal -->
<div
  class="modal fade"
  id="depositBonusModal"
  tabindex="-1"
  aria-labelledby="depositBonusModalLabel"
  aria-hidden="true"
>
  <div class="modal-dialog modal-dialog-centered modal-fixed">
    <div class="modal-content">
      <!-- Modal Header -->
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="depositBonusModalLabel">
          Extra first deposit bonus
        </h5>
        <button
          type="button"
          class="btn-close btn-close-white"
          data-bs-dismiss="modal"
          aria-label="Close"
        ></button>
      </div>
      <!-- Modal Body -->
      <div class="modal-body p-3">
        <p class="small text-muted mb-3">
          Each account can only receive rewards once
        </p>

        <!-- Example Row 1 -->
        <div class="mb-3 p-2 border rounded d-flex flex-column flex-sm-row align-items-start align-items-sm-center">
          <div class="me-sm-3 mb-2 mb-sm-0">
            <strong>First deposit ₹100000</strong>
            <p class="mb-0 text-danger">+ ₹5,888 bonus</p>
          </div>
          <div class="flex-grow-1 me-3">
            <div class="progress" style="height: 8px;">
              <div
                class="progress-bar bg-success"
                role="progressbar"
                style="width: 0%"
                aria-valuenow="0"
                aria-valuemin="0"
                aria-valuemax="100"
              ></div>
            </div>
            <small class="text-muted">0 / 100000</small>
          </div>
          <button class="btn btn-sm btn-outline-danger">Deposit</button>
        </div>

        <!-- Example Row 2 -->
        <div class="mb-3 p-2 border rounded d-flex flex-column flex-sm-row align-items-start align-items-sm-center">
          <div class="me-sm-3 mb-2 mb-sm-0">
            <strong>First deposit ₹50000</strong>
            <p class="mb-0 text-danger">+ ₹3,888 bonus</p>
          </div>
          <div class="flex-grow-1 me-3">
            <div class="progress" style="height: 8px;">
              <div
                class="progress-bar bg-success"
                role="progressbar"
                style="width: 0%"
                aria-valuenow="0"
                aria-valuemin="0"
                aria-valuemax="100"
              ></div>
            </div>
            <small class="text-muted">0 / 50000</small>
          </div>
          <button class="btn btn-sm btn-outline-danger">Deposit</button>
        </div>

        <!-- Example Row 3 -->
        <div class="mb-3 p-2 border rounded d-flex flex-column flex-sm-row align-items-start align-items-sm-center">
          <div class="me-sm-3 mb-2 mb-sm-0">
            <strong>First deposit ₹100000</strong>
            <p class="mb-0 text-danger">+ ₹800 bonus</p>
          </div>
          <div class="flex-grow-1 me-3">
            <div class="progress" style="height: 8px;">
              <div
                class="progress-bar bg-success"
                role="progressbar"
                style="width: 0%"
                aria-valuenow="0"
                aria-valuemin="0"
                aria-valuemax="100"
              ></div>
            </div>
            <small class="text-muted">0 / 100000</small>
          </div>
          <button class="btn btn-sm btn-outline-danger">Deposit</button>
        </div>

        <!-- Example Row 4 -->
        <div class="mb-3 p-2 border rounded d-flex flex-column flex-sm-row align-items-start align-items-sm-center">
          <div class="me-sm-3 mb-2 mb-sm-0">
            <strong>First deposit ₹100000</strong>
            <p class="mb-0 text-danger">+ ₹500 bonus</p>
          </div>
          <div class="flex-grow-1 me-3">
            <div class="progress" style="height: 8px;">
              <div
                class="progress-bar bg-success"
                role="progressbar"
                style="width: 0%"
                aria-valuenow="0"
                aria-valuemin="0"
                aria-valuemax="100"
              ></div>
            </div>
            <small class="text-muted">0 / 100000</small>
          </div>
          <button class="btn btn-sm btn-outline-danger">Deposit</button>
        </div>

        <!-- "No more reminders" & Activity Section -->
        <div class="d-flex justify-content-between align-items-center mt-4">
          <div>
            <input type="checkbox" id="noRemindersToday" />
            <label for="noRemindersToday" class="ms-1">
              No more reminders today
            </label>
          </div>
          <button class="btn btn-danger btn-sm" id="activityBtn">Activity</button>
        </div>
      </div>
    </div>
  </div>
</div>

    <!-- Hero Section with Automatic Slideshow -->
<section class="hero">
  <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="3000">
    <div class="carousel-inner">
      <!-- Slide 1 -->
      <div class="carousel-item active">
        <img
          src="IMAGE/HERO 1.png"
          class="d-block w-100"
          alt="Hero Image 1"
        />
      </div>
      <!-- Slide 2 -->
      <div class="carousel-item">
        <img
          src="IMAGE/Banner_202305270515371rsv.png"
          class="d-block w-100"
          alt="Hero Image 2"
        />
      </div>
      <!-- Slide 3 -->
      <div class="carousel-item">
        <img
          src="IMAGE/Banner_20230306180901ggp9.png"
          class="d-block w-100"
          alt="Hero Image 3"
        />
      </div>
    </div>
  </div>
</section>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Animated Announcement Bar</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background: #f8f9fa;
        }
        .announcement-bar {
            display: flex;
            align-items: center;
            background: #ffe6e6;
            color: #333;
            padding: 10px 15px;
            border-radius: 8px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: 20px auto;
            overflow: hidden;
            position: relative;
            height: 50px;
            
        }
        .announcement-icon {
            font-size: 20px;
            color: red;
            margin-right: 10px;
        }
        .announcement-text {
            flex-grow: 1;
            font-weight: bold;
            font-size: 16px;
            height: 40px;
            overflow: hidden;
            display: flex;
            align-items: center;
            position: relative;
        }
        .announcement-text span {
            position: absolute;
            width: 100%;
            text-align: left;
            transition: transform 0.5s ease-in-out;
        }
        .detail-btn {
            background: #ff6b6b;
            color: white;
            padding: 5px 15px;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
        }
        .detail-btn:hover {
            background: #ff3b3b;
        }
    </style>
</head>
<body>

<div class="announcement-bar">
    <span class="announcement-icon">🔊</span>
    <div class="announcement-text">
        <span id="announcement"></span>
    </div>
    
</div>

<script>
   const messages = [
    "🎰 Welcome to 91 Win! Unlock thrilling games & massive rewards! 🚀",
    "⚠️ Important! Stay safe – only trust official 91 Win support. 🔒",
    "🎁 Daily Bonuses Await! Spin, win, and claim your exclusive rewards now! 🎉",
    "🛡️ Protect Your Winnings! Never share your account details. Stay secure! ✅",
    "💸 Instant Withdrawals are LIVE! Cash out your winnings in seconds! 💰🔥"
];


    let currentIndex = 0;
    const announcementElement = document.getElementById("announcement");

    function changeAnnouncement() {
        announcementElement.style.transform = "translateY(-20px)"; 
        setTimeout(() => {
            announcementElement.textContent = messages[currentIndex];
            announcementElement.style.transform = "translateY(0px)";
        }, 500);

        currentIndex = (currentIndex + 1) % messages.length;
    }

    // Initialize first message
    announcementElement.textContent = messages[0];

    // Change message every 2 seconds
    setInterval(changeAnnouncement, 2000);
</script>

</body>
</html>

  

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fishing Games - 91Win</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        body {
            background-color: #F8F9FA;
        }
        .section-title {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: bold;
            padding: 10px;
            border-bottom: 2px solid #E5E7EB;
        }
        .section-title h2 {
            font-size: 20px;
            color: #333;
            display: flex;
            align-items: center;
        }
        .section-title h2::before {
            content: "⦁";
            color: red;
            font-size: 18px;
            margin-right: 8px;
        }
        .all-games {
            font-size: 14px;
            color: #666;
            text-decoration: none;
            border: 1px solid #ddd;
            padding: 4px 10px;
            border-radius: 12px;
            transition: all 0.3s ease;
        }
        .all-games:hover {
            background-color: #ddd;
        }
        .cards-container {
            display: grid;
            grid-template-columns: repeat(3, 1fr); /* 3 Cards per row */
            gap: 15px;
            padding: 20px;
        }
        .card {
            border-radius: 10px;
            overflow: hidden;
            transition: transform 0.3s ease-in-out;
            text-align: center;
        }
        .card:hover {
            transform: scale(1.05);
        }
        .card img {
            width: 100%;
            height: 160px;
            object-fit: cover;
            border-radius: 5px;
        }
        
        /* Ensure 3 Columns on All Screens */
        @media (max-width: 900px) {
            .cards-container {
                grid-template-columns: repeat(3, 1fr); /* 3 per row */
            }
        }
        @media (max-width: 600px) {
            .cards-container {
                grid-template-columns: repeat(3, 1fr); /* 3 per row */
            }
        }
    </style>
</head>
<body>

    <!-- Fishing Section -->
    <div class="container py-4">
        <div class="section-title">
            <h2>Fishing</h2>
            <a href="#" class="all-games">All 21 ➝</a>
        </div>

        <!-- Cards Grid -->
        <div class="cards-container">
            <div class="card">
                <a href="color_bet.php">
                    <img src="IMAGE/MINES2.png" alt="Paradise">
                </a>
            </div>
            <div class="card">
                <a href="luckynumber.php">
                    <img src="IMAGE/MINES2.png" alt="OneShot Fishing">
                </a>
            </div>
            <div class="card">
                <a href="pick_door.php">
                    <img src="IMAGE/MINES2.png" alt="Lucky Fishing">
                </a>
            </div>
            <div class="card">
                <a href="coin_flip.php">
                    <img src="IMAGE/MINES2.png" alt="Hero Fishing">
                </a>
            </div>
            <div class="card">
                <a href="three_chests.php">
                    <img src="IMAGE/MINES2.png" alt="Royal Fishing">
                </a>
            </div>
            <div class="card">
                <a href="dualdice.php">
                    <img src="IMAGE/MINES2.png" alt="All Star Fishing">
                </a>
            </div>
        </div>
    </div>





     <!-- Fishing Section -->
     <div class="container py-4">
        <div class="section-title">
            <h2>Fishing</h2>
            <a href="#" class="all-games">All 21 ➝</a>
        </div>

        <!-- Cards Grid -->
        <div class="cards-container">
            <div class="card">
                <a href="paradise.php">
                    <img src="IMAGE/MINES2.png" alt="Paradise">
                </a>
            </div>
            <div class="card">
                <a href="oneshot.php">
                    <img src="IMAGE/MINES2.png" alt="OneShot Fishing">
                </a>
            </div>
            <div class="card">
                <a href="lucky.php">
                    <img src="IMAGE/MINES2.png" alt="Lucky Fishing">
                </a>
            </div>
            <div class="card">
                <a href="herofishing.php">
                    <img src="IMAGE/MINES2.png" alt="Hero Fishing">
                </a>
            </div>
            <div class="card">
                <a href="royal.php">
                    <img src="IMAGE/MINES2.png" alt="Royal Fishing">
                </a>
            </div>
            <div class="card">
                <a href="allstar.php">
                    <img src="IMAGE/MINES2.png" alt="All Star Fishing">
                </a>
            </div>
        </div>
    </div>



     <!-- Fishing Section -->
     <div class="container py-4">
        <div class="section-title">
            <h2>Fishing</h2>
            <a href="#" class="all-games">All 21 ➝</a>
        </div>

        <!-- Cards Grid -->
        <div class="cards-container">
            <div class="card">
                <a href="paradise.php">
                    <img src="IMAGE/MINES2.png" alt="Paradise">
                </a>
            </div>
            <div class="card">
                <a href="oneshot.php">
                    <img src="IMAGE/MINES2.png" alt="OneShot Fishing">
                </a>
            </div>
            <div class="card">
                <a href="lucky.php">
                    <img src="IMAGE/MINES2.png" alt="Lucky Fishing">
                </a>
            </div>
            <div class="card">
                <a href="herofishing.php">
                    <img src="IMAGE/MINES2.png" alt="Hero Fishing">
                </a>
            </div>
            <div class="card">
                <a href="royal.php">
                    <img src="IMAGE/MINES2.png" alt="Royal Fishing">
                </a>
            </div>
            <div class="card">
                <a href="allstar.php">
                    <img src="IMAGE/MINES2.png" alt="All Star Fishing">
                </a>
            </div>
        </div>
    </div>



    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>



 
<br>
<br>
 



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Winning Information</title>
    <style>
        /* Winning Information Container */
        .winning-container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            max-width: 600px;
            margin: auto;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            font-family: Arial, sans-serif;
        }

        /* Header */
        .winning-header {
            font-size: 18px;
            color: #333;
            border-left: 4px solid red;
            padding-left: 10px;
            margin-bottom: 15px;
            font-weight: bold;
        }

        /* Winning List */
        .winning-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        /* Winning Item */
        .winning-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #f9f9f9;
            padding: 10px;
            border-radius: 10px;
            box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1);
        }

        .winner-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .winner-info img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
        }

        .winner-name {
            font-weight: bold;
            color: #333;
        }

        /* Winning Amount */
        .winning-amount {
            text-align: right;
        }

        .winning-amount p {
            margin: 0;
            font-weight: bold;
            color: #ff6b6b;
        }

        .winning-amount span {
            font-size: 12px;
            color: #777;
        }

        /* Winning Image */
        .winning-img {
            width: 60px;
            height: 40px;
            border-radius: 5px;
        }
    </style>
</head>
<body>

<div class="winning-container">
    <h2 class="winning-header">🏆 Winning Information</h2>
    
    <div class="winning-list" id="winningList">
        <!-- Winning items will be inserted dynamically -->
    </div>
</div>

<script>
    // List of unique Indian names with amounts and images
    const winners = [
        { name: "Rajesh***K", amount: "₹2,500.00", image: "game1.jpg" },
        { name: "Anjali***M", amount: "₹3,750.00", image: "game2.jpg" },
        { name: "Vikram***P", amount: "₹1,200.00", image: "game3.jpg" },
        { name: "Neha***S", amount: "₹950.00", image: "game4.jpg" },
        { name: "Amit***T", amount: "₹5,000.00", image: "game5.jpg" },
        { name: "Priya***R", amount: "₹4,100.00", image: "game6.jpg" },
        { name: "Arjun***V", amount: "₹2,700.00", image: "game7.jpg" },
        { name: "Sneha***D", amount: "₹1,800.00", image: "game8.jpg" },
        { name: "Rahul***J", amount: "₹3,200.00", image: "game9.jpg" },
        { name: "Kavita***L", amount: "₹2,050.00", image: "game10.jpg" }
    ];

    let usedIndexes = new Set(); // Track used indexes to prevent repetition
    const winningList = document.getElementById("winningList");

    function updateWinners() {
        winningList.innerHTML = ""; // Clear previous list

        let availableIndexes = [...Array(winners.length).keys()].filter(i => !usedIndexes.has(i));

        if (availableIndexes.length < 5) {
            usedIndexes.clear(); // Reset when all names are used
            availableIndexes = [...Array(winners.length).keys()];
        }

        // Randomly pick 5 unique winners from available indexes
        let selectedIndexes = [];
        while (selectedIndexes.length < 5) {
            const randomIndex = availableIndexes[Math.floor(Math.random() * availableIndexes.length)];
            if (!selectedIndexes.includes(randomIndex)) {
                selectedIndexes.push(randomIndex);
                usedIndexes.add(randomIndex);
            }
        }

        selectedIndexes.forEach(index => {
            const winner = winners[index];
            const winningItem = document.createElement("div");
            winningItem.classList.add("winning-item");
            winningItem.innerHTML = `
                <div class="winner-info">
                    <img src="profile.jpg" alt="User">
                    <span class="winner-name">${winner.name}</span>
                </div>
                <img src="${winner.image}" class="winning-img" alt="Winning Game">
                <div class="winning-amount">
                    <p>Receive ${winner.amount}</p>
                    <span>Winning amount</span>
                </div>
            `;
            winningList.appendChild(winningItem);
        });
    }

    // Update winners every 3 seconds
    updateWinners();
    setInterval(updateWinners, 3000);
</script>

</body>
</html>








  


  <br>
  <br>
  <br>
  <br>

<section>
  <!-- Fixed Footer Navigation -->
<footer class="fixed-bottom bg-white border-top">
  <div class="container-fluid">
    <div class="row row-cols-5 text-center m-0 w-100">
      <!-- Promotion -->
      <div class="col">
        <a href="news.php" class="d-block py-2 text-decoration-none text-dark" title="Promotion">
          <i class="fa-solid fa-fire fs-4"></i>
        </a>
      </div>
      <!-- Home -->
      <div class="col">
        <a href="#" class="d-block py-2 text-decoration-none text-dark" title="Home">
          <i class="fa-solid fa-house-user fs-4"></i>
        </a>
      </div>
      <!-- Wallet -->
      <div class="col">
        <a href="wallet.php" class="d-block py-2 text-decoration-none text-dark" title="Wallet">
          <i class="fa-solid fa-wallet fs-4"></i>
        </a>
      </div>
      <!-- Account -->
      <div class="col">
        <a href="profile.php" class="d-block py-2 text-decoration-none text-dark" title="Account">
          <i class="fa-solid fa-user fs-4"></i>
        </a>
      </div>
      <!-- Activity -->
      <div class="col">
        <a href="activity.php" class="d-block py-2 text-decoration-none text-dark" title="Activity">
          <i class="fa-solid fa-couch fs-4"></i>
        </a>
      </div>
    </div>
  </div>
</footer>
</section>

    
<!--earnings-chart-container-->

<div class="earnings-chart-container">
    <h2>📈 Today's earnings chart</h2>

    

    <!-- Top Earner -->
    <div class="top-earner">
        <div class="top-earner-profile">
            <img src="image/1P.png" alt="Top Earner" class="top-earner-img" />
            <div class="crown">👑</div>
        </div>
        <div class="top-earner-info">
            <div class="top-earner-name">Anjli434</div>
            <div class="top-earner-amount">₹1,359,919.38</div>
        </div>
    </div>

    <!-- Earnings Table -->
    <table class="earnings-table">
        <tbody>
            <tr>
                <td class="rank">2</td>
                <td class="user">
                    <img src="image/2P.png" alt="Profile" class="profile-img">
                    Misha65
                </td>
                <td class="amount">₹752,900.00</td>
            </tr>
            <tr>
                <td class="rank">3</td>
                <td class="user">
                    <img src="image/3P.png" alt="Profile" class="profile-img">
                    Queen635
                </td>
                <td class="amount">₹650,750.00</td>
            </tr>
            <tr>
                <td class="rank">4</td>
                <td class="user">
                    <img src="image/5P.png" alt="Profile" class="profile-img">
                    Reyaj687
                </td>
                <td class="amount">₹609,109.20</td>
            </tr>
            <tr>
                <td class="rank">5</td>
                <td class="user">
                    <img src="image/7P.png" alt="Profile" class="profile-img">
                    Pryan9659
                </td>
                <td class="amount">₹425,362.34</td>
            </tr>
            <tr>
                <td class="rank">6</td>
                <td class="user">
                    <img src="image/4P.png" alt="Profile" class="profile-img">
                    Prince989
                </td>
                <td class="amount">₹398,900.00</td>
            </tr>
        </tbody>
    </table>
</div>
<br>
<br>



<style>/* General Container */
.earnings-chart-container {
    background: white;
    border-radius: 10px;
    padding: 20px;
    max-width: 400px;
    margin: auto;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

/* Header */
h2 {
    font-size: 18px;
    color: #333;
    margin-bottom: 15px;
}

/* Tabs */
.tabs {
    display: flex;
    justify-content: space-around;
    background: #f0f0f0;
    padding: 10px;
    border-radius: 10px;
    margin-bottom: 20px;
}

.tab {
    padding: 8px 12px;
    border-radius: 6px;
    cursor: pointer;
    font-weight: bold;
    color: #666;
}

.tab.active {
    background: #ff6b6b;
    color: white;
}

/* Top Earner */
.top-earner {
    display: flex;
    align-items: center;
    background: #ffe0e0;
    padding: 10px;
    border-radius: 10px;
    margin-bottom: 15px;
    position: relative;
}

.top-earner-profile {
    position: relative;
    margin-right: 15px;
}

.top-earner-img {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    border: 3px solid gold;
}

.crown {
    position: absolute;
    top: -5px;
    right: -5px;
    font-size: 16px;
}

/* Earnings Table */
.earnings-table {
    width: 100%;
    border-collapse: collapse;
}

.earnings-table tr {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #fff;
    padding: 10px;
    margin-bottom: 5px;
    border-radius: 8px;
    box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1);
}

.earnings-table td {
    padding: 10px;
}

.rank {
    font-size: 16px;
    font-weight: bold;
    color: #333;
}

.user {
    display: flex;
    align-items: center;
    font-weight: bold;
    color: #333;
}

.profile-img {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    margin-right: 10px;
}

.amount {
    font-weight: bold;
    color: #ff6b6b;
}
</style>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>91 Win</title>
    <style>
        /* Container */
        .game-info-container {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 10px;
            max-width: 500px;
            margin: auto;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            font-family: Arial, sans-serif;
            text-align: center;
        }

        /* Header */
        .game-header {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            font-size: 22px;
            font-weight: bold;
            color: red;
        }

        .game-header img {
            width: 120px;
        }

        .age-restriction {
            background: red;
            color: white;
            font-size: 14px;
            padding: 5px 10px;
            border-radius: 50%;
        }

        /* Game Providers */
        .game-providers {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-top: 15px;
        }

        .provider-logo {
            background: white;
            padding: 10px;
            border-radius: 8px;
            box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1);
        }

        .provider-logo img {
            width: 80px;
        }

        /* Game Info */
        .game-info {
            margin-top: 15px;
            text-align: left;
            font-size: 14px;
            color: #333;
        }

        .game-info p {
            margin: 8px 0;
        }

        .game-info p span {
            color: red;
            font-weight: bold;
        }

        /* Disclaimer */
        .disclaimer {
            color: red;
            font-size: 12px;
            margin-top: 15px;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="game-info-container">
    <!-- Header -->
    <div class="game-header">
        <img src="logo.png" alt="">
        91 Win
        <span class="age-restriction">+18</span>
    </div>

    <!-- Game Providers -->
   

    <!-- Game Information -->
    <div class="game-info">
        <p>🔹 The platform advocates fairness, justice, and openness. We mainly operate fair lottery, blockchain games, live casinos, and slot machine games.</p>
        <p>🔹 Welcome to <span>91 Win</span>, which works with more than 10,000 online live game dealers and slot games, all of which are verified fair games.</p>
        <p>🔹 <span>91 Win</span> supports fast deposit and withdrawal and looks forward to your visit.</p>
    </div>

    <!-- Disclaimer -->
    <p class="disclaimer">⚠ Gambling can be addictive, please play rationally.</p>
    <p class="disclaimer">⚠ <span>91 Win</span> only accepts customers above the age of 18.</p>
</div>

</body>
</html>

<br><br>
<br>


</body>
</html>

  


