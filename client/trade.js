document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("tradeForm");
  const upButton = document.getElementById("upButton");
  const downButton = document.getElementById("downButton");

  const derivWs = new WebSocket("wss://ws.binaryws.com/websockets/v3?app_id=64600");
  let isAuthorized = false;

  derivWs.onopen = () => {
    console.log("WebSocket connection opened.");
    const authToken = "lgjjWAG5XxK2jQL"; // Replace with your API token
    derivWs.send(JSON.stringify({ authorize: authToken }));
  };

  derivWs.onmessage = (message) => {
    const response = JSON.parse(message.data);
    console.log("WebSocket Response:", response);

    if (response.msg_type === "authorize" && response.error) {
      alert(`Authorization Error: ${response.error.message}`);
    } else if (response.msg_type === "authorize") {
      console.log("Authorization successful.");
      isAuthorized = true;
    }

    if (response.msg_type === "proposal" && response.proposal) {
      console.log("Contract Proposal Details:", response.proposal);
      executeTrade(response.proposal);
    }

    if (response.msg_type === "buy" && response.buy) {
      alert(`Trade Successful! Contract ID: ${response.buy.contract_id}`);
    }

    if (response.error) {
      alert(`Error: ${response.error.message}`);
    }
  };

  derivWs.onerror = (error) => {
    console.error("WebSocket Error:", error);
    alert("WebSocket connection error. Please try again later.");
  };

  derivWs.onclose = () => {
    console.log("WebSocket connection closed.");
  };

  const fetchExchangeRate = async () => {
    try {
      const response = await fetch("https://api.exchangerate-api.com/v4/latest/NGN");
      const data = await response.json();
      return data.rates.USD || 0.0013; // Default rate
    } catch (error) {
      console.error("Exchange rate error:", error);
      return 0.0013; // Fallback rate
    }
  };

  const prepareTradeData = async (formData, action) => {
    const exchangeRate = await fetchExchangeRate();
    const stakeAmountNGN = parseFloat(formData.get("stake_amount"));
    const stakeAmountUSD = (stakeAmountNGN * exchangeRate).toFixed(2);

    const assetDetails = JSON.parse(formData.get("asset"));
    const multiplier = formData.get("multiplier");
    const duration = formData.get("duration");

    const parameters = {
      symbol: assetDetails.asset,
      currency: "USD",
      contract_type: multiplier ? (action === "up" ? "MULTUP" : "MULTDOWN") : action === "up" ? "CALL" : "PUT",
    };

    if (multiplier) {
      // Multiplier trade uses "payout"
      parameters.multiplier = multiplier;
      return {
        buy: 1,
        price: stakeAmountUSD, // Price corresponds to payout for multiplier trades
        parameters,
      };
    } else {
      // Non-multiplier trade uses "stake"
      parameters.duration = duration;
      parameters.duration_unit = "s";
      derivWs.send(
        JSON.stringify({
          proposal: 1,
          amount: stakeAmountUSD,
          basis: "stake",
          ...parameters,
        })
      );
      return null; // Wait for proposal response
    }
  };

  const executeTrade = (proposal) => {
    const tradeData = {
      buy: 1,
      price: proposal.ask_price,
      parameters: {
        symbol: proposal.symbol,
        currency: "USD",
        contract_type: proposal.contract_type,
        duration: proposal.duration,
        duration_unit: proposal.duration_unit,
      },
    };
    derivWs.send(JSON.stringify(tradeData));
    console.log("Trade Data Sent:", tradeData);
  };

  const submitTrade = async (action) => {
    const formData = new FormData(form);
    formData.append("action", action);

    if (!isAuthorized) {
      alert("Authorization is required before placing a trade.");
      return;
    }

    const tradeData = await prepareTradeData(formData, action);
    if (tradeData) {
      console.log("Sending Trade Data:", tradeData);
      derivWs.send(JSON.stringify(tradeData));
    }
  };

  upButton.addEventListener("click", () => submitTrade("up"));
  downButton.addEventListener("click", () => submitTrade("down"));
});