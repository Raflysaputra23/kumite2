<!DOCTYPE html>
<html lang="en">

<head>
  <script src="https://cdn.sheetjs.com/xlsx-latest/package/dist/xlsx.full.min.js"></script>

  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Kumite Scoreboard</title>
  <style>
    @keyframes blink {

      0%,
      100% {
        opacity: 1;
      }

      50% {
        opacity: 0;
      }
    }

    .blink {
      animation: blink 1s step-start infinite;
    }

    body {
      margin: 0;
      font-family: 'Inter', 'Segoe UI', sans-serif;
      background-color: #1a1a1a;
      color: #f5f5f5;
      display: flex;
      flex-direction: column;
      align-items: center;
      padding: 20px;
      min-height: 100vh;
    }

    /* Form & Layout Section */
    .form-section {
      width: 100%;
      max-width: 950px;
      background: #002b86;
      padding: 40px;
      border-radius: 10px;
      margin-bottom: 32px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.4);
    }

    .form-section h2 {
      margin-top: 0;
      color: #f9c74f;
    }

    .form-section input,
    .form-section select,
    textarea {
      width: 100%;
      padding: 10px;
      margin-top: 6px;
      background: #ffffe0;
      border: 1px solid #444;
      color: #ff4500;
      border-radius: 6px;
      font-size: 18px;
    }

    .form-section label {
      margin-top: 14px;
      display: block;
      color: #ccc;
    }

    .player-section {
      display: grid;
      grid-template-columns: 2fr 1fr;
      gap: 30px;
      width: 100%;
      max-width: 900px;
      margin-bottom: 32px;
      background: #2b2b2b;
      padding: 20px;
      border-radius: 10px;
      border-left: 45px solid #555;
    }

    .player1 {
      border-color: #e74c3c;
    }

    .player2 {
      border-color: #3498db;
    }

    .player-info h1 {
      font-size: 32px;
      margin: 0;
      color: #fff;
      font-weight: 600;
    }

    .player-info p {
      font-size: 14px;
      color: #aaa;
      margin: 4px 0;
    }

    .penalty-line {
      display: flex;
      flex-wrap: wrap;
      gap: 6px;
      margin-top: 10px;
    }

    .penalty-line div {
      padding: 6px 12px;
      border-radius: 4px;
      background: #3a3a3a;
      border: 1px solid #555;
      cursor: pointer;
      font-size: 14px;
    }

    .penalty-line div.active {
      background: #f1c40f;
      color: #000;
      font-weight: bold;
    }

    .score {
      font-size: 96px;
      font-weight: bold;
      text-align: center;
      color: #eee;
    }

    .score.red {
      color: #e74c3c;
    }

    .score.blue {
      color: #3498db;
    }

    .buttons {
      margin-top: 10px;
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      justify-content: center;
    }

    .buttons button {
      padding: 10px 20px;
      font-size: 14px;
      border-radius: 6px;
      border: 2px solid #00d2b3;
      background: transparent;
      color: #00d2b3;
      cursor: pointer;
      font-weight: 500;
      transition: all 0.2s ease;
    }

    .buttons button:hover:not(:disabled) {
      background: #00d2b3;
      color: #000;
    }

    button:disabled {
      opacity: 0.5;
      cursor: not-allowed;
    }

    .bottom-section {
      display: grid;
      grid-template-columns: 1fr 1fr 2fr;
      gap: 20px;
      width: 100%;
      max-width: 900px;
      background: #1f1f1f;
      padding: 24px;
      border-radius: 10px;
      color: #f5f5f5;
    }

    .tatami-number {
      font-size: 18px;
    }

    .timer {
      font-size: 48px;
      font-weight: bold;
      text-align: center;
    }

    .collapsed {
      max-height: 50px;
      overflow: hidden;
      transition: max-height 0.3s ease;
    }


    textarea {
      resize: none;
      min-height: 100px;
    }

    select,
    input[type="number"] {
      background: #2e2e2e;
      color: #fff;
      border: 1px solid #555;
      border-radius: 5px;
      padding: 8px;
    }
  </style>
</head>

