<?php
// Set session cookie parameters for security
session_set_cookie_params([
  'lifetime' => 0,
  'path' => '/',
  'secure' => true, // Only if using HTTPS
  'httponly' => true,
  'samesite' => 'Strict'
]);

session_start();

// Regenerate session ID to prevent session fixation
session_regenerate_id();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: ../../signin.php');
    exit(); // Stop further execution if the user is not logged in
}

require_once('../../model/my_fns.php');

// Fetch user details using the session username
$username = $_SESSION['username'];
$userDetails = fetch_user_details($username);

if (!$userDetails) {
    die("User details not found.");
}

$userEmail = $userDetails['email'];

// Assuming $userDetails['avatar'] contains the filename of the user's avatar from the database
$avatar = $userDetails['avatar'];

// Check if the user has uploaded an avatar or not
if (!empty($avatar)) {
    // If avatar exists, show the uploaded image
    $avatarPath = "../../uploads/" . $avatar;
} else {
    // If no avatar, show the default image
    $avatarPath = "../assets/img/avatar.svg";
}

?>

<!DOCTYPE html>
<html lang="en">
<meta http-equiv="content-type" content="text/html;charset=UTF-8" /><!-- /Added by HTTrack -->
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Nexus Dashboard</title>
  <link rel="shortcut icon" href="../../images/logo.jfif" type="image/x-icon">
  <link rel="stylesheet" href="../assets/css/style.css">
  <!-- Bootstrap CSS -->
  <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet"> -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <style>
      .clickable-row {
          cursor: pointer;
        }
        
        .clickable-row:hover {
          background-color: #f0f0f0; /* Light gray hover effect */
        }
  </style>
</head>

<body id="dark">
  <header class="dark-bb">
    <?php include '../navbar.php'; ?>
  </header>
  
  <div class="markets ptb70">
    <div class="container">
      <div class="row">
        <div class="col-md-4">
          <div class="markets-container">
            <div class="markets-content">
              <h2>EURUSD</h2>
              <p>7340.65</p>
              <span class="green"> + 0.45%</span>
            </div>
            <div class="markets-chart">
              <div id="marketsChartBtcLight"></div>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="markets-container">
            <div class="markets-content">
              <h2>AUDUSD</h2>
              <p>146.58</p>
              <span class="red"> - 5.09%</span>
            </div>
            <div class="markets-chart">
              <div id="marketsChartEthLight"></div>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="markets-container">
            <div class="markets-content">
              <h2>XAUUSD</h2>
              <p>44.49</p>
              <span class="green"> + 2.14%</span>
            </div>
            <div class="markets-chart">
              <div id="marketsChartLtcLight"></div>
            </div>
          </div>
        </div>

        <div class="col-md-12">
          <div class="markets-pair-list">
              <ul class="nav nav-pills" id="pills-tab" role="tablist">
                <!-- Major Pairs Tab -->
                <li class="nav-item">
                  <a class="nav-link active" data-toggle="pill" href="#major" role="tab" aria-selected="true">Major Pairs</a>
                </li>
                <!-- Minor Pairs Tab -->
                <li class="nav-item">
                  <a class="nav-link" data-toggle="pill" href="#minor" role="tab" aria-selected="true">Minor Pairs</a>
                </li>
                <!-- Cryptocurrencies Tab -->
                <li class="nav-item">
                  <a class="nav-link" data-toggle="pill" href="#crypto" role="tab" aria-selected="true">Cryptocurrencies</a>
                </li>
              </ul>
            
              <div class="tab-content">
                <!-- Major Pairs -->
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
            
                <!-- Minor Pairs -->
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
            
                <!-- Cryptocurrencies -->
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
            
              <!-- Refresh Button -->
              <div class="text-center">
                <button id="refresh-button" class="load-more btn">
                  Refresh <i class="icon ion-md-refresh"></i>
                </button>
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
  <script src="../assets/js/custom.js"></script>
</body>

