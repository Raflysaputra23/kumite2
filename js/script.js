let timer;
let time = 180;

function updateDisplay() {
    const min = String(Math.floor(time / 60)).padStart(1, '0');
    const sec = String(time % 60).padStart(2, '0');
    document.getElementById("timer").textContent = `${min}:${sec}`;
}

function startTimer() {
    if (timer) return;
    timer = setInterval(() => {
        if (time > 0) {
            time--;
            updateDisplay();
        } else {
            clearInterval(timer);
        }
    }, 1000);
}

function pauseTimer() {
    clearInterval(timer);
    timer = null;
}

function resetTimer() {
    pauseTimer();
    time = 180;
    updateDisplay();
}

function toggleFullscreen() {
    if (!document.fullscreenElement) {
        document.documentElement.requestFullscreen();
    } else {
        document.exitFullscreen();
    }
}

function toggle(el) {
    el.classList.toggle('active');
}

function addScore(team) {
    const scoreId = `score${team}`;
    const el = document.getElementById(scoreId);
    el.textContent = parseInt(el.textContent) + 1;
}

function subScore(team) {
    const scoreId = `score${team}`;
    const el = document.getElementById(scoreId);
    if (parseInt(el.textContent) > 0) {
        el.textContent = parseInt(el.textContent) - 1;
    }
}

function prepareSubmit() {
    document.getElementById('redNameSubmit').value = document.getElementById('nameRed').value;
    document.getElementById('blueNameSubmit').value = document.getElementById('nameBlue').value;
    document.getElementById('scoreRedSubmit').value = document.getElementById('scoreRed').textContent;
    document.getElementById('scoreBlueSubmit').value = document.getElementById('scoreBlue').textContent;
}
