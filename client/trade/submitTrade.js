// document.addEventListener('DOMContentLoaded', function () {
//     // Create the modal HTML structure
//     const modalHTML = `
//         <div id="apiModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5); z-index: 1000; text-align: center; padding-top: 20%;">
//             <div style="background-color: #fff; border-radius: 10px; padding: 20px; width: 80%; max-width: 500px; margin: 0 auto; box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);">
//                 <h2 style="color: green;">Software access is currently under development. </h2>
//                 <p style="color: #333; font-size: 16px;">Please check back soon.</p>
//                 <button id="closeModal" style="background-color: #3498db; color: #fff; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px;">Close</button>
//             </div>
//         </div>
//     `;

//     // Append the modal to the body
//     document.body.insertAdjacentHTML('beforeend', modalHTML);

//     // Show the modal
//     const modal = document.getElementById('apiModal');
//     modal.style.display = 'block';

//     // Close the modal when the close button is clicked
//     const closeModalButton = document.getElementById('closeModal');
//     closeModalButton.addEventListener('click', function () {
//         modal.style.display = 'none';
//     });
// });


// document.addEventListener('DOMContentLoaded', function () {
//     document.getElementById('confirm-trade').addEventListener('click', async function (event) {
//         event.preventDefault();
//         // Create the modal HTML structure
//         const modalHTML = `
//             <div id="apiModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5); z-index: 1000; text-align: center; padding-top: 20%;">
//                 <div style="background-color: #fff; border-radius: 10px; padding: 20px; width: 80%; max-width: 500px; margin: 0 auto; box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);">
//                     <h2 style="color: green;">Software access is currently under development. </h2>
//                 <p style="color: #333; font-size: 16px;">Please check back soon.</p>
//                     <button id="closeModal" style="background-color: #3498db; color: #fff; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px;">Close</button>
//                 </div>
//             </div>
//         `;
    
//         // Append the modal to the body
//         document.body.insertAdjacentHTML('beforeend', modalHTML);
    
//         // Show the modal
//         const modal = document.getElementById('apiModal');
//         modal.style.display = 'block';
    
//         // Close the modal when the close button is clicked
//         const closeModalButton = document.getElementById('closeModal');
//         closeModalButton.addEventListener('click', function () {
//             modal.style.display = 'none';
//         });
//     })
// })

