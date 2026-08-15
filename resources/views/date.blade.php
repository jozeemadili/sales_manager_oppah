<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Date Flow 💖</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
    body {
        background: linear-gradient(135deg, #ffb6c1, #ffc0cb);
        height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: Arial;
        overflow: hidden;
    }

    .card {
        max-width: 520px;
        width: 100%;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    }

    .title {
        color: #ff1493;
        font-weight: bold;
    }

    .hidden {
        display: none;
    }

    .food-btn {
        margin: 5px;
    }

    .selected {
        background: #ff1493 !important;
        color: white !important;
        border: none !important;
    }

    #noBtn {
        position: relative;
    }
</style>
</head>

<body>

<div class="card p-4 bg-white text-center">

<!-- STEP 1 -->
<div id="step1">
    <h3 class="title">Will you go on a date with me? 💕</h3>

    <button class="btn btn-danger mt-3" id="yesBtn">Yes 💖</button>
    <button class="btn btn-secondary mt-3" id="noBtn">No 😅</button>
</div>

<!-- STEP 2 -->
<div id="step2" class="hidden">
    <h4 class="title">When will you be free? 💕</h4>

    <div class="mt-3 text-start">

        <label class="form-label fs-5">📅 Pick Day</label>
        <input type="date" id="date" class="form-control mb-3">

        <label class="form-label fs-5">⏰ Pick Time</label>
        <input type="time" id="time" class="form-control">

    </div>

    <button class="btn btn-danger mt-3 w-100" onclick="nextStep2()">Next 💖</button>
</div>

<!-- STEP 3 -->
<div id="step3" class="hidden">
    <h4 class="title">What are you feeling like eating? 🍽️</h4>

    <div class="mt-3">
        <button class="btn btn-outline-dark food-btn" onclick="selectFood(this,'Pizza')">Pizza 🍕</button>
        <button class="btn btn-outline-dark food-btn" onclick="selectFood(this,'Burger')">Burger 🍔</button>
        <button class="btn btn-outline-dark food-btn" onclick="selectFood(this,'Pasta')">Pasta 🍝</button>
        <button class="btn btn-outline-dark food-btn" onclick="selectFood(this,'Ramen')">Ramen 🍜</button>
        <button class="btn btn-outline-dark food-btn" onclick="selectFood(this,'KFC')">KFC 🍗</button>
        <button class="btn btn-outline-dark food-btn" onclick="selectFood(this,'Ice Cream')">Ice Cream 🍦</button>
    </div>

    <button class="btn btn-danger mt-3 w-100" onclick="finish()">Finish 💖</button>
</div>

<!-- STEP 4 -->
<div id="step4" class="hidden">
    <h4 class="text-success fw-bold" id="finalText"></h4>
</div>

</div>

<script>

let selectedFood = "";

// YES
document.getElementById("yesBtn").onclick = function () {
    document.getElementById("step1").classList.add("hidden");
    document.getElementById("step2").classList.remove("hidden");
};

// NO runaway button
const noBtn = document.getElementById("noBtn");

noBtn.addEventListener("mouseover", () => {
    noBtn.style.position = "absolute";
    noBtn.style.left = Math.random() * 80 + "%";
    noBtn.style.top = Math.random() * 80 + "%";
});

// STEP 2
function nextStep2() {
    document.getElementById("step2").classList.add("hidden");
    document.getElementById("step3").classList.remove("hidden");
}

// FOOD select
function selectFood(btn, food) {
    selectedFood = food;

    document.querySelectorAll(".food-btn").forEach(b => {
        b.classList.remove("selected");
    });

    btn.classList.add("selected");
}

// FINAL
function finish() {
    let date = document.getElementById("date").value;
    let time = document.getElementById("time").value;

    document.getElementById("step3").classList.add("hidden");
    document.getElementById("step4").classList.remove("hidden");

    document.getElementById("finalText").innerHTML =
        "💖 I'm glad you didn't say no 💖<br><br>" +
        "📅 Be ready by: <b>" + date + "</b><br>" +
        "⏰ At: <b>" + time + "</b><br><br>" +
        "🍽️ Food mood: <b>" + selectedFood + "</b><br><br>" +
        "🚗 I'm coming to get you. Don't keep me waiting 😘";
}

</script>

</body>
</html>