<script>
  // WebSocket Setup
  const socket = new WebSocket('wss://ws.binaryws.com/websockets/v3?app_id=64600');

  const majorPairs = [
    'frxAUDCAD', 'frxAUDCHF', 'frxAUDJPY', 'frxAUDNZD', 'frxAUDUSD',
    'frxEURAUD', 'frxEURCAD', 'frxEURCHF', 'frxEURGBP', 'frxEURJPY', 'frxEURNZD', 'frxEURUSD',
    'frxGBPAUD', 'frxGBPCAD', 'frxGBPCHF', 'frxGBPJPY', 'frxGBPNZD', 'frxGBPUSD'
  ];
  const minorPairs = ['frxUSDMXN', 'frxUSDNOK', 'frxUSDPLN', 'frxUSDSEK'];
  const cryptocurrencies = ['cryBTCUSD', 'cryETHUSD'];

  let currentTab = 'major';
  const activeSubscriptions = new Set();
  const pairData = {};

  // Update individual rows in the table
  function updateRow(pair, trend, price, high, low, volume) {
    const tableBody = document.getElementById(`${currentTab}-pairs`);
    pairData[pair] = { ...pairData[pair], trend, price, high, low, volume };

    // Remove 'frx' and 'cry' prefixes for display
    const displayPair = pair.replace(/^(frx|cry)/, '');

    // Sort the pairs alphabetically by their display names
    const sortedPairs = Object.keys(pairData)
      .filter(symbol => activeSubscriptions.has(symbol)) // Ensure it's an active subscription
      .sort((a, b) => {
        const displayA = a.replace(/^(frx|cry)/, '');
        const displayB = b.replace(/^(frx|cry)/, '');
        return displayA.localeCompare(displayB);
      });

    // Clear the table and re-render in sorted order
    tableBody.innerHTML = '';
    sortedPairs.forEach(sortedPair => {
      const data = pairData[sortedPair];
      const displaySortedPair = sortedPair.replace(/^(frx|cry)/, '');

      let row = document.createElement('tr');
      row.id = sortedPair;
      row.classList.add('clickable-row'); // Add a class for styling
      row.setAttribute('data-href', `trade/index.php?${displaySortedPair}`); // Store the link in a data attribute

      row.innerHTML = `
        <td>${displaySortedPair}</td>
        <td class="trend"></td>
        <td class="price"></td>
        <td class="high"></td>
        <td class="low"></td>
        <td class="volume"></td>
      `;

      tableBody.appendChild(row);

      // Update cell data
      updateCell(row.querySelector('.trend'), data.trend, data.trend > 0 ? '↑' : '↓');
      updateCell(row.querySelector('.price'), data.price);
      updateCell(row.querySelector('.high'), data.high.toFixed(5));
      updateCell(row.querySelector('.low'), data.low.toFixed(5));
      updateCell(row.querySelector('.volume'), data.volume);
    });

    // Add click event listeners to rows
    document.querySelectorAll('.clickable-row').forEach(row => {
      row.addEventListener('click', function () {
        const href = this.getAttribute('data-href');
        window.open(href, '_blank'); // Open the link in a new tab
      });
    });
  }

  // Update the color of a cell based on the new value
  function updateCell(cell, newValue, displayValue = newValue) {
    const oldValue = parseFloat(cell.textContent) || 0;
    cell.textContent = displayValue;

    if (newValue > oldValue) {
      cell.style.color = 'green'; // Green for increase
    } else if (newValue < oldValue) {
      cell.style.color = 'red'; // Red for decrease
    } else {
      cell.style.color = 'white'; // White for no change
    }
  }

  // Subscribe to pairs
  function fetchPairs(pairs) {
    activeSubscriptions.forEach(symbol => {
      socket.send(JSON.stringify({ forget: symbol }));
    });
    activeSubscriptions.clear();

    pairs.forEach(pair => {
      socket.send(JSON.stringify({ ticks: pair, subscribe: 1 }));
      activeSubscriptions.add(pair);
      pairData[pair] = { high: -Infinity, low: Infinity, volume: Math.floor(Math.random() * 1000) };
    });
  }

  // WebSocket connection opened
  socket.onopen = function () {
    fetchPairs(majorPairs); // Fetch major pairs on connection open
  };

  // Handle WebSocket messages
  socket.onmessage = function (event) {
    const data = JSON.parse(event.data);
    if (data.tick) {
      const { symbol, quote } = data.tick;

      pairData[symbol].price = quote;
      pairData[symbol].high = Math.max(pairData[symbol].high, quote);
      pairData[symbol].low = Math.min(pairData[symbol].low, quote);
      pairData[symbol].volume += Math.floor(Math.random() * 100);

      updateRow(
        symbol,
        Math.random() > 0.5 ? 1 : -1,
        pairData[symbol].price,
        pairData[symbol].high,
        pairData[symbol].low,
        pairData[symbol].volume
      );
    }
  };

  // Tab navigation
  document.querySelectorAll('.nav-link').forEach(tab => {
    tab.addEventListener('click', function (event) {
      event.preventDefault();
      currentTab = this.getAttribute('href').substring(1);

      const pairs = currentTab === 'major'
        ? majorPairs
        : currentTab === 'minor'
        ? minorPairs
        : cryptocurrencies;

      document.getElementById(`${currentTab}-pairs`).innerHTML = '';
      fetchPairs(pairs);
    });
  });

  // Refresh button functionality
  document.getElementById('refresh-button').addEventListener('click', () => {
    console.log("Refreshing market data...");
    fetchPairs(majorPairs); // Re-fetch the major pairs to refresh the data
    document.getElementById(`${currentTab}-pairs`).innerHTML = ''; // Clear current table
  });
</script>

</html>