<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Page</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .activity-header {
            background: linear-gradient(to right, #ff5e62, #ff9966);
            color: white;
            padding: 15px;
            text-align: center;
            font-weight: bold;
            font-size: 20px;
        }
        .activity-text {
            text-align: center;
            color: white;
            font-size: 14px;
        }
        .icons-section {
            display: flex;
            justify-content: space-around;
            padding: 15px;
            background: white;
            border-radius: 10px;
            margin: 10px;
        }
        .icon-box {
            text-align: center;
        }
        .icon-box img {
            width: 50px;
            height: 50px;
        }
        .card-custom {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 10px;
        }
        .card-custom img {
            width: 100%;
            height: auto;
            border-radius: 10px;
        }
        .footer {
            background: linear-gradient(to right, #ff5e62, #ff9966);
            color: white;
            text-align: center;
            padding: 10px;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <!-- Activity Header -->
    <div class="activity-header">
        Activity
        <p class="activity-text">Please remember to follow the event page. We will launch user feedback activities from time to time.</p>
    </div>

    <!-- Icons Section -->
    <div class="container">
        <div class="icons-section">
            <div class="icon-box">
                <img src="icons/invite.png" alt="Invitation Bonus">
                <p>Invitation bonus</p>
            </div>
            <div class="icon-box">
                <img src="icons/bet.png" alt="Betting Rebate">
                <p>Betting rebate</p>
            </div>
            <div class="icon-box">
                <img src="icons/jackpot.png" alt="Super Jackpot">
                <p>Super Jackpot</p>
            </div>
            <div class="icon-box">
                <img src="icons/gift.png" alt="New Member Gift">
                <p>New member gift</p>
            </div>
        </div>

        <!-- Main Content Section -->
        <div class="row">
            <div class="col-md-4">
                <div class="card card-custom">
                    <img src="images/gift.jpg" alt="Gift">
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-custom">
                    <img src="images/attendance.jpg" alt="Attendance Bonus">
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-custom">
                    <img src="images/become-agent.jpg" alt="Become Agent">
                </div>
            </div>

            <div class="col-md-4">
                <div class="card card-custom">
                    <img src="images/member-activities.jpg" alt="Member Activities">
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-custom">
                    <img src="images/aviator-betting.jpg" alt="Aviator Betting Award">
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-custom">
                    <img src="images/lucky-10-days.jpg" alt="Lucky 10 Days">
                </div>
            </div>

            <div class="col-md-4">
                <div class="card card-custom">
                    <img src="images/youtube-content.jpg" alt="Youtube Creative Video">
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-custom">
                    <img src="images/mysterious-gift.jpg" alt="Mysterious Gift">
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-custom">
                    <img src="images/reward-bonus.jpg" alt="Reward Bonus">
                </div>
            </div>
        </div>
    </div>
<br>
<br>
<br>

    <!-- Footer -->
    <div class="footer">
        91 Win
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <section>
  <!-- Fixed Footer Navigation -->
<footer class="fixed-bottom bg-white border-top">
  <div class="container-fluid">
    <div class="row row-cols-5 text-center m-0 w-100">
      <!-- Promotion -->
      <div class="col">
        <a href="#" class="d-block py-2 text-decoration-none text-dark" title="Promotion">
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
        <a href="#" class="d-block py-2 text-decoration-none text-dark" title="Wallet">
          <i class="fa-solid fa-wallet fs-4"></i>
        </a>
      </div>
      <!-- Account -->
      <div class="col">
        <a href="#" class="d-block py-2 text-decoration-none text-dark" title="Account">
          <i class="fa-solid fa-user fs-4"></i>
        </a>
      </div>
      <!-- Activity -->
      <div class="col">
        <a href="#" class="d-block py-2 text-decoration-none text-dark" title="Activity">
          <i class="fa-solid fa-couch fs-4"></i>
        </a>
      </div>
    </div>
  </div>
</footer>
</section>

</body>
</html>
