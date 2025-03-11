<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daman Game UI</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f8f8;
            text-align: center;
            padding: 10px;
        }

        .container {
            width: 90%;
            max-width: 400px;
            margin: auto;
            background: linear-gradient(to bottom, #ff7b7b, #ffb4b4);
            border-radius: 10px;
            padding: 20px;
            color: white;
        }

        /* Wallet */
        .wallet h2 {
            margin: 0;
            font-size: 24px;
        }

        .wallet p {
            margin: 5px 0;
            font-size: 14px;
        }

        .buttons {
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        .withdraw {
            background: #ff4d4d;
            border: none;
            padding: 10px;
            border-radius: 5px;
            color: white;
            cursor: pointer;
        }

        .deposit {
            background: #00b300;
            border: none;
            padding: 10px;
            border-radius: 5px;
            color: white;
            cursor: pointer;
        }

        /* Game Modes */
        .game-modes {
            display: flex;
            justify-content: space-around;
            margin: 15px 0;
        }

        .game-modes button {
            background: gray;
            padding: 10px;
            border: none;
            border-radius: 5px;
            color: white;
        }

        .game-modes .active {
            background: red;
        }

        /* Timer & Numbers */
        .game-info {
            margin-top: 15px;
        }

        .how-to-play {
            background: orange;
            border: none;
            padding: 8px;
            border-radius: 5px;
            color: white;
            margin-bottom: 10px;
        }

        .timer h3 {
            font-size: 20px;
            margin: 5px 0;
        }

        .numbers span {
            display: inline-block;
            font-size: 18px;
            margin: 5px;
            padding: 8px;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            line-height: 40px;
            text-align: center;
        }

        .red {
            background: red;
        }

        .green {
            background: green;
        }

        /* Color Options */
        .color-options {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 15px;
        }

        .color-options button {
            padding: 10px;
            border-radius: 5px;
            color: white;
            border: none;
            cursor: pointer;
        }

        .green-btn {
            background: green;
        }

        .violet-btn {
            background: purple;
        }

        .red-btn {
            background: red;
        }

        /* Number Grid */
        .number-grid {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 15px;
        }

        .circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            line-height: 40px;
            text-align: center;
            color: white;
        }

        /* History Table */
        .history {
            margin-top: 20px;
            background: white;
            color: black;
            padding: 10px;
            border-radius: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 5px;
            text-align: center;
        }

        .red-dot::after {
            content: "🔴";
        }

        .green-dot::after {
            content: "🟢";
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Wallet Section -->
        <div class="wallet">
            <h2>₹0.00</h2>
            <p>Wallet balance</p>
            <div class="buttons">
                <button class="withdraw">Withdraw</button>
                <button class="deposit">Deposit</button>
            </div>
        </div>

        <!-- Game Modes -->
        <div class="game-modes">
            <button class="active">Win Go 30s</button>
            <button>Win Go 1Min</button>
            <button>Win Go 3Min</button>
            <button>Win Go 5Min</button>
        </div>

        <!-- Timer & Numbers -->
        <div class="game-info">
            <button class="how-to-play">How to play</button>
            <div class="timer">
                <span>Time remaining</span>
                <h3 id="countdown">00:30</h3>
            </div>
            <div class="numbers">
                <span class="red">2</span>
                <span class="green">6</span>
                <span class="red">3</span>
                <span class="green">6</span>
                <span class="green">9</span>
            </div>
        </div>

        <!-- Color Selection -->
        <div class="color-options">
            <button class="green-btn">Green</button>
            <button class="violet-btn">Violet</button>
            <button class="red-btn">Red</button>
        </div>

        <!-- Number Grid -->
        <div class="number-grid">
            <div class="circle red">0</div>
            <div class="circle green">1</div>
            <div class="circle red">2</div>
            <div class="circle green">3</div>
            <div class="circle violet">5</div>
            <div class="circle red">6</div>
            <div class="circle red">4</div>
            <div class="circle green">9</div>
        </div>

        <!-- History Table -->
        <div class="history">
            <h3>Game History</h3>
            <table>
                <thead>
                    <tr>
                        <th>Period</th>
                        <th>Number</th>
                        <th>Big/Small</th>
                        <th>Color</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>20250304100052194</td>
                        <td>2</td>
                        <td>Small</td>
                        <td class="red-dot"></td>
                    </tr>
                    <tr>
                        <td>20250304100052193</td>
                        <td>6</td>
                        <td>Big</td>
                        <td class="red-dot"></td>
                    </tr>
                    <tr>
                        <td>20250304100052192</td>
                        <td>3</td>
                        <td>Small</td>
                        <td class="green-dot"></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // Countdown Timer
        let timer = 30;
        const countdownElement = document.getElementById("countdown");

        function startCountdown() {
            const interval = setInterval(() => {
                if (timer <= 0) {
                    clearInterval(interval);
                    countdownElement.textContent = "00:00";
                } else {
                    countdownElement.textContent = `00:${timer < 10 ? "0" : ""}${timer}`;
                    timer--;
                }
            }, 1000);
        }

        startCountdown();
    </script>
</body>
</html>