document.addEventListener('DOMContentLoaded', function () {
    const alertBox = document.querySelector('.alert-box');
    const tradeModal = new bootstrap.Modal(document.getElementById('tradeModal'));
    const orderIdElement = document.getElementById('orderId');
    const positionIdElement = document.getElementById('positionId');
    const remainingTimeElement = document.getElementById('remainingTime');
    const currentProfitElement = document.getElementById('currentProfit');
    const loaderOverlay = document.getElementById('loader-overlay');
    const loader = document.getElementById('loader');
    const closeTradeButton = document.getElementById('closeTrade');

    function showAlert(message, type = 'success') {
        const alert = document.createElement('div');
        alert.className = `alert alert-${type} fade show`;
        alert.role = 'alert';
        alert.innerHTML = message;
        alertBox.appendChild(alert);
        setTimeout(() => alert.remove(), 5000); // Auto-remove after 5 seconds
    }

    document.getElementById('confirm-trade').addEventListener('click', async function (event) {
        event.preventDefault();

        // Fetch form inputs
        const usernameInput = document.getElementById('username');
        const symbolInput = document.getElementById('symbol');
        const balanceInput = document.getElementById('balance');
        const maxStakeInput = document.getElementById('max-stake');
        const lotsInput = document.getElementById('lots');
        const durationValueInput = document.getElementById('duration-value');
        const durationUnitInput = document.getElementById('duration-unit');
        const orderTypeInput = document.getElementById('order-type');

        // Parse form inputs
        const symbol = `${symbolInput.value}m`;
        const formattedBalance = balanceInput.value.replace(/,/g, ''); // Remove commas
        const balance = parseFloat(formattedBalance);
        const maxStakeNGN = parseFloat(maxStakeInput.value);
        const lots = parseFloat(lotsInput.value);
        const durationValue = parseInt(durationValueInput.value, 10);
        const durationUnit = durationUnitInput.value;
        const orderType = orderTypeInput.value;
        const username = usernameInput.value;

        // Convert duration into milliseconds
        const durationMs = (() => {
            switch (durationUnit) {
                case 'seconds': return durationValue * 1000;
                case 'minutes': return durationValue * 60 * 1000;
                case 'hours': return durationValue * 60 * 60 * 1000;
                default: throw new Error(`Invalid duration unit: ${durationUnit}`);
            }
        })();

        // Fetch the current exchange rate (USD to NGN)
        async function fetchExchangeRate() {
            const fallbackRate = 1700; // Default fallback rate
            try {
                const response = await fetch('https://api.exchangerate-api.com/v4/latest/USD');
                const data = await response.json();
                return data.rates.NGN || fallbackRate;
            } catch (error) {
                console.error('Error fetching exchange rate:', error);
                return fallbackRate;
            }
        }

        // Show loader
        loaderOverlay.style.display = 'flex';

        try {
            // Convert maxStake to USD
            const exchangeRate = await fetchExchangeRate();
            const maxStakeUSD = exchangeRate ? (maxStakeNGN / exchangeRate).toFixed(2) : maxStakeNGN;
            const maxStakeBuffer = parseFloat(maxStakeUSD) * 1.1; // Add 10% buffer

            // Initialize MetaApi
            const token = 'eyJhbGciOiJSUzUxMiIsInR5cCI6IkpXVCJ9.eyJfaWQiOiIyNmY4MTNiZWVkODcyMGU5MjI4NTMzYTA2Y2U0MTE2MiIsInBlcm1pc3Npb25zIjpbXSwiYWNjZXNzUnVsZXMiOlt7ImlkIjoidHJhZGluZy1hY2NvdW50LW1hbmFnZW1lbnQtYXBpIiwibWV0aG9kcyI6WyJ0cmFkaW5nLWFjY291bnQtbWFuYWdlbWVudC1hcGk6cmVzdDpwdWJsaWM6KjoqIl0sInJvbGVzIjpbInJlYWRlciIsIndyaXRlciJdLCJyZXNvdXJjZXMiOlsiYWNjb3VudDokVVNFUl9JRCQ6NzcwYWU0ZmUtMGJhOS00YmQ2LWIwYTctYTNlNmE4ZjZhZDAyIl19LHsiaWQiOiJtZXRhYXBpLXJlc3QtYXBpIiwibWV0aG9kcyI6WyJtZXRhYXBpLWFwaTpyZXN0OnB1YmxpYzoqOioiXSwicm9sZXMiOlsicmVhZGVyIiwid3JpdGVyIl0sInJlc291cmNlcyI6WyJhY2NvdW50OiRVU0VSX0lEJDo3NzBhZTRmZS0wYmE5LTRiZDYtYjBhNy1hM2U2YThmNmFkMDIiXX0seyJpZCI6Im1ldGFhcGktcnBjLWFwaSIsIm1ldGhvZHMiOlsibWV0YWFwaS1hcGk6d3M6cHVibGljOio6KiJdLCJyb2xlcyI6WyJyZWFkZXIiLCJ3cml0ZXIiXSwicmVzb3VyY2VzIjpbImFjY291bnQ6JFVTRVJfSUQkOjc3MGFlNGZlLTBiYTktNGJkNi1iMGE3LWEzZTZhOGY2YWQwMiJdfSx7ImlkIjoibWV0YWFwaS1yZWFsLXRpbWUtc3RyZWFtaW5nLWFwaSIsIm1ldGhvZHMiOlsibWV0YWFwaS1hcGk6d3M6cHVibGljOio6KiJdLCJyb2xlcyI6WyJyZWFkZXIiLCJ3cml0ZXIiXSwicmVzb3VyY2VzIjpbImFjY291bnQ6JFVTRVJfSUQkOjc3MGFlNGZlLTBiYTktNGJkNi1iMGE3LWEzZTZhOGY2YWQwMiJdfSx7ImlkIjoibWV0YXN0YXRzLWFwaSIsIm1ldGhvZHMiOlsibWV0YXN0YXRzLWFwaTpyZXN0OnB1YmxpYzoqOioiXSwicm9sZXMiOlsicmVhZGVyIl0sInJlc291cmNlcyI6WyJhY2NvdW50OiRVU0VSX0lEJDo3NzBhZTRmZS0wYmE5LTRiZDYtYjBhNy1hM2U2YThmNmFkMDIiXX0seyJpZCI6InJpc2stbWFuYWdlbWVudC1hcGkiLCJtZXRob2RzIjpbInJpc2stbWFuYWdlbWVudC1hcGk6cmVzdDpwdWJsaWM6KjoqIl0sInJvbGVzIjpbInJlYWRlciIsIndyaXRlciJdLCJyZXNvdXJjZXMiOlsiYWNjb3VudDokVVNFUl9JRCQ6NzcwYWU0ZmUtMGJhOS00YmQ2LWIwYTctYTNlNmE4ZjZhZDAyIl19LHsiaWQiOiJtZXRhYXBpLXJlYWwtdGltZS1zdHJlYW1pbmctYXBpIiwibWV0aG9kcyI6WyJtZXRhYXBpLWFwaTp3czpwdWJsaWM6KjoqIl0sInJvbGVzIjpbInJlYWRlciIsIndyaXRlciJdLCJyZXNvdXJjZXMiOlsiYWNjb3VudDokVVNFUl9JRCQ6NzcwYWU0ZmUtMGJhOS00YmQ2LWIwYTctYTNlNmE4ZjZhZDAyIl19LHsiaWQiOiJjb3B5ZmFjdG9yeS1hcGkiLCJtZXRob2RzIjpbImNvcHlmYWN0b3J5LWFwaTpyZXN0OnB1YmxpYzoqOioiXSwicm9sZXMiOlsicmVhZGVyIiwid3JpdGVyIl0sInJlc291cmNlcyI6WyJhY2NvdW50OiRVU0VSX0lEJDo3NzBhZTRmZS0wYmE5LTRiZDYtYjBhNy1hM2U2YThmNmFkMDIiXX1dLCJpZ25vcmVSYXRlTGltaXRzIjpmYWxzZSwidG9rZW5JZCI6IjIwMjEwMjEzIiwiaW1wZXJzb25hdGVkIjpmYWxzZSwicmVhbFVzZXJJZCI6IjI2ZjgxM2JlZWQ4NzIwZTkyMjg1MzNhMDZjZTQxMTYyIiwiaWF0IjoxNzM2NzkyMjkyLCJleHAiOjE3MzkzODQyOTJ9.TlFuU7V97w_wKpDixcg3A2fvMp70iBY4LokRkx4LMRPki4MPJZ26U6CN0T2gJfJOYsh4svD2sjDm-I7qwDnj65kWcHygfKX3ReMLLrV3cpz7hYL8QJm-iIX5Aoo4vYp9fAH3S4pKNG_pS_-UPJVn4dwePhPPCeuQr3nCFpKresII-uZyXWCKvuBSE_CgB5l2r0r0NUAYqDuqY-4Kna1eyeJo56kXQgWFNtp2DpbZOiZWv5X6B4m1AylutxJtSjbSELJbjE5jjtNO53KL2BOJCGcXRpfBcfhqObGs8pQ6Q__CW9OTU06d4yT7rMv_3PIIqrRiNzw1iAPq1sGwUxzCjdPDfhSuz0Nv80AFE3qf6LQAE6iySSoHux2VmXRDpcB-r5PVmesu5TV_XcMhIa3hJ2uXpW46wZgMq1cqVo2vE7WzsB--XgWkkL3x2jRbkODIVuIAQnD6310Pb7ajs_CP6GBrGi7Kr3IgToXnRXy4R8qB6CeCfufPeOwLxuoGrMvc-tHXheXXS1_2dZDlQtUEpM9KZP3f9z1hkrn5wsnq8exj0dNfPktBic42TCostpkWRR4ZAIjqdCFia268Zc1WWpH5eYXLW55QzbQI5f6syive4lQ5pr9vcei6vVlEdYLDLx7CWJZPpbgQs2WM30fzHGzf6Rcr1rPL59quyhSvnO0'; // Replace with your MetaApi token
            const accountId = '770ae4fe-0ba9-4bd6-b0a7-a3e6a8f6ad02'; // Replace with your account ID
            const api = new MetaApi.default(token);

            // Connect to MetaTrader account
            const account = await api.metatraderAccountApi.getAccount(accountId);
            console.log('Connecting to broker...');
            await account.waitConnected();

            const connection = account.getStreamingConnection();
            await connection.connect();
            
            // access local copy of terminal state
            const terminalState = connection.terminalState;

            console.log('Waiting for terminal synchronization...');
            await connection.waitSynchronized();

            // Hide loader and display modal
            loaderOverlay.style.display = 'none';

            console.log(`Placing ${orderType} order...`);
            const tradeResult = orderType === 'buy'
                ? await connection.createMarketBuyOrder(symbol, lots)
                : await connection.createMarketSellOrder(symbol, lots);
            console.log('Trade placed:', tradeResult);

            const positionId = tradeResult.positionId;
            positionIdElement.textContent = positionId;
            tradeModal.show();

            let remainingTime = durationMs / 1000;
            remainingTimeElement.textContent = `${remainingTime}s`;

            const updateRemainingTime = () => {
                remainingTime -= 1;
                remainingTimeElement.textContent = `${remainingTime}s`;
            };

            // Monitor profit/loss
            const profitUpdater = setInterval(async () => {
                const positions = terminalState.positions;
                const position = positions.find(p => p.id === positionId);

                if (position) {
                    const profit = position.unrealizedProfit.toFixed(2);
                    currentProfitElement.textContent = profit;

                    if (Math.abs(profit) >= maxStakeBuffer) {
                        showAlert('Max loss exceeded. Closing trade...', 'warning');
                        clearInterval(profitUpdater);
                        clearInterval(countdownInterval);
                        closeTrade(connection, positionId);
                    }
                }
            }, 1000);

            // Add click event listener to Close Trade button
            closeTradeButton.addEventListener('click', async function () {
                if (positionIdElement.textContent) {
                    clearInterval(profitUpdater); // Stop profit monitoring
                    clearInterval(countdownInterval); // Stop countdown
                    await closeTrade(connection, positionIdElement.textContent);
                } else {
                    console.warn('No position ID available to close the trade.');
                }
            });

            // Countdown interval
            const countdownInterval = setInterval(() => {
                if (remainingTime <= 0) {
                    clearInterval(countdownInterval);
                    clearInterval(profitUpdater);
                    closeTrade(connection, positionId);
                } else {
                    updateRemainingTime();
                }
            }, 1000);

            async function closeTrade(connection, positionId) {
                try {
                    const closeResult = await connection.closePosition(positionId);
                    console.log('Trade closed:', closeResult);

                    const finalProfit = await fetchClosedTradeProfit(connection, positionId);
                    const finalProfitNGN = (finalProfit * exchangeRate).toFixed(2);
                    const profitOrLoss = finalProfit >= 0 ? 'Profit' : 'Loss';

                    const tradeData = {
                        symbol,
                        positionId,
                        orderType,
                        duration: `${durationValue} ${durationUnit}`,
                        result: profitOrLoss,
                        profitOrLossAmount: finalProfitNGN,
                        username
                    };

                    console.log('Trade data:', tradeData);
                    sendTradeData(tradeData);

                    showAlert(`Trade closed with ${profitOrLoss}: ₦${Math.abs(finalProfitNGN)}`, 'info');
                    tradeModal.hide();
                } catch (error) {
                    console.error('Error closing trade:', error.message || error);
                }
            }

            function sendTradeData(tradeData) {
                const xhr = new XMLHttpRequest();
                xhr.open('POST', 'saveTrade.php', true);
                xhr.setRequestHeader('Content-Type', 'application/json;charset=UTF-8');

                xhr.onreadystatechange = function () {
                    if (xhr.readyState === 4) {
                        if (xhr.status === 200) {
                            const response = JSON.parse(xhr.responseText);
                            if (response.status === 'success') {
                                showAlert(response.message, 'success');
                                location.reload();
                            } else {
                                showAlert(`Error: ${response.message}`, 'danger');
                            }
                        } else {
                            showAlert(`Error: Failed to communicate with the server (Status: ${xhr.status})`, 'danger');
                        }
                    }
                };

                xhr.send(JSON.stringify(tradeData));
            }

            async function fetchClosedTradeProfit(connection, positionId) {
                try {
                    const startTime = new Date(Date.now() - 7 * 24 * 60 * 60 * 1000); // 7 days ago
                    const endTime = new Date();
                    const deals = await connection.getDealsByTimeRange(startTime, endTime);
                    const relatedDeals = deals.deals.filter(deal => deal.positionId === positionId);

                    if (!relatedDeals || relatedDeals.length === 0) {
                        return 0;
                    }

                    const closingDeal = relatedDeals.find(deal => deal.entryType === 'DEAL_ENTRY_OUT');
                    return closingDeal ? closingDeal.profit : 0;
                } catch (error) {
                    console.error('Error fetching closed trade profit:', error.message || error);
                    return 0;
                }
            }
        } catch (err) {
            console.error('Error:', err.message || err);
            loaderOverlay.style.display = 'none';
            showAlert('An error occurred while processing the trade.', 'danger');
        }
    });
});

