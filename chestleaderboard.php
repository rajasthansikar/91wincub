<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Fake Leaderboard - Three Chests</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body { background-color: #fdf5df; font-family: Arial, sans-serif; text-align: center; }
        .container { max-width: 400px; margin: 30px auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2); }
        
        .leaderboard {
            background: #ffcc00;
            padding: 10px;
            border-radius: 10px;
        }
        
        .leaderboard h2 { color: #353535; }
        .leaderboard table { width: 100%; margin-top: 10px; }
        .leaderboard th, .leaderboard td {
            padding: 10px;
            text-align: left;
            border-bottom: 2px solid #fff;
        }
        
        .leaderboard tr:nth-child(odd) { background: #fff6d5; }
        .leaderboard tr:nth-child(even) { background: #ffe08a; }

        .btn-back { background: #f92c85; color: white; font-size: 16px; padding: 10px; width: 100%; margin-top: 15px; border-radius: 5px; border: none; cursor: pointer; }
        .btn-back:hover { background: #e02070; }
    </style>
</head>
<body>

<div class="container">
    <h2>🏆 Leaderboard</h2>
    <p>🔥 Top 10 Players with Highest Winnings</p>

    <div class="leaderboard">
        <h2>🔥 Top Winners</h2>
        <table>
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>Player</th>
                    <th>Total Winnings</th>
                </tr>
            </thead>
            <tbody id="leaderboard-body">
                <!-- Fake entries will be injected here -->
            </tbody>
        </table>
    </div>

    <button class="btn-back" onclick="window.location.href='three_chests.php'">🔙 Back to Game</button>
</div>

<script>
    const fakePlayers = [
        "Rahul_91", "Swayam_007", "Neha_X", "LuckyKing", "Pardeep98",
        "Rohit_K", "MeghaQueen", "Arjun_OP", "Payal_M", "SharmaX",
        "Deepak_11", "NitinBoss", "RajeshLucky", "Vikas999", "PreetiS",
        "Sakshi_777", "ManojGuru", "DivyaStar", "Varun_GG", "AnjaliWins"
    ];

    function getRandomWinnings() {
        return (Math.random() * 5000 + 1000).toFixed(2); // Random between ₹1000 - ₹6000
    }

    function shuffleLeaderboard() {
        let shuffledPlayers = fakePlayers.sort(() => 0.5 - Math.random()).slice(0, 10);
        let leaderboardHTML = "";

        shuffledPlayers.forEach((player, index) => {
            let winnings = getRandomWinnings();
            leaderboardHTML += `
                <tr>
                    <td>#${index + 1}</td>
                    <td>${player}</td>
                    <td>₹${winnings}</td>
                </tr>
            `;
        });

        document.getElementById("leaderboard-body").innerHTML = leaderboardHTML;
    }

    // Shuffle leaderboard every 5 seconds
    setInterval(shuffleLeaderboard, 5000);
    
    // Load first leaderboard instantly
    shuffleLeaderboard();
</script>

</body>
</html>
