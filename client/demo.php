<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Binary Trading Integration</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Binary Trading Interface</h1>

        <!-- TradingView Widget Container -->
        <div id="tradingview_widget"></div>

        <div class="trading-panel">
            <h2>Place Your Trade</h2>
            <select id="currencyPair">
                <option value="EURUSD">EUR/USD</option>
                <option value="GBPUSD">GBP/USD</option>
                <option value="AUDJPY">USD/JPY</option>
            </select>
            <input type="number" id="amount" placeholder="Enter Amount (USD)" min="1">
            <select id="contractType">
                <option value="CALL">Call (Buy)</option>
                <option value="PUT">Put (Sell)</option>
            </select>
            <button id="tradeButton">Place Trade</button>
            <div id="tradeResult"></div>
        </div>
    </div>

    <!-- TradingView Script -->
    <script src="https://s3.tradingview.com/tv.js"></script>
    <script>
        // Initialize the TradingView widget
        new TradingView.widget({
            "container_id": "tradingview_widget",
            "width": "100%",
            "height": 500,
            "symbol": "FX:EURUSD",
            "interval": "D",
            "timezone": "Etc/UTC",
            "theme": "dark",
            "style": "1",
            "locale": "en",
            "toolbar_bg": "#f1f3f6",
            "enable_publishing": false,
            "hide_top_toolbar": false,
            "save_image": false,
            "studies": ["MACD@tv-basicstudies"],
            "withdateranges": true,
            "hide_side_toolbar": false,
            "allow_symbol_change": true,
            "details": true,
        });
    </script>

    <!-- Your JavaScript File -->
    <script src="app.js"></script>
</body>
</html>