// document.addEventListener('DOMContentLoaded', function () {
//     // Cached DOM elements
//     const elements = {
//         alertBox: document.querySelector('.alert-box'),
//         tradeModal: new bootstrap.Modal(document.getElementById('tradeModal')),
//         orderId: document.getElementById('orderId'),
//         positionId: document.getElementById('positionId'),
//         remainingTime: document.getElementById('remainingTime'),
//         currentProfit: document.getElementById('currentProfit'),
//         loaderOverlay: document.getElementById('loader-overlay'),
//         closeTradeButton: document.getElementById('closeTrade'),
//         confirmTradeButton: document.getElementById('confirm-trade'),
//         usernameInput: document.getElementById('username'),
//         symbolInput: document.getElementById('symbol'),
//         balanceInput: document.getElementById('balance'),
//         maxStakeInput: document.getElementById('max-stake'),
//         lotsInput: document.getElementById('lots'),
//         durationValueInput: document.getElementById('duration-value'),
//         durationUnitInput: document.getElementById('duration-unit'),
//         orderTypeInput: document.getElementById('order-type'),
//     };

//     // Utility Module
//     const Utils = {
//         async fetchExchangeRate() {
//             const fallbackRate = 1700;
//             try {
//                 const response = await fetch('https://api.exchangerate-api.com/v4/latest/USD');
//                 const data = await response.json();
//                 return data.rates.NGN || fallbackRate;
//             } catch (error) {
//                 console.error('Error fetching exchange rate:', error);
//                 return fallbackRate;
//             }
//         },

