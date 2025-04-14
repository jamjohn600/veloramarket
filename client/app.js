// Ensure Luxon is used as the date adapter for Chart.js
Chart._adapters._date.override({
    _create: function (time) {
        return luxon.DateTime.fromMillis(time);
    },
});

// Store account tokens securely using localStorage
const storeAccountTokens = (accounts) => {
    localStorage.setItem('deriv_accounts', JSON.stringify(accounts));
};

const getStoredAccountTokens = () => {
    const data = localStorage.getItem('deriv_accounts');
    return data ? JSON.parse(data) : null;
};

// Extract tokens from URL after OAuth authorization
const extractTokensFromUrl = () => {
    const params = new URLSearchParams(window.location.search);
    const accounts = [];
    let index = 1;

    while (params.has(`acct${index}`) && params.has(`token${index}`)) {
        accounts.push({
            account: params.get(`acct${index}`),
            token: params.get(`token${index}`),
            currency: params.get(`cur${index}`),
        });
        index++;
    }

    if (accounts.length > 0) {
        storeAccountTokens(accounts);
        alert('Authorization successful. Tokens stored securely.');
    }
};

// Initialize WebSocket with the user's app ID
const ws = new WebSocket("wss://ws.binaryws.com/websockets/v3?app_id=64452");

let currentToken = '';
let selectedSymbol = 'R_100'; // Default symbol (change as needed)

// Set the token from stored account data
const initializeWebSocket = () => {
    const accounts = getStoredAccountTokens();
    if (accounts && accounts.length > 0) {
        currentToken = accounts[0].token;
        subscribeToTicks(selectedSymbol);
    } else {
        alert('No token found. Please authorize your account.');
    }
};

// Function to subscribe to real-time tick data
const subscribeToTicks = (symbol) => {
    if (!currentToken) {
        console.error('Missing authorization token.');
        return;
    }

    ws.send(
        JSON.stringify({
            authorize: currentToken,
        })
    );

    ws.onopen = () => {
        console.log(`WebSocket connection established. Subscribing to ${symbol}`);
        ws.send(
            JSON.stringify({
                ticks: symbol,
                subscribe: 1,
            })
        );
    };
};

// Real-time chart setup using Chart.js
const ctx = document.getElementById('price-chart').getContext('2d');
const priceChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: [],
        datasets: [
            {
                label: 'Price',
                data: [],
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 2,
                fill: false,
            },
        ],
    },
    options: {
        scales: {
            x: {
                type: 'time',
                time: {
                    unit: 'minute',
                },
            },
        },
    },
});

// Function to update chart with new price data
const updateChart = (price, timestamp) => {
    priceChart.data.labels.push(new Date(timestamp * 1000)); // Convert UNIX timestamp to Date
    priceChart.data.datasets[0].data.push(price);
    priceChart.update();
};

// Handle incoming price data
ws.onmessage = (event) => {
    const data = JSON.parse(event.data);

    if (data.authorize) {
        console.log('Authorization successful:', data.authorize);
    }

    if (data.tick) {
        const price = data.tick.quote;
        const timestamp = data.tick.epoch;
        updateChart(price, timestamp);
    }

    if (data.error) {
        console.error('Error:', data.error.message);
        alert(`Error: ${data.error.message}`);
    }
};

// Handle WebSocket errors
ws.onerror = (error) => {
    console.error('WebSocket error:', error);
};

// Handle WebSocket close
ws.onclose = () => {
    console.log('WebSocket connection closed');
};

// Extract tokens from URL and initialize WebSocket connection
window.onload = () => {
    extractTokensFromUrl();
    initializeWebSocket();
};