<body>
  <audio id="beepSound" src="/kumite/beep.mp3" preload="auto"></audio>
  <!-- Player Input Form -->
  <div class="form-section" id="playerForm">
    <h2 style="display: flex; justify-content: space-between; align-items: center;">
      Input Player Info
      <button onclick="togglePlayerForm()" style="font-size: 16px; padding: 6px 12px;">🔽</button>
    </h2>

    <h3 style="color: #ff4d4d;">🟥 Player 1 (Red)</h3>
    <label>AKA NAME: <input id="p1Name" placeholder="Kumite Player 1" /></label>
    <label>AKA PERGURUAN: <input id="p1Club" placeholder="ABC" /></label>
    <label>AKA DAERAH: <input id="p1Team" placeholder="Team ABC Info" /></label>

    <h3 style="color: #4d94ff; margin-top: 20px;">🟦 Player 2 (Blue)</h3>
    <label>AO NAME: <input id="p2Name" placeholder="Kumite Player 2" /></label>
    <label>AO PERGURUAN: <input id="p2Club" placeholder="DEF" /></label>
    <label>AO DAERAH: <input id="p2Team" placeholder="Team DEF Info" /></label>

    <br />
    <button onclick="updatePlayerInfo()">✔️ Update Info</button>
  </div>

  <!-- Player Sections -->

  <div class="player-section player1">
    <div class="player-info">
      <h1 id="player1Name">Kumite Player 1</h1>
      <p id="player1Club">ABC</p>
      <p id="player1Team">Team ABC Info</p>
      <div class="penalty-line" id="penaltyRed">
        <div onclick="togglePenalty(this)">1C</div>
        <div onclick="togglePenalty(this)">2C</div>
        <div onclick="togglePenalty(this)">3C</div>
        <div onclick="togglePenalty(this)">HC</div>
        <div onclick="togglePenalty(this)">H</div>
      </div>
      <div class="buttons">
        <button onclick="addScore('Red', 1)">+1</button>
        <button onclick="addScore('Red', 2)">+2</button>
        <button onclick="addScore('Red', 3)">+3</button>
        <button onclick="addScore('Red', -1)">-1</button>
        <button onclick="addScore('Red', -2)">-2</button>
        <button onclick="addScore('Red', -3)">-3</button>
        <button id="senshuBtnRed" onclick="setSenshu('Red')">✔️ Senshu</button>
      </div>
    </div>
    <div class="score red">
      <span id="senshuRed"></span><span id="scoreRedValue">0</span>
      <div style="font-size: 18px; margin-top: 8px;" id="scoreDetailRed">AKA: 0 - 0 - 0</div>
    </div>

  </div>

  <div class="player-section player2">
    <div class="player-info">
      <h1 id="player2Name">Kumite Player 2</h1>
      <p id="player2Club">DEF</p>
      <p id="player2Team">Team DEF Info</p>
      <div class="penalty-line" id="penaltyBlue">
        <div onclick="togglePenalty(this)">1C</div>
        <div onclick="togglePenalty(this)">2C</div>
        <div onclick="togglePenalty(this)">3C</div>
        <div onclick="togglePenalty(this)">HC</div>
        <div onclick="togglePenalty(this)">H</div>
      </div>
      <div class="buttons">
        <button onclick="addScore('Blue', 1)">+1</button>
        <button onclick="addScore('Blue', 2)">+2</button>
        <button onclick="addScore('Blue', 3)">+3</button>
        <button onclick="addScore('Blue', -1)">-1</button>
        <button onclick="addScore('Blue', -2)">-2</button>
        <button onclick="addScore('Blue', -3)">-3</button>
        <button id="senshuBtnBlue" onclick="setSenshu('Blue')">✔️ Senshu</button>
      </div>
    </div>
    <div class="score blue">
      <span id="senshuBlue"></span><span id="scoreBlueValue">0</span>
      <div style="font-size: 18px; margin-top: 8px;" id="scoreDetailBlue">AO: 0 - 0 - 0</div>
    </div>

  </div>

  <!-- Controls -->

  <div class="buttons">
    <button onclick="saveToDatabase()">💾 Save to Database</button>
    <button onclick="resetScore()">Reset Score</button>
    <button onclick="toggleTimer()"><span id="timerBtn">▶️ Start</span></button>
    <button onclick="resetTimer()">⏹ Reset Timer</button>
    <button onclick="openLiveView()">🟢 Open Live View</button>
    <button onclick="openLiveTracking()">🟢 Open Live Tracking</button>
    <button onclick="resetHistoryJuri()">Reset History Juri</button>
    <button onclick="exportToExcel()">📤 Export Excel</button>


    <!-- Timer Presets -->

    <select id="timerPreset" onchange="setTimerPreset()">
      <option value="60000">01:00</option>
      <option value="90000">01:30</option>
      <option value="120000">02:00</option>
      <option value="180000" selected>03:00</option>
    </select>

    <!-- Manual Time Input -->

    <div style="display: flex; flex-direction: column; align-items: center;">
      <label style="color: white;">Manual Time (MM:SS):</label>
      <div style="display: flex; gap: 5px; align-items: center;">
        <input id="manualMinutes" type="number" min="0" max="99" placeholder="Minutes" style="width: 80px;" />
        <input id="manualSeconds" type="number" min="0" max="59" placeholder="Seconds" style="width: 80px;" />
        <button onclick="setManualTime()">⏱ Set Manual Time</button>
      </div>
    </div>
  </div>

  <!-- Bottom Section -->

  <div class="bottom-section">
    <div class="tatami-number">
      <label for="tatamiSelect">TATAMI</label><br />
      <select id="tatamiSelect" onchange="updateTatami()">
        <option value="1">1</option>
        <option value="2">2</option>
        <option value="3">3</option>
        <option value="4">4</option>
        <option value="5">5</option>
        <option value="6">6</option>
        <option value="7">7</option>
        <option value="8">8</option>
        <option value="9">9</option>
        <option value="10">10</option>
      </select>
    </div>

    <div class="timer" id="timer">3:00<sup style="font-size:24px;">00</sup></div>
    <div>
      <label for="description" style="font-weight: bold;">Match Description:</label><br />
      <textarea id="description" placeholder="Describe the match, strategy, or important notes"
        style="font-size: 18px; padding: 10px; width: 80%; height: 120px; border-radius: 6px;"
        oninput="saveDescription()"></textarea>
    </div>

  </div>

  <script>
    let redScore = 0, blueScore = 0;
    let time = 180000;
    let timerRunning = false;
    let interval;
    let senshu = null;
    let beepPlayed = false;
    const tatami = localStorage.getItem("tatamiNumber") || 1;

    const resetHistoryJuri = () => {
      localStorage.setItem("historyJuri" + tatami, JSON.stringify([]));
    }

    loadScores();
    setInterval(() => {
      loadScores();
    }, 1000);

    // Load data dari localStorage saat halaman dimuat
    window.addEventListener('load', () => {
      // Load player info
      const p1 = JSON.parse(localStorage.getItem('player1')) || {};
      const p2 = JSON.parse(localStorage.getItem('player2')) || {};

      document.getElementById('player1Name').innerText = p1.name || 'Kumite Player 1';
      document.getElementById('player1Club').innerText = p1.club || 'ABC';
      document.getElementById('player1Team').innerText = p1.team || 'Team ABC Info';

      document.getElementById('player2Name').innerText = p2.name || 'Kumite Player 2';
      document.getElementById('player2Club').innerText = p2.club || 'DEF';
      document.getElementById('player2Team').innerText = p2.team || 'Team DEF Info';

      // Load scores
      redScore = parseInt(localStorage.getItem('redScore' + tatami)) || 0;
      blueScore = parseInt(localStorage.getItem('blueScore' + tatami)) || 0;
      document.getElementById('scoreRedValue').innerText = redScore;
      document.getElementById('scoreBlueValue').innerText = blueScore;
      localStorage.setItem('timerPlay', false);

      // Load timer
      time = parseInt(localStorage.getItem('timer' + tatami)) || 180000;
      updateTimerDisplay();

      // Load senshu
      senshu = localStorage.getItem('senshu') || null;
      updateSenshuDisplay();

      // Restore penalty states
      restorePenalties();

      // Restore tatami selection
      const savedTatami = localStorage.getItem('tatamiNumber') || '1';
      document.getElementById('tatamiSelect').value = savedTatami;

      // Restore match description
      const desc = localStorage.getItem('matchDescription') || '';
      document.getElementById('description').value = desc;
      updateScoreDetails(); // <-- Tambahkan ini
    });


    function openLiveView() {
      // buka live view sesuai tatami
      const tatami = document.getElementById('tatamiSelect').value || '1';
      localStorage.setItem("tatamiNumber", tatami);
      window.open(`live_view.php?tatami=${tatami}`, '_blank');

    }

    function openLiveTracking() {
      // buka tracking sesuai tatami
      const tatami = document.getElementById('tatamiSelect').value || '1';
      localStorage.setItem("tatamiNumber", tatami);
      window.open(`live_tracking.php?tatami=${tatami}`, '_blank');
    }



    // Update informasi player
    function updatePlayerInfo() {
      const p1Name = document.getElementById('p1Name').value || 'Kumite Player 1';
      const p1Club = document.getElementById('p1Club').value || 'ABC';
      const p1Team = document.getElementById('p1Team').value || 'Team ABC Info';

      const p2Name = document.getElementById('p2Name').value || 'Kumite Player 2';
      const p2Club = document.getElementById('p2Club').value || 'DEF';
      const p2Team = document.getElementById('p2Team').value || 'Team DEF Info';

      localStorage.setItem('player1', JSON.stringify({ name: p1Name, club: p1Club, team: p1Team }));
      localStorage.setItem('player2', JSON.stringify({ name: p2Name, club: p2Club, team: p2Team }));

      document.getElementById('player1Name').innerText = p1Name;
      document.getElementById('player1Club').innerText = p1Club;
      document.getElementById('player1Team').innerText = p1Team;

      document.getElementById('player2Name').innerText = p2Name;
      document.getElementById('player2Club').innerText = p2Club;
      document.getElementById('player2Team').innerText = p2Team;
    }

    function updateTatami() {
      const tatamiValue = document.getElementById('tatamiSelect').value;
      localStorage.setItem('tatamiNumber', tatamiValue);
    }

    // Saat halaman dimuat, atur nilai select sesuai yang tersimpan
    window.onload = function () {
      // (Baris lain sudah ada)
      // Tambahkan ini di bagian bawah dari window.onload:
      const savedTatami = localStorage.getItem('tatamiNumber') || '1';
      document.getElementById('tatamiSelect').value = savedTatami;
      updateScoreDetails(); // <-- Tambahkan ini
    };



    // Senshu logic
    function setSenshu(player) {
      senshu = player;
      localStorage.setItem('senshu', senshu);
      updateSenshuDisplay();
    }

    function updateSenshuDisplay() {
      document.getElementById('senshuRed').innerHTML = senshu === 'Red' ? '<span style="font-size:40px;">◉</span> ' : '';

      document.getElementById('senshuBlue').innerHTML = senshu === 'Blue' ? '<span style="font-size:40px;">◉</span> ' : '';

    }

    // Save the description to localStorage
    function saveDescription() {
      const desc = document.getElementById('description').value;
      localStorage.setItem('matchDescription', desc ? desc : 'No Description');
    }

    // Load the description from localStorage when the page loads
    window.onload = function () {
      const desc = localStorage.getItem('matchDescription') || '';
      document.getElementById('description').value = desc;
      // Other window.onload code remains the same
    };



    // Toggle penalty
    function togglePenalty(element) {
      // Aktif/nonaktifkan tampilan penalti
      element.classList.toggle('active');
      savePenalties();

      const isHCOrH = ['HC', 'H'].includes(element.innerText);
      const isRed = element.parentElement.id === 'penaltyRed';
      const isBlue = element.parentElement.id === 'penaltyBlue';

      // Jika penalti berat (HC atau H) diberikan dan pemain tersebut punya senshu, hapus senshu
      if (isHCOrH) {
        if ((isRed && senshu === 'Red') || (isBlue && senshu === 'Blue')) {
          senshu = null;
          localStorage.removeItem('senshu');
          updateSenshuDisplay();
        }
      }

      // Cek apakah pemain saat ini punya penalti berat aktif (HC atau H)
      const redHCActive = Array.from(document.querySelectorAll('#penaltyRed .active'))
        .some(div => ['HC', 'H'].includes(div.innerText));
      const blueHCActive = Array.from(document.querySelectorAll('#penaltyBlue .active'))
        .some(div => ['HC', 'H'].includes(div.innerText));

      // Nonaktifkan/aktifkan tombol senshu sesuai kondisi
      document.getElementById('senshuBtnRed').disabled = redHCActive;
      document.getElementById('senshuBtnBlue').disabled = blueHCActive;
    }



    // Save and restore penalties
    function savePenalties() {
      const red = Array.from(document.querySelectorAll('#penaltyRed .active')).map(div => div.innerText);
      const blue = Array.from(document.querySelectorAll('#penaltyBlue .active')).map(div => div.innerText);
      localStorage.setItem('penaltiesRed', JSON.stringify(red));
      localStorage.setItem('penaltiesBlue', JSON.stringify(blue));
    }

    function restorePenalties() {
      const red = JSON.parse(localStorage.getItem('penaltiesRed')) || [];
      const blue = JSON.parse(localStorage.getItem('penaltiesBlue')) || [];

      document.querySelectorAll('#penaltyRed div').forEach(div => {
        if (red.includes(div.innerText)) div.classList.add('active');
      });

      document.querySelectorAll('#penaltyBlue div').forEach(div => {
        if (blue.includes(div.innerText)) div.classList.add('active');
      });
    }

    function loadScores() {
      redScore = parseInt(localStorage.getItem('redScore' + tatami)) || 0;
      blueScore = parseInt(localStorage.getItem('blueScore' + tatami)) || 0;
      document.getElementById('scoreRedValue').innerText = redScore;
      document.getElementById('scoreBlueValue').innerText = blueScore;
    }

    // Timer
    function toggleTimer() {
      const btn = document.getElementById('timerBtn');
      if (timerRunning) {
        clearInterval(interval);
        localStorage.setItem('timerPlay', false);
        localStorage.setItem('juri' + localStorage.getItem('tatamiNumber'), JSON.stringify([]));
        timerRunning = false;
        btn.innerText = '▶️ Start';
      } else {
        startTimer();
        localStorage.setItem('timerPlay', true);
        timerRunning = true;
        btn.innerText = '⏸ Pause';
      }
    }

    function startTimer() {
      if (time <= 0) {
        alert("⛔ Waktu sudah habis! Harap atur ulang atau setel waktu terlebih dahulu.");
        return;
      }

      const now = Date.now();
      const endTime = now + time;

      localStorage.setItem('timerStart', now);
      localStorage.setItem('timerEnd', endTime);
      beepPlayed = false; // reset flag saat timer mulai

      interval = setInterval(() => {
        const timerPlay = localStorage.getItem("timerPlay");
        if (timerPlay === 'false') {
          clearInterval(interval);
          timerRunning = false;
          document.getElementById('timerBtn').innerText = '▶️ Start';
          return false;
        }

        const remaining = endTime - Date.now();
        time = Math.max(0, remaining);

        // Mainkan beep sekali saat waktu menyentuh 15 detik
        if (time <= 15000 && !beepPlayed) {
          beepPlayed = true;   // set dulu biar gak ke-trigger dobel
          const audio = document.getElementById("beepSound");
          audio.currentTime = 0;
          audio.play().catch(() => { });
        }

        if (time <= 0) {
          clearInterval(interval);
          timerRunning = false;
          document.getElementById('timerBtn').innerText = '▶️ Start';
          localStorage.setItem('timerPlay', false);
          localStorage.setItem('juri' + tatami, JSON.stringify([]));

          // ⏱ Waktu habis, tentukan pemenang
          const winner = determineWinner();
          setTimeout(() => {
            if (winner === 'Draw') {
              alert('⚖️ Hasil seri! Tidak ada pemenang.');
            } else {
              alert(`🎉 ${winner === 'Red' ? '🟥 AKA' : '🟦 AO'} menang!`);
            }
          }, 100); // Sedikit delay agar UI update dulu
        }


        updateTimerDisplay();
        localStorage.setItem('timer' + tatami, time);
      }, 100);
    }





    function updateTimerDisplay() {
      let minutes = Math.floor(time / 60000);
      let seconds = Math.floor((time % 60000) / 1000);
      let milliseconds = Math.floor((time % 1000) / 10); // ambil dua digit ms
      // Ubah warna latar belakang saat waktu tinggal 15 detik
      const timerEl = document.getElementById('timer');

      if (time <= 0) {
        timerEl.style.color = 'white';
        timerEl.classList.remove('blink');
      } else if (time <= 15000) {
        timerEl.style.color = 'red';
        timerEl.classList.add('blink');
      } else {
        timerEl.style.color = 'yellow';
        timerEl.classList.remove('blink');
      }

      timerEl.innerHTML =
        `${minutes}:${seconds < 10 ? '0' : ''}${seconds}<sup style="font-size:24px;">${milliseconds < 10 ? '0' : ''}${milliseconds}</sup>`;
    }


    function resetTimer() {
      // Ambil nilai preset dari dropdown
      const preset = parseInt(document.getElementById("timerPreset").value);
      time = preset;

      // Reset tampilan timer
      updateTimerDisplay();

      // Hentikan timer jika sedang berjalan
      clearInterval(interval);
      timerRunning = false;
      document.getElementById("timerBtn").innerText = "▶️ Start";

      // Simpan nilai ke localStorage
      localStorage.setItem("timer" + tatami, time);
    }



    // Set timer dari preset
    function setTimerPreset() {
      resetTimer();
    }



    // Set waktu manual berdasarkan input
    function setManualTime() {
      const minutes = parseInt(document.getElementById('manualMinutes').value) || 0;
      const seconds = parseInt(document.getElementById('manualSeconds').value) || 0;

      const totalMilliseconds = (minutes * 60 + seconds) * 1000;

      if (totalMilliseconds <= 0) {
        alert('Waktu tidak valid. Masukkan waktu lebih dari 0 detik.');
        return;
      }

      time = totalMilliseconds;
      clearInterval(interval); // pastikan timer berhenti
      timerRunning = false;
      document.getElementById("timerBtn").innerText = "▶️ Start";
      updateTimerDisplay();
      localStorage.setItem('timer', time);
    }


    function determineWinner() {
      const red = redScore;
      const blue = blueScore;

      const redIppon = countScoreDetails('Red', 3);
      const blueIppon = countScoreDetails('Blue', 3);

      const redWazari = countScoreDetails('Red', 2);
      const blueWazari = countScoreDetails('Blue', 2);

      // 1. Menang jika selisih skor >= 8
      if (Math.abs(red - blue) >= 8) {
        return red > blue ? 'Red' : 'Blue';
      }

      // 2. Skor sama dan punya senshu
      if (red === blue) {
        if (senshu === 'Red') return 'Red';
        if (senshu === 'Blue') return 'Blue';

        // 3. Tidak ada senshu, cek ippon terbanyak
        if (redIppon !== blueIppon) {
          return redIppon > blueIppon ? 'Red' : 'Blue';
        }

        // 4. Ippon sama, cek wazari terbanyak
        if (redWazari !== blueWazari) {
          return redWazari > blueWazari ? 'Red' : 'Blue';
        }

        // 5. Semua sama
        return 'Draw';
      }

      // 6. Jika skor beda tapi selisih < 8, yang lebih tinggi menang
      return red > blue ? 'Red' : 'Blue';
    }


    // Fungsi bantu: hitung jumlah skor tertentu
    let scoreLog = {
      Red: [],
      Blue: []
    };

    function addScore(player, amount) {
      if (player === 'Red') {
        if (amount > 0) {
          redScore += amount;
          scoreLog.Red.push(amount);
        } else if (amount < 0) {
          // Hapus skor terakhir sesuai jumlah negatif (prioritaskan nilai itu)
          const indexToRemove = scoreLog.Red.lastIndexOf(Math.abs(amount));
          if (indexToRemove !== -1) {
            scoreLog.Red.splice(indexToRemove, 1);
            redScore = Math.max(0, redScore + amount);
          }
        }

        document.getElementById('scoreRedValue').innerText = redScore;
        localStorage.setItem('redScore' + tatami, redScore);
      } else {
        if (amount > 0) {
          blueScore += amount;
          scoreLog.Blue.push(amount);
        } else if (amount < 0) {
          const indexToRemove = scoreLog.Blue.lastIndexOf(Math.abs(amount));
          if (indexToRemove !== -1) {
            scoreLog.Blue.splice(indexToRemove, 1);
            blueScore = Math.max(0, blueScore + amount);
          }
        }

        document.getElementById('scoreBlueValue').innerText = blueScore;
        localStorage.setItem('blueScore' + tatami, blueScore);
      }

      localStorage.setItem('scoreLog' + tatami, JSON.stringify(scoreLog));
      updateScoreDetails();
    }




    function countScoreDetails(player, value) {
      return scoreLog[player].filter(v => v === value).length;
    }

    function saveToDatabase() {
      const p1 = JSON.parse(localStorage.getItem('player1')) || {};
      const p2 = JSON.parse(localStorage.getItem('player2')) || {};
      const tatami = localStorage.getItem('tatamiNumber') || '1';
      const description = localStorage.getItem('matchDescription') || '';

      const redScore = parseInt(localStorage.getItem('redScore' + tatami)) || 0;
      const blueScore = parseInt(localStorage.getItem('blueScore' + tatami)) || 0;
      const winner = determineWinner();
      const timestamp = new Date().toLocaleString();
      const redIppon = countScoreDetails('Red', 3);
      const blueIppon = countScoreDetails('Blue', 3);
      const redWazari = countScoreDetails('Red', 2);
      const blueWazari = countScoreDetails('Blue', 2);

      const matchData = {
        timestamp,
        tatami,
        redName: p1.name || '',
        redClub: p1.club || '',
        redTeam: p1.team || '',
        redScore,
        blueName: p2.name || '',
        blueClub: p2.club || '',
        blueTeam: p2.team || '',
        blueScore,
        redIppon,
        blueIppon,
        redWazari,
        blueWazari,
        winner,
        description
      };


      // Ambil data sebelumnya, atau buat array baru
      const history = JSON.parse(localStorage.getItem('matchHistory')) || [];
      history.push(matchData);
      localStorage.setItem('matchHistory', JSON.stringify(history));

      alert("✅ Match data saved locally! Klik 'Export Excel' untuk unduh semua.");
    }

    function exportToExcel() {
      const history = JSON.parse(localStorage.getItem('matchHistory')) || [];
      if (!history.length) return alert("No match history to export!");

      const worksheet = XLSX.utils.json_to_sheet(history, {
        header: [
          "timestamp", "tatami",
          "redName", "redClub", "redTeam", "redScore",
          "blueName", "blueClub", "blueTeam", "blueScore",
          "redIppon", "blueIppon", "redWazari", "blueWazari",
          "winner", "description"
        ]
      });

      const workbook = XLSX.utils.book_new();
      XLSX.utils.book_append_sheet(workbook, worksheet, "Kumite Matches");

      XLSX.writeFile(workbook, "kumite_match_history.xlsx");
    }

    function togglePlayerForm() {
      const form = document.getElementById('playerForm');
      form.classList.toggle('collapsed');

      const btn = form.querySelector('button');
      btn.textContent = form.classList.contains('collapsed') ? '🔼' : '🔽';
    }

    // Shortcut: Tekan spasi untuk toggle timer
    document.addEventListener('keydown', function (event) {
      // Cek jika tombol yang ditekan adalah spasi dan tidak sedang fokus di input atau textarea
      if (event.code === 'Space' && !['INPUT', 'TEXTAREA'].includes(document.activeElement.tagName)) {
        event.preventDefault(); // Hindari scroll saat spasi ditekan
        toggleTimer(); // Jalankan fungsi toggle timer
      }
    });

    // Shortcut: Tekan M atau m untuk toggle form Input Player Info
    document.addEventListener('keydown', function (event) {
      // Abaikan jika sedang mengetik di input/textarea
      if (['INPUT', 'TEXTAREA'].includes(document.activeElement.tagName)) return;

      if (event.key === 'm' || event.key === 'M') {
        togglePlayerForm();
      }
    });
    // Shortcut keyboard untuk skor AKA dan AO
    document.addEventListener('keydown', (e) => {
      // Cek apakah fokus di input atau textarea, agar tidak mengganggu input user
      const activeTag = document.activeElement.tagName.toLowerCase();
      if (activeTag === 'input' || activeTag === 'textarea' || activeTag === 'select') return;

      switch (e.key) {
        // AKA (Red) shortcuts
        case '+':
        case '1':
          addScore('Red', 1); // +1 AKA → 1
          break;
        case '2':
          addScore('Red', 2); // +2 AKA → 2
          break;
        case '3':
          addScore('Red', 3); // +3 AKA → 3
          break;

        // AO (Blue) shortcuts, misal pakai tombol lain
        case '0':
          addScore('Blue', 1); // +1 AO → 0
          break;
        case '9':
          addScore('Blue', 2); // +2 AO → 9
          break;
        case '8':
          addScore('Blue', 3); // +3 AO → 8
          break;
      }
    });
    function resetTimer() {
      if (confirm("Apakah Anda yakin ingin mereset timer ke waktu default?")) {
        time = parseInt(document.getElementById('timerPreset').value) || 180000;
        clearInterval(interval);
        timerRunning = false;
        document.getElementById("timerBtn").innerText = "▶️ Start";
        updateTimerDisplay();
        localStorage.setItem("timer" + tatami, time);

      }
    }

    function resetScore() {
      if (confirm("Apakah Anda yakin ingin mereset skor dan penalti?")) {
        redScore = 0;
        blueScore = 0;
        document.getElementById("scoreRedValue").innerText = redScore;
        document.getElementById("scoreBlueValue").innerText = blueScore;

        // Hapus skor dari localStorage
        localStorage.setItem("redScore" + tatami, "0");
        localStorage.setItem("blueScore" + tatami, "0");
        localStorage.setItem("timerPlay", false);
        localStorage.setItem("juri" + tatami, JSON.stringify([]));
        localStorage.setItem("historyJuri" + tatami, JSON.stringify([]));

        // Reset score log dan detail
        scoreLog.Red = [];
        scoreLog.Blue = [];
        updateScoreDetails();

        // Reset senshu
        senshu = null;
        updateSenshuDisplay();
        localStorage.removeItem('senshu');

        // Reset penalti di UI
        document.querySelectorAll('#penaltyRed div, #penaltyBlue div').forEach(div => {
          div.classList.remove('active');
        });
        localStorage.removeItem('penaltiesRed');
        localStorage.removeItem('penaltiesBlue');

        // Aktifkan kembali tombol senshu
        document.getElementById('senshuBtnRed').disabled = false;
        document.getElementById('senshuBtnBlue').disabled = false;

        // Reset player info
        document.getElementById('player1Name').innerText = 'AKA';
        document.getElementById('player1Club').innerText = 'PERGURUAN';
        document.getElementById('player1Team').innerText = 'DAERAH';

        document.getElementById('player2Name').innerText = 'AO';
        document.getElementById('player2Club').innerText = 'PERGURUAN';
        document.getElementById('player2Team').innerText = 'DAERAH';

        document.getElementById('p1Name').value = '';
        document.getElementById('p1Club').value = '';
        document.getElementById('p1Team').value = '';

        document.getElementById('p2Name').value = '';
        document.getElementById('p2Club').value = '';
        document.getElementById('p2Team').value = '';

        localStorage.removeItem('player1');
        localStorage.removeItem('player2');

        // Reset timer ke preset awal
        resetTimer();
      }
    }



    // Shortcut Keyboard: A/a = Senshu Red, L/l = Senshu Blue
    document.addEventListener('keydown', function (event) {
      const key = event.key.toLowerCase(); // normalize ke huruf kecil

      // Hindari shortcut ketika mengetik di input atau textarea
      if (event.target.tagName === 'INPUT' || event.target.tagName === 'TEXTAREA') return;

      if (key === 'a') {
        if (!document.getElementById('senshuBtnRed').disabled) {
          setSenshu('Red');
        }
      }

      if (key === 'l') {
        if (!document.getElementById('senshuBtnBlue').disabled) {
          setSenshu('Blue');
        }
      }
    });

    // Shortcut keyboard untuk penalti
    document.addEventListener("keydown", function (event) {
      const key = event.key.toLowerCase();

      // Abaikan input jika sedang mengetik
      const tag = event.target.tagName.toLowerCase();
      if (tag === "input" || tag === "textarea") return;

      const shortcuts = {
        q: { id: "penaltyRed", label: "1C" },
        w: { id: "penaltyRed", label: "2C" },
        e: { id: "penaltyRed", label: "3C" },
        r: { id: "penaltyRed", label: "HC" },
        t: { id: "penaltyRed", label: "H" },

        p: { id: "penaltyBlue", label: "1C" },
        o: { id: "penaltyBlue", label: "2C" },
        i: { id: "penaltyBlue", label: "3C" },
        u: { id: "penaltyBlue", label: "HC" },
        y: { id: "penaltyBlue", label: "H" },
      };

      const shortcut = shortcuts[key];
      if (!shortcut) return;

      const parent = document.getElementById(shortcut.id);
      if (!parent) return;

      const elements = parent.querySelectorAll("div");
      for (let el of elements) {
        if (el.textContent.trim() === shortcut.label) {
          togglePenalty(el); // 🔥 Ini kuncinya: panggil langsung fungsi togglePenalty
          break;
        }
      }
    });
    function updateScoreDetails() {
      const aka1 = countScoreDetails('Red', 1);
      const aka2 = countScoreDetails('Red', 2);
      const aka3 = countScoreDetails('Red', 3);
      const ao1 = countScoreDetails('Blue', 1);
      const ao2 = countScoreDetails('Blue', 2);
      const ao3 = countScoreDetails('Blue', 3);

      document.getElementById('scoreDetailRed').innerText = `AKA: ${aka1} - ${aka2} - ${aka3}`;
      document.getElementById('scoreDetailBlue').innerText = `AO: ${ao1} - ${ao2} - ${ao3}`;
    }

  </script>

</body>

</html>