//         convertDurationToMs(value, unit) {
//             const units = { seconds: 1000, minutes: 60000, hours: 3600000 };
//             return value * (units[unit] || 0);
//         },

//         sendTradeData(tradeData) {
//             return new Promise((resolve, reject) => {
//                 const xhr = new XMLHttpRequest();
//                 xhr.open('POST', 'saveTrade.php', true);
//                 xhr.setRequestHeader('Content-Type', 'application/json;charset=UTF-8');
//                 xhr.onload = () => {
//                     if (xhr.status === 200) {
//                         const response = JSON.parse(xhr.responseText);
//                         response.status === 'success' ? resolve(response) : reject(response.message);
//                     } else {
//                         reject(`Server error: ${xhr.status}`);
//                     }
//                 };
//                 xhr.onerror = () => reject('Network error');
//                 xhr.send(JSON.stringify(tradeData));
//             });
//         },
//     };

//     // UI Module
//     const UI = {
//         showAlert(message, type = 'success') {
//             const alert = document.createElement('div');
//             alert.className = `alert alert-${type} fade show`;
//             alert.role = 'alert';
//             alert.innerHTML = message;
//             elements.alertBox.appendChild(alert);
//             setTimeout(() => alert.remove(), 5000);
//         },

//         toggleLoader(show) {
//             elements.loaderOverlay.style.display = show ? 'flex' : 'none';
//         },

