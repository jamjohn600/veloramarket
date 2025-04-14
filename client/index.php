<?php
require_once '../model/my_fns.php';

// Check login status
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: ../signin.php');
    exit;
}

// Fetch user details
$username = $_SESSION['username'];
$userDetails = fetch_user_details($username);

if (!$userDetails) {
    header('Location: ../error.php?message=User details not found.');
    exit;
}

// Fetch wallet and trade data
$walletDetails = fetch_wallet_details($username);
$walletBalance = fetch_wallet_balance($username);
$deposit = fetch_tradeDeposits($username);

// Avatar handling
$avatar = $userDetails['avatar'] ?? '';
$avatarPath = !empty($avatar) ? "../Uploads/" . htmlspecialchars($avatar) : "../assets/img/avatar.svg";

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>VeloraMarket Dashboard</title>
    <link rel="shortcut icon" href="../images/logo.jfif" type="image/x-icon">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        /* [Inline CSS unchanged from your submission] */
        .market-order-header {
            display: flex;
            justify-content: space-between;
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            padding: 10px;
            font-weight: bold;
            text-align: center;
        }
        .market-order-header li { flex: 1; text-align: center; }
        .market-order-item {
            display: flex;
            justify-content: space-between;
            border-bottom: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }
        .market-order-item li { flex: 1; text-align: center; word-wrap: break-word; }
        thead tr { pointer-events: none; }
        .no-data { text-align: center; padding: 20px; font-style: italic; color: #888; }
        @media (max-width: 768px) {
            .market-order-header li, .market-order-item li { font-size: 14px; }
        }
        .scrollable-content {
            max-height: 300px;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 10px;
        }
        .scrollable-content ul {
            display: flex;
            justify-content: space-between;
            padding: 8px 10px;
            border-bottom: 1px solid gray;
        }
        .scrollable-content ul.no-data { justify-content: center; text-align: center; font-style: italic; }
        .scrollable-content::-webkit-scrollbar { width: 8px; }
        .scrollable-content::-webkit-scrollbar-thumb { background: #ced4da; border-radius: 4px; }
        .scrollable-content::-webkit-scrollbar-thumb:hover { background: #adb5bd; }
    </style>
</head>
<body id="dark">
    <header class="dark-bb">
        <?php include 'index-navbar.php'; ?>
    </header>
    <div class="container-fluid mtb15 no-fluid">
        <div class="container-fluid">
            <div class="alert bg-warning justify-content-between align-items-center <?php echo ($userDetails['transaction_status'] === 'ready' && $userDetails['email_verified'] == 1) ? 'd-none' : 'd-flex'; ?>">
                <p class="mt-4">Hi, <b><?php echo htmlspecialchars($userDetails['name']); ?></b> to get you started for a wonderful trading experience please verify your account.</p>
                <button class="btn btn-success"><a href="../profile" class="text-decoration-none text-white">Verify!</a></button>
            </div>
        </div>
        <div class="row sm-gutters">
            <div class="col-md-12">
                <div class="markets ptb70">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="markets-pair-list">
                                    <ul class="nav nav-pills" id="pills-tab" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active currency-link" data-toggle="pill" href="#major" role="tab" aria-selected="true">Major Pairs</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link currency-link" data-toggle="pill" href="#minor" role="tab" aria-selected="true">Minor Pairs</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link currency-link" data-toggle="pill" href="#crypto" role="tab" aria-selected="true">Cryptocurrencies</a>
                                        </li>
                                    </ul>
                                    <div class="tab-content">
                                        <div class="tab-pane fade show active" id="major" role="tabpanel">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th>Pair</th>
                                                        <th>Trend</th>
                                                        <th>Price</th>
                                                        <th>High (24H)</th>
                                                        <th>Low (24H)</th>
                                                        <th>Volume (24H)</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="major-pairs"></tbody>
                                            </table>
                                        </div>
                                        <div class="tab-pane fade" id="minor" role="tabpanel">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th>Pair</th>
                                                        <th>Trend</th>
                                                        <th>Price</th>
                                                        <th>High (24H)</th>
                                                        <th>Low (24H)</th>
                                                        <th>Volume (24H)</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="minor-pairs"></tbody>
                                            </table>
                                        </div>
                                        <div class="tab-pane fade" id="crypto" role="tabpanel">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th>Pair</th>
                                                        <th>Trend</th>
                                                        <th>Price</th>
                                                        <th>High (24H)</th>
                                                        <th>Low (24H)</th>
                                                        <th>Volume (24H)</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="crypto-pairs"></tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="text-center">
                                        <button id="refresh-button" class="load-more btn">Refresh <i class="icon ion-md-refresh"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="market-news mt15">
                            <h2 class="heading">Market News</h2>
                            <ul>
                                <?php
                                $apiKey = "2fb528bbad5f4edca8db07094b6ba6e0";
                                $apiUrl = "https://newsapi.org/v2/everything?q=forex&sortBy=publishedAt&language=en&apiKey=$apiKey";
                                $ch = curl_init();
                                curl_setopt($ch, CURLOPT_URL, $apiUrl);
                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                curl_setopt($ch, CURLOPT_HTTPHEADER, ['User-Agent: veloramarket.com']);
                                $response = curl_exec($ch);
                                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                                curl_close($ch);
                                if ($httpCode == 200) {
                                    $newsData = json_decode($response, true);
                                    if (is_array($newsData['articles']) && !empty($newsData['articles'])) {
                                        foreach (array_slice($newsData['articles'], 0, 5) as $newsItem) {
                                            $newsTitle = htmlspecialchars($newsItem['title']);
                                            $newsDescription = htmlspecialchars(substr($newsItem['description'] ?? '', 0, 100)) . "...";
                                            $newsDate = htmlspecialchars($newsItem['publishedAt']);
                                            $newsUrl = htmlspecialchars($newsItem['url']);
                                            echo "<li><a href='$newsUrl' target='_blank'>
                                                    <strong>$newsTitle</strong><br>
                                                    $newsDescription<br>
                                                    <span>$newsDate</span>
                                                  </a></li>";
                                        }
                                    } else {
                                        echo "<li>No Forex news available at the moment.</li>";
                                    }
                                } else {
                                    echo "<li>Failed to fetch news (Error $httpCode).</li>";
                                }
                                ?>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="market-history market-order mt15">
                            <ul class="nav nav-pills" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" data-toggle="pill" href="#order-history" role="tab" aria-selected="true">Order History</a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="order-history" role="tabpanel">
                                    <ul class="market-order-header">
                                        <li>Symbol</li>
                                        <li>Position ID</li>
                                        <li>Order Type</li>
                                        <li>Duration</li>
                                        <li>Result</li>
                                        <li>Amount</li>
                                    </ul>
                                    <div id="trade-history-body" class="scrollable-content">
                                        <ul class="no-data">
                                            <li colspan="6" class="text-center">
                                                <i class="icon ion-md-document"></i> No data
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="../assets/js/jquery-3.4.1.min.js"></script>
    <script src="../assets/js/popper.min.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script src="../assets/js/amcharts-core.min.js"></script>
    <script src="../assets/js/amcharts.min.js"></script>
    <script src="../assets/js/jquery.mCustomScrollbar.js"></script>
    <script src="../assets/js/custom.js"></script>
    <script>
        $('tbody, .market-news ul').mCustomScrollbar({ theme: 'minimal' });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            fetchTradeHistory('<?php echo htmlspecialchars($username); ?>');
        });
        function fetchTradeHistory(username) {
            const endpoint = `../trade/fetchTrades.php?_=${Date.now()}`;
            const xhr = new XMLHttpRequest();
            xhr.open('POST', endpoint, true);
            xhr.setRequestHeader('Content-Type', 'application/json;charset=UTF-8');
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4) {
                    const tradeHistoryBody = document.querySelector('#trade-history-body');
                    const noDataElement = tradeHistoryBody.querySelector('.no-data');
                    if (xhr.status === 200) {
                        try {
                            const response = JSON.parse(xhr.responseText);
                            tradeHistoryBody.querySelectorAll('.market-order-item').forEach(el => el.remove());
                            if (response.status === 'success' && response.data.length > 0) {
                                noDataElement.style.display = 'none';
                                response.data.forEach(trade => {
                                    const tradeItem = document.createElement('ul');
                                    tradeItem.className = 'market-order-item';
                                    tradeItem.innerHTML = `
                                        <li>${trade.symbol}</li>
                                        <li>${trade.position_id}</li>
                                        <li>${trade.order_type}</li>
                                        <li>${trade.duration}</li>
                                        <li>${trade.result}</li>
                                        <li>${trade.amount}</li>
                                    `;
                                    tradeHistoryBody.appendChild(tradeItem);
                                });
                            } else {
                                noDataElement.style.display = 'block';
                            }
                        } catch (e) {
                            console.error('Error parsing trade history response:', e);
                        }
                    } else {
                        console.error('Failed to fetch trade history:', xhr.status, xhr.responseText);
                    }
                }
            };
            xhr.send(JSON.stringify({ username }));
        }
    </script>
    <script>
        const socket = new WebSocket('wss://ws.binaryws.com/websockets/v3?app_id=64600');
        const majorPairs = ['frxAUDUSD', 'frxNZDUSD', 'frxUSDJPY', 'frxEURAUD', 'frxEURCAD', 'frxEURGBP', 'frxEURJPY', 'frxEURUSD', 'frxGBPJPY', 'frxGBPUSD'];
        const minorPairs = ['frxUSDMXN', 'frxUSDNOK', 'frxUSDPLN', 'frxUSDSEK', 'frxAUDNZD', 'frxEURNZD', 'frxEURCHF', 'frxGBPNZD', 'frxGBPCHF', 'frxAUDCHF'];
        const cryptocurrencies = ['cryBTCUSD', 'cryETHUSD'];
        let currentTab = 'major';
        const activeSubscriptions = new Set();
        const pairData = {};
        const noDataTimeouts = {};
        function showNoDataMessage(tabId, message) {
            const tableBody = document.getElementById(`${tabId}-pairs`);
            tableBody.innerHTML = `<tr id="${tabId}-no-data"><td colspan="6" style="text-align: center; font-style: italic;">${message}</td></tr>`;
        }
        function removeNoDataMessage(tabId) {
            const noDataMessageRow = document.getElementById(`${tabId}-no-data`);
            if (noDataMessageRow) noDataMessageRow.remove();
        }
        function updateRow(pair, trend, price, high, low, volume) {
            const tableBody = document.getElementById(`${currentTab}-pairs`);
            let row = document.getElementById(`${currentTab}-${pair}`);
            const displayPair = pair.replace(/^(frx|cry)/, '');
            if (!row) {
                row = document.createElement('tr');
                row.id = `${currentTab}-${pair}`;
                row.addEventListener('click', () => {
                    window.location.href = `../trade/index.php?pair=${displayPair}`;
                });
                row.innerHTML = `
                    <td>${displayPair}</td>
                    <td class="trend"></td>
                    <td class="price"></td>
                    <td class="high"></td>
                    <td class="low"></td>
                    <td class="volume"></td>
                `;
                tableBody.appendChild(row);
            }
            updateCell(row.querySelector('.trend'), trend, trend > 0 ? '↑' : '↓');
            updateCell(row.querySelector('.price'), price);
            updateCell(row.querySelector('.high'), high.toFixed(5));
            updateCell(row.querySelector('.low'), low.toFixed(5));
            updateCell(row.querySelector('.volume'), volume);
            removeNoDataMessage(currentTab);
            clearTimeout(noDataTimeouts[currentTab]);
        }
        function updateCell(cell, newValue, displayValue = newValue) {
            const oldValue = parseFloat(cell.textContent) || 0;
            cell.textContent = displayValue;
            if (newValue > oldValue) {
                cell.style.color = 'green';
            } else if (newValue < oldValue) {
                cell.style.color = 'red';
            } else {
                cell.style.color = 'white';
            }
        }
        function fetchPairs(pairs, tabId) {
            if (noDataTimeouts[tabId]) clearTimeout(noDataTimeouts[tabId]);
            activeSubscriptions.forEach(symbol => socket.send(JSON.stringify({ forget: symbol })));
            activeSubscriptions.clear();
            document.getElementById(`${tabId}-pairs`).innerHTML = '';
            removeNoDataMessage(tabId);
            pairs.forEach(pair => {
                socket.send(JSON.stringify({ ticks: pair, subscribe: 1 }));
                activeSubscriptions.add(pair);
                pairData[pair] = { high: -Infinity, low: Infinity, volume: Math.floor(Math.random() * 1000) };
            });
            noDataTimeouts[tabId] = setTimeout(() => {
                showNoDataMessage(tabId, 'No market data available. This could be due to weekends, holidays, or other technical issues.');
            }, 3000);
        }
        socket.onopen = function () {
            const initialPairs = currentTab === 'major' ? majorPairs : currentTab === 'minor' ? minorPairs : cryptocurrencies;
            fetchPairs(initialPairs, currentTab);
        };
        socket.onmessage = function (event) {
            const data = JSON.parse(event.data);
            if (data.tick) {
                const { symbol, quote } = data.tick;
                pairData[symbol].price = quote;
                pairData[symbol].high = Math.max(pairData[symbol].high, quote);
                pairData[symbol].low = Math.min(pairData[symbol].low, quote);
                pairData[symbol].volume += Math.floor(Math.random() * 100);
                const isMajor = majorPairs.includes(symbol);
                const isMinor = minorPairs.includes(symbol);
                const isCrypto = cryptocurrencies.includes(symbol);
                if ((isMajor && currentTab === 'major') || (isMinor && currentTab === 'minor') || (isCrypto && currentTab === 'crypto')) {
                    updateRow(symbol, Math.random() > 0.5 ? 1 : -1, pairData[symbol].price, pairData[symbol].high, pairData[symbol].low, pairData[symbol].volume);
                }
            }
        };
        document.querySelectorAll('.currency-link').forEach(tab => {
            tab.addEventListener('click', function (event) {
                event.preventDefault();
                currentTab = this.getAttribute('href').substring(1);
                const pairs = currentTab === 'major' ? majorPairs : currentTab === 'minor' ? minorPairs : cryptocurrencies;
                fetchPairs(pairs, currentTab);
            });
        });
        document.getElementById('refresh-button').addEventListener('click', () => {
            const pairs = currentTab === 'major' ? majorPairs : currentTab === 'minor' ? minorPairs : cryptocurrencies;
            fetchPairs(pairs, currentTab);
        });
    </script>
</body>
</html>