//         updateTradeModal({ positionId, remainingTime }) {
//             elements.positionId.textContent = positionId;
//             elements.remainingTime.textContent = `${remainingTime}s`;
//             elements.tradeModal.show();
//         },

//         updateProfit(profit) {
//             elements.currentProfit.textContent = profit.toFixed(2);
//         },
//     };

//     // API Module
//     const MetaApiHandler = {
//         token: 'eyJhbGciOiJSUzUxMiIsInR5cCI6IkpXVCJ9.eyJfaWQiOiIyNmY4MTNiZWVkODcyMGU5MjI4NTMzYTA2Y2U0MTE2MiIsInBlcm1pc3Npb25zIjpbXSwiYWNjZXNzUnVsZXMiOlt7ImlkIjoidHJhZGluZy1hY2NvdW50LW1hbmFnZW1lbnQtYXBpIiwibWV0aG9kcyI6WyJ0cmFkaW5nLWFjY291bnQtbWFuYWdlbWVudC1hcGk6cmVzdDpwdWJsaWM6KjoqIl0sInJvbGVzIjpbInJlYWRlciIsIndyaXRlciJdLCJyZXNvdXJjZXMiOlsiYWNjb3VudDokVVNFUl9JRCQ6NzcwYWU0ZmUtMGJhOS00YmQ2LWIwYTctYTNlNmE4ZjZhZDAyIl19LHsiaWQiOiJtZXRhYXBpLXJlc3QtYXBpIiwibWV0aG9kcyI6WyJtZXRhYXBpLWFwaTpyZXN0OnB1YmxpYzoqOioiXSwicm9sZXMiOlsicmVhZGVyIiwid3JpdGVyIl0sInJlc291cmNlcyI6WyJhY2NvdW50OiRVU0VSX0lEJDo3NzBhZTRmZS0wYmE5LTRiZDYtYjBhNy1hM2U2YThmNmFkMDIiXX0seyJpZCI6Im1ldGFhcGktcnBjLWFwaSIsIm1ldGhvZHMiOlsibWV0YWFwaS1hcGk6d3M6cHVibGljOio6KiJdLCJyb2xlcyI6WyJyZWFkZXIiLCJ3cml0ZXIiXSwicmVzb3VyY2VzIjpbImFjY291bnQ6JFVTRVJfSUQkOjc3MGFlNGZlLTBiYTktNGJkNi1iMGE3LWEzZTZhOGY2YWQwMiJdfSx7ImlkIjoibWV0YWFwaS1yZWFsLXRpbWUtc3RyZWFtaW5nLWFwaSIsIm1ldGhvZHMiOlsibWV0YWFwaS1hcGk6d3M6cHVibGljOio6KiJdLCJyb2xlcyI6WyJyZWFkZXIiLCJ3cml0ZXIiXSwicmVzb3VyY2VzIjpbImFjY291bnQ6JFVTRVJfSUQkOjc3MGFlNGZlLTBiYTktNGJkNi1iMGE3LWEzZTZhOGY2YWQwMiJdfSx7ImlkIjoibWV0YXN0YXRzLWFwaSIsIm1ldGhvZHMiOlsibWV0YXN0YXRzLWFwaTpyZXN0OnB1YmxpYzoqOioiXSwicm9sZXMiOlsicmVhZGVyIl0sInJlc291cmNlcyI6WyJhY2NvdW50OiRVU0VSX0lEJDo3NzBhZTRmZS0wYmE5LTRiZDYtYjBhNy1hM2U2YThmNmFkMDIiXX0seyJpZCI6InJpc2stbWFuYWdlbWVudC1hcGkiLCJtZXRob2RzIjpbInJpc2stbWFuYWdlbWVudC1hcGk6cmVzdDpwdWJsaWM6KjoqIl0sInJvbGVzIjpbInJlYWRlciIsIndyaXRlciJdLCJyZXNvdXJjZXMiOlsiYWNjb3VudDokVVNFUl9JRCQ6NzcwYWU0ZmUtMGJhOS00YmQ2LWIwYTctYTNlNmE4ZjZhZDAyIl19LHsiaWQiOiJtZXRhYXBpLXJlYWwtdGltZS1zdHJlYW1pbmctYXBpIiwibWV0aG9kcyI6WyJtZXRhYXBpLWFwaTp3czpwdWJsaWM6KjoqIl0sInJvbGVzIjpbInJlYWRlciIsIndyaXRlciJdLCJyZXNvdXJjZXMiOlsiYWNjb3VudDokVVNFUl9JRCQ6NzcwYWU0ZmUtMGJhOS00YmQ2LWIwYTctYTNlNmE4ZjZhZDAyIl19LHsiaWQiOiJjb3B5ZmFjdG9yeS1hcGkiLCJtZXRob2RzIjpbImNvcHlmYWN0b3J5LWFwaTpyZXN0OnB1YmxpYzoqOioiXSwicm9sZXMiOlsicmVhZGVyIiwid3JpdGVyIl0sInJlc291cmNlcyI6WyJhY2NvdW50OiRVU0VSX0lEJDo3NzBhZTRmZS0wYmE5LTRiZDYtYjBhNy1hM2U2YThmNmFkMDIiXX1dLCJpZ25vcmVSYXRlTGltaXRzIjpmYWxzZSwidG9rZW5JZCI6IjIwMjEwMjEzIiwiaW1wZXJzb25hdGVkIjpmYWxzZSwicmVhbFVzZXJJZCI6IjI2ZjgxM2JlZWQ4NzIwZTkyMjg1MzNhMDZjZTQxMTYyIiwiaWF0IjoxNzM2NzkyMjkyLCJleHAiOjE3MzkzODQyOTJ9.TlFuU7V97w_wKpDixcg3A2fvMp70iBY4LokRkx4LMRPki4MPJZ26U6CN0T2gJfJOYsh4svD2sjDm-I7qwDnj65kWcHygfKX3ReMLLrV3cpz7hYL8QJm-iIX5Aoo4vYp9fAH3S4pKNG_pS_-UPJVn4dwePhPPCeuQr3nCFpKresII-uZyXWCKvuBSE_CgB5l2r0r0NUAYqDuqY-4Kna1eyeJo56kXQgWFNtp2DpbZOiZWv5X6B4m1AylutxJtSjbSELJbjE5jjtNO53KL2BOJCGcXRpfBcfhqObGs8pQ6Q__CW9OTU06d4yT7rMv_3PIIqrRiNzw1iAPq1sGwUxzCjdPDfhSuz0Nv80AFE3qf6LQAE6iySSoHux2VmXRDpcB-r5PVmesu5TV_XcMhIa3hJ2uXpW46wZgMq1cqVo2vE7WzsB--XgWkkL3x2jRbkODIVuIAQnD6310Pb7ajs_CP6GBrGi7Kr3IgToXnRXy4R8qB6CeCfufPeOwLxuoGrMvc-tHXheXXS1_2dZDlQtUEpM9KZP3f9z1hkrn5wsnq8exj0dNfPktBic42TCostpkWRR4ZAIjqdCFia268Zc1WWpH5eYXLW55QzbQI5f6syive4lQ5pr9vcei6vVlEdYLDLx7CWJZPpbgQs2WM30fzHGzf6Rcr1rPL59quyhSvnO0',
//         accountId: '770ae4fe-0ba9-4bd6-b0a7-a3e6a8f6ad02',

//         async connectToBroker() {
//             const api = new MetaApi.default(this.token);
//             const account = await api.metatraderAccountApi.getAccount(this.accountId);
//             await account.waitConnected();
//             const connection = account.getRPCConnection();
//             await connection.connect();
//             await connection.waitSynchronized();
//             return connection;
//         },

//         async placeTrade(connection, symbol, lots, orderType) {
//             return orderType === 'buy'
//                 ? connection.createMarketBuyOrder(symbol, lots)
//                 : connection.createMarketSellOrder(symbol, lots);
//         },

//         async closeTrade(connection, positionId) {
//             return connection.closePosition(positionId);
//         },

//         async fetchClosedTradeProfit(connection, positionId) {
//             const startTime = new Date(Date.now() - 7 * 24 * 60 * 60 * 1000);
//             const endTime = new Date();
//             const deals = await connection.getDealsByTimeRange(startTime, endTime);
//             const closingDeal = deals.deals.find(deal => deal.positionId === positionId && deal.entryType === 'DEAL_ENTRY_OUT');
//             return closingDeal ? closingDeal.profit : 0;
//         },
//     };

//     // Trade Monitoring
//     async function monitorTrade(connection, positionId, maxStakeBuffer, durationMs) {
//         let remainingTime = durationMs / 1000;

//         const countdownInterval = setInterval(() => {
//             if (remainingTime <= 0) {
//                 clearInterval(countdownInterval);
//                 closeTrade();
//             } else {
//                 remainingTime -= 1;
//                 elements.remainingTime.textContent = `${remainingTime}s`;
//             }
//         }, 1000);

//         const profitUpdater = setInterval(async () => {
//             const positions = await connection.getPositions();
//             const position = positions.find(p => p.id === positionId);

//             if (position) {
//                 const profit = position.unrealizedProfit;
//                 UI.updateProfit(profit);

//                 if (Math.abs(profit) >= maxStakeBuffer) {
//                     UI.showAlert('Max loss exceeded. Closing trade...', 'warning');
//                     clearInterval(profitUpdater);
//                     clearInterval(countdownInterval);
//                     closeTrade();
//                 }
//             }
//         }, 1000);

//         async function closeTrade() {
//             clearInterval(profitUpdater);
//             const profit = await MetaApiHandler.fetchClosedTradeProfit(connection, positionId);
//             const profitNGN = profit * (await Utils.fetchExchangeRate());
//             UI.showAlert(`Trade closed with profit: ₦${profitNGN.toFixed(2)}`, 'info');
//         }
//     }

//     // Trade Execution
//     elements.confirmTradeButton.addEventListener('click', async event => {
//         event.preventDefault();
//         UI.toggleLoader(true);

//         try {
//             const symbol = `${elements.symbolInput.value}m`;
//             const balance = parseFloat(elements.balanceInput.value.replace(/,/g, ''));
//             const maxStakeNGN = parseFloat(elements.maxStakeInput.value);
//             const lots = parseFloat(elements.lotsInput.value);
//             const durationMs = Utils.convertDurationToMs(
//                 parseInt(elements.durationValueInput.value, 10),
//                 elements.durationUnitInput.value
//             );
//             const orderType = elements.orderTypeInput.value;
//             const exchangeRate = await Utils.fetchExchangeRate();
//             const maxStakeBuffer = (maxStakeNGN / exchangeRate) * 1.1;

//             const connection = await MetaApiHandler.connectToBroker();
//             const tradeResult = await MetaApiHandler.placeTrade(connection, symbol, lots, orderType);
//             UI.updateTradeModal({ positionId: tradeResult.positionId, remainingTime: durationMs / 1000 });

//             monitorTrade(connection, tradeResult.positionId, maxStakeBuffer, durationMs);
//         } catch (error) {
//             console.error('Trade Error:', error);
//             UI.showAlert('An error occurred while processing the trade.', 'danger');
//         } finally {
//             UI.toggleLoader(false);
//         }
//     });
// });