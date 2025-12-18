<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <title>Live Kumite Tatami <?= $_GET['tatami'] ?></title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <script src="https://unpkg.com/mqtt/dist/mqtt.min.js"></script>

  <style>
    body {
      background: linear-gradient(to bottom right, #000000, #1a1a1a);
      color: white;
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
      text-align: center;
      position: relative;
      box-shadow: inset 0 0 100px rgba(255, 255, 255, 0.05);
      overflow: hidden;
    }

    body::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: linear-gradient(120deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0) 60%);
      pointer-events: none;
    }


    #switchBtn {
      position: absolute;
      top: 10px;
      right: 10px;
      width: 50px;
      height: 50px;
      font-size: 24px;
      background-color: #444;
      color: white;
      border: none;
      border-radius: 50%;
      cursor: pointer;
      z-index: 1100;
    }

    .tatami {
      font-size: 48px;
      font-weight: bold;
      margin-top: 20px;
      color: cyan;
    }

    .description {
      margin-top: 10px;
      font-size: 40px;
      color: orange;
      padding: 3px 5px;
    }

    .player-box {
      position: relative;
      display: flex;
      justify-content: space-around;
      padding: 0px;
      flex-wrap: wrap;
    }

    .player {
      width: 50%;
      padding: 0px;
      border-radius: 10px;
      box-sizing: border-box;
    }

    .red {
      background: #0000;
    }

    .blue {
      background: #0000;
    }

    .player-namem {
      font-size: 50px;
      font-weight: bold;
      color: red;
    }

    .player-nameb {
      font-size: 50px;
      font-weight: bold;
      color: blue;
    }

    .player-club,
    .player-team {
      font-size: 20px;
      margin: 5px 0;
    }

    .scorem {
      font-size: 250px;
      font-weight: bold;
      color: red;
      margin: 0;
      position: relative;
      left: 60%;


      width: 270px;
      height: 205px;
      display: flex;
      justify-content: center;
      align-items: center;

      text-align: center;
      background-color: #f5f5f5;
      border: 5px solid #000;
      border-radius: 10px;
      box-shadow: 4px 4px 10px rgba(0, 0, 0, 0.2);
    }

    .scoreb {
      font-size: 250px;
      font-weight: bold;
      color: blue;
      margin: 0;
      position: relative;
      left: 5%;


      width: 270px;
      height: 205px;
      display: flex;
      justify-content: center;
      align-items: center;

      text-align: center;
      background-color: #f5f5f5;
      border: 5px solid #000;
      border-radius: 10px;
      box-shadow: 4px 4px 10px rgba(0, 0, 0, 0.2);
    }

    .senshu {
      font-size: 48px;
      margin-left: 10px;
      color: green;
      /* Bisa diubah sesuai tema */
      display: inline-block;
      vertical-align: top;
    }

    .penalties {
      margin-top: 10px;
      font-size: 20px;
      position: relative;
      top: -0.5px;
      /* Atur sesuai kebutuhan */
    }

    .timer {
      font-size: 160px;
      font-weight: bold;
      margin-top: 0px;
      color: yellow;
      transition: color 0.01s ease;
      position: relative;
      top: -130px;
      /* Atur sesuai kebutuhan */
    }

    .blink {
      animation: blink-animation 1s steps(2, start) infinite;
    }

    @keyframes blink-animation {
      to {
        visibility: hidden;
      }
    }

    .penalty-box {
      display: inline-block;
      padding: 4px 8px;
      margin: 2px;
      border-radius: 4px;
      font-weight: bold;
      font-size: 50px;
    }

    .yellow-box {
      background-color: yellow;
      color: red;
    }

    .red-box {
      background-color: red;
      color: white;
    }

    @media (max-width: 768px) {
      .player {
        width: 100%;
        margin-bottom: 20px;
      }

      .score {
        font-size: 64px;
      }

      .timer {
        font-size: 48px;
      }

      .tatami {
        font-size: 32px;
      }
    }

    #akaYukoImage,
    #aoYukoImage,
    #wazariImage,
    #ipponImage {
      display: none;
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      max-width: 100%;
      z-index: 1000;
    }

    .player-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 0 50px;
      /* Padding untuk responsivitas */
      gap: 10px;
      /* Jarak antar elemen */
    }

    .player-namem,
    .player-nameb {
      font-size: 2.9vw;
      /* Ukuran font responsif */
      font-weight: bold;
      max-width: 100%;
      /* Hindari melebar terlalu jauh */
      white-space: nowrap;
      align-items: center;
      text-overflow: ellipsis;
    }

    .player-namem {
      color: red;
      text-align: right;
    }

    .player-nameb {
      color: blue;
      text-align: left;
    }

    .logo-wrapper {
      flex-shrink: 0;
      /* Logo tidak mengecil */
      display: flex;
      justify-content: center;
      align-items: center;
      max-width: 100px;
      /* Atur sesuai ukuran logo */
    }


    #forkiLogo {
      max-height: 80px;
      width: auto;
    }

    .club-logo {
      margin-top: 5px;
      max-height: 100px;
      width: auto;
      display: block;
      margin-left: auto;
      margin-right: auto;
    }

    .container-vote {
      position: absolute;
      top: 5px;
      display: flex;
      justify-content: center;
      gap: 10px;
      align-items: center;
    }

    .box-vote {
      width: 35px;
      height: 35px;
      background-color: #f5f5f5;
      border: 2px solid #000;
      border-radius: 10px;
      display: flex;
      justify-content: center;
      align-items: center;
      color: #fff;
      box-shadow: 4px 4px 10px rgba(0, 0, 0, 0.2);
    }

    .active-vote-blue {
      background-color: #002fff;
    }

    .active-vote-red {
      background-color: #ff0000;
    }
  </style>
</head>

<body>
  <button id="switchBtn" title="Switch AKA/AO"><i class="fas fa-retweet"></i></button>

  <div class="tatami" id="liveTatami">TATAMI <?= $_GET['tatami'] ?></div>
  <div class="description" id="matchDescription">Loading description...</div>

  <div class="player-header">
    <div class="player-namem" id="liveP1Name">AKA</div>
    <div class="logo-wrapper">
      <img id="forkiLogo" src="/logos/forki.png" alt="Forki Logo" />
    </div>
    <div class="player-nameb" id="liveP2Name">AO</div>
  </div>

  <div class="player-box" id="playerBox">
    <div class="player red" id="playerRed">
      <div class="player-club" id="liveP1Club">PERGURUAN</div>
      <div class="player-team" id="liveP1Team">DAERAH</div>
      <div class="scorem" id="liveScoreRed"><span id="liveScoreRedValue">0</span><span class="senshu"
          id="liveSenshuRed"></span></div>
      <div class="penalties" id="livePenaltyRed">Penalties: -</div>
      <img id="logoAka" class="club-logo" src="/logos/default.png" alt="Logo AKA">

    </div>

    <div class="player blue" id="playerBlue">
      <div class="player-club" id="liveP2Club">PERGURUAN</div>
      <div class="player-team" id="liveP2Team">DAERAH</div>
      <div class="scoreb" id="liveScoreBlue"><span id="liveScoreBlueValue">0</span><span class="senshu"
          id="liveSenshuBlue"></span></div>
      <div class="penalties" id="livePenaltyBlue">Penalties: -</div>
      <img id="logoAo" class="club-logo" src="/logos/default.png" alt="Logo AO">

    </div>
  </div>

  <div class="timer" id="liveTimer">3:00<sup style="font-size: 30px;">00</sup></div>

  <img id="akaYukoImage" src="/logos/akayuko.gif" alt="Aka Yuko" />
  <img id="aoYukoImage" src="/logos/aoyuko.gif" alt="Ao Yuko" />
  <img id="wazariImage" src="/logos/wazari.gif" alt="Wazari" />
  <img id="ipponImage" src="/logos/ippon.gif" alt="Ippon" />

  <script>
    let lastDescription = '';
    let prevRedScore = 0;
    let prevBlueScore = 0;
    let isSwitched = false;
    let isCalculated = false;
    let isCalculated2 = false;
    const durasiJuri = 1000;
    const tatami = localStorage.getItem('tatamiNumber') || '1';

    const redTim = document.getElementById("liveScoreRedValue")
    const blueTim = document.getElementById("liveScoreBlueValue")

    if (!localStorage.getItem("blueScore" + tatami) || !localStorage.getItem("redScore" + tatami)) {
      localStorage.setItem("blueScore" + tatami, "0");
      localStorage.setItem("redScore" + tatami, "0");
    }

    const blueScore = parseInt(localStorage.getItem("blueScore" + tatami));
    const redScore = parseInt(localStorage.getItem("redScore" + tatami));
    redTim.innerHTML = redScore;
    blueTim.innerHTML = blueScore;

    const juri = JSON.parse(localStorage.getItem("juri" + tatami)) || [];

    const host =
      "wss://93eedc74ea8c4eb0acfa05709199be1f.s1.eu.hivemq.cloud:8884/mqtt";
    const options = {
      username: "Remote",
      password: "Raflysaputra23",
      connectTimeout: 5000,
    };
    const client = mqtt.connect(host, options);
    client.on("connect", () => {
      console.log("✅ Terhubung ke HiveMQ Cloud!");
      client.subscribe("scorekata/data");
      client.publish("scorekata/kontrol", JSON.stringify({ tatami }));
    });
    client.on("message", (topic, message) => {
      try {
        const item = JSON.parse(message.toString());
        const timerPlay = localStorage.getItem("timerPlay");
        const tatami = localStorage.getItem("tatamiNumber");
        const juri = JSON.parse(localStorage.getItem("juri" + tatami)) || [];

        if (tatami != <?= $_GET['tatami'] ?>) return true;
        if (item.tatami != tatami) return true;

        if (item.team == "reset") {
          if (item.deviceId == "juri-1" && timerPlay == "false") {
            localStorage.setItem("blueScore" + tatami, "0");
            localStorage.setItem("redScore" + tatami, "0");
            localStorage.setItem("timer" + tatami, "180000");
            localStorage.setItem("juri" + tatami, JSON.stringify([]));
            // localStorage.setItem("historyJuri" + tatami, JSON.stringify([]));
            redTim.innerHTML = 0;
            blueTim.innerHTML = 0;
            juri.length = 0;
            vote = 0;
          }
          return true;
        }

        if (timerPlay == "false") return true;

        //   NGECEK JURI UDAH VOTE ATAU BELUM
          if(juri.some(j => j.deviceId == item.deviceId)) return true

        // CEK JIKA JURI UDAH VOTE KEEMPATNYA MAKA JANGAN DITAMBAH LAGI
        if (juri.length > 4) return false;

        juri.push({
          deviceId: item.deviceId,
          votes: item.team,
          score: item.score,
          time: localStorage.getItem("timer" + tatami),
        });

        localStorage.setItem("juri" + tatami, JSON.stringify(juri));

        if (juri.length >= 2) {
          const scoreBlue = [];
          const scoreRed = [];

          juri.forEach((j) => {
            if (j.votes == "blue") {
              scoreBlue.push(j.score);
            } else {
              scoreRed.push(j.score);
            }
          });

          let timeout1 = null;
          if(scoreBlue.length >= 1 && scoreRed.length >= 1 && !isCalculated2) {
             timeout1 = setTimeout(() => {
                isCalculated2 = false;
                localStorage.setItem("juri" + tatami, JSON.stringify([]));
                clearTimeout(timeout1);
             }, durasiJuri)
          }

          // CEK JIKA TIM BLUE ATAU TIM RED LEBIH DARI SATU VOTING MASUK KE LOGIKA
          if ((scoreBlue.length > 1 || scoreRed.length > 1) && !isCalculated) {
            isCalculated = true;
            clearTimeout(timeout1);
            const timeout = setTimeout(() => {
              isCalculated = false;
              const latestBlue = [];
              const latestRed = [];

              juri.forEach((j) => {
                if (j.votes === "blue") latestBlue.push(Number(j.score));
                if (j.votes === "red") latestRed.push(Number(j.score));
              });

              const blueScore = parseInt(localStorage.getItem("blueScore" + tatami));
              const redScore = parseInt(localStorage.getItem("redScore" + tatami));

              if (latestBlue.length > 1) {
                const maxBlue = Math.max(...latestBlue);
                const total = blueScore + maxBlue;
                blueTim.innerHTML = total;
                localStorage.setItem("blueScore" + tatami, total);
              }

              if (latestRed.length > 1) {
                const maxRed = Math.max(...latestRed);
                const total = redScore + maxRed;
                redTim.innerHTML = total;
                localStorage.setItem("redScore" + tatami, total);
              }

              // RESET
              juri.length = 0;
              localStorage.setItem("juri" + tatami, JSON.stringify([]));
              localStorage.setItem("timerPlay", false);
              clearTimeout(timeout);
            }, 700);
          }
        }

      } catch (e) {
        console.log(e);
        console.log(message.toString());
      }
    });
    client.on("error", (err) => {
      console.log("❌ Gagal terhubung!");
      console.error(err);
    });

    function renderPenalties(penalties) {
      if (!penalties || penalties.length === 0) return "-";
      return penalties
        .map(
          (p) =>
            `<span class="penalty-box ${p === "H" ? "red-box" : "yellow-box"
            }">${p}</span>`
        )
        .join(" ");
    }

    function showImage(id) {
      const img = document.getElementById(id);
      if (!img) return;
      img.style.display = "block";
      setTimeout(() => (img.style.display = "none"), 1500);
    }

    function fetchLiveData() {
      const tatami = localStorage.getItem('tatamiNumber') || '1';
      const player1 = JSON.parse(localStorage.getItem('player1')) || {};
      const player2 = JSON.parse(localStorage.getItem('player2')) || {};
      const redScore = parseInt(localStorage.getItem('redScore' + tatami)) || 0;
      const blueScore = parseInt(localStorage.getItem('blueScore' + tatami)) || 0;
      const timer = parseInt(localStorage.getItem('timer' + tatami)) || 180000;
      const senshu = localStorage.getItem('senshu') || null;
      const penaltiesRed = JSON.parse(localStorage.getItem('penaltiesRed')) || [];
      const penaltiesBlue = JSON.parse(localStorage.getItem('penaltiesBlue')) || [];

      const redName = isSwitched ? player2.name : player1.name;
      const redClub = isSwitched ? player2.club : player1.club;
      document.getElementById('logoAka').src = `/logos/${getClubLogoFilename(redClub)}`;
      const redTeam = isSwitched ? player2.team : player1.team;

      const blueName = isSwitched ? player1.name : player2.name;
      const blueClub = isSwitched ? player1.club : player2.club;
      document.getElementById('logoAo').src = `/logos/${getClubLogoFilename(blueClub)}`;
      const blueTeam = isSwitched ? player1.team : player2.team;

      const redScoreVal = isSwitched ? blueScore : redScore;
      const blueScoreVal = isSwitched ? redScore : blueScore;

      const redPenalties = isSwitched ? penaltiesBlue : penaltiesRed;
      const bluePenalties = isSwitched ? penaltiesRed : penaltiesBlue;

      const redSenshu = isSwitched ? 'Blue' : 'Red';
      const blueSenshu = isSwitched ? 'Red' : 'Blue';

      document.getElementById('liveP1Name').innerText = redName || 'PEMAIN 1';
      document.getElementById('liveP1Club').innerText = redClub || 'PERGURUAN';
      document.getElementById('liveP1Team').innerText = redTeam || 'DAERAH';

      document.getElementById('liveP2Name').innerText = blueName || 'PEMAIN 2';
      document.getElementById('liveP2Club').innerText = blueClub || 'PERGURUAN';
      document.getElementById('liveP2Team').innerText = blueTeam || 'DAERAH';

      document.getElementById('liveScoreRedValue').innerText = redScoreVal;
      document.getElementById('liveScoreBlueValue').innerText = blueScoreVal;
      // Setelah semua element.innerText di-set

      const nameRedEl = document.getElementById('liveP1Name');
      const nameBlueEl = document.getElementById('liveP2Name');
      const clubRedEl = document.getElementById('liveP1Club');
      const clubBlueEl = document.getElementById('liveP2Club');
      const teamRedEl = document.getElementById('liveP1Team');
      const teamBlueEl = document.getElementById('liveP2Team');

      // Warna teks sesuai posisi AKA/AO
      if (isSwitched) {
        nameRedEl.style.color = 'blue';
        nameBlueEl.style.color = 'red';

        clubRedEl.style.color = 'blue';
        clubBlueEl.style.color = 'red';

        teamRedEl.style.color = 'blue';
        teamBlueEl.style.color = 'red';
      } else {
        nameRedEl.style.color = 'red';
        nameBlueEl.style.color = 'blue';

        clubRedEl.style.color = 'red';
        clubBlueEl.style.color = 'blue';

        teamRedEl.style.color = 'red';
        teamBlueEl.style.color = 'blue';
      }

      // Ubah warna skor sesuai switch
      const scoreRedEl = document.getElementById('liveScoreRed');
      const scoreBlueEl = document.getElementById('liveScoreBlue');

      if (isSwitched) {
        scoreRedEl.style.color = 'blue';
        scoreBlueEl.style.color = 'red';
      } else {
        scoreRedEl.style.color = 'red';
        scoreBlueEl.style.color = 'blue';
      }



      document.getElementById('liveSenshuRed').innerText = senshu === redSenshu ? '✔️' : '';
      document.getElementById('liveSenshuBlue').innerText = senshu === blueSenshu ? '✔️' : '';

      document.getElementById('livePenaltyRed').innerHTML = renderPenalties(redPenalties);
      document.getElementById('livePenaltyBlue').innerHTML = renderPenalties(bluePenalties);

      document.getElementById('liveTatami').innerText = `TATAMI ${tatami}`;

      const timerElement = document.getElementById('liveTimer');
      let minutes = Math.floor(timer / 60000);
      let seconds = Math.floor((timer % 60000) / 1000);
      let milliseconds = Math.floor((timer % 1000) / 10);

      timerElement.innerHTML = `${minutes}:${seconds < 10 ? '0' : ''}${seconds}<sup style="font-size:30px;">${milliseconds < 10 ? '0' : ''}${milliseconds}</sup>`;

      if (timer <= 0) {
        timerElement.style.color = 'white';
        timerElement.classList.remove('blink');
      } else if (timer <= 15000) {
        timerElement.style.color = 'red';
        timerElement.classList.add('blink');
      } else {
        timerElement.style.color = 'yellow';
        timerElement.classList.remove('blink');
      }

      if (redScoreVal !== prevRedScore) {
        showImage(redScoreVal - prevRedScore === 1 ? 'akaYukoImage' : redScoreVal - prevRedScore === 2 ? 'wazariImage' : 'ipponImage');
        prevRedScore = redScoreVal;
      }

      if (blueScoreVal !== prevBlueScore) {
        showImage(blueScoreVal - prevBlueScore === 1 ? 'aoYukoImage' : blueScoreVal - prevBlueScore === 2 ? 'wazariImage' : 'ipponImage');
        prevBlueScore = blueScoreVal;
      }
    }


    function determineWinner() {
      const redScore = parseInt(document.getElementById('liveScoreRed').innerText) || 0;
      const blueScore = parseInt(document.getElementById('liveScoreBlue').innerText) || 0;
      const senshu = localStorage.getItem('senshu') || null;
      const redSenshu = isSwitched ? 'Blue' : 'Red';
      const blueSenshu = isSwitched ? 'Red' : 'Blue';

      const redIppon = parseInt(localStorage.getItem('redIppon')) || 0;
      const blueIppon = parseInt(localStorage.getItem('blueIppon')) || 0;
      const redWazari = parseInt(localStorage.getItem('redWazari')) || 0;
      const blueWazari = parseInt(localStorage.getItem('blueWazari')) || 0;

      const redScoreEl = document.getElementById('liveScoreRed');
      const blueScoreEl = document.getElementById('liveScoreBlue');

      // Reset blink
      redScoreEl.classList.remove('blink');
      blueScoreEl.classList.remove('blink');

      // 1. Cek selisih 8 poin
      if (Math.abs(redScore - blueScore) >= 8) {
        (redScore > blueScore ? redScoreEl : blueScoreEl).classList.add('blink');
        return;
      }

      // 2. Cek senshu jika skor sama
      if (redScore === blueScore) {
        if (senshu === redSenshu) {
          redScoreEl.classList.add('blink');
          return;
        } else if (senshu === blueSenshu) {
          blueScoreEl.classList.add('blink');
          return;
        }

        // 3. Jika tidak ada senshu, cek ippon
        if (redIppon !== blueIppon) {
          (redIppon > blueIppon ? redScoreEl : blueScoreEl).classList.add('blink');
          return;
        }

        // 4. Jika ippon sama, cek wazari
        if (redWazari !== blueWazari) {
          (redWazari > blueWazari ? redScoreEl : blueScoreEl).classList.add('blink');
          return;
        }

        // Skor benar-benar sama tanpa pembeda, tidak ada pemenang ditampilkan
        return;
      }

      // Jika tidak imbang, yang lebih tinggi menang
      (redScore > blueScore ? redScoreEl : blueScoreEl).classList.add('blink');
    }

    function checkDescriptionUpdate() {
      const newDescription = localStorage.getItem('matchDescription') || '';
      if (newDescription !== lastDescription) {
        document.getElementById('matchDescription').innerText = newDescription || 'No description available.';
        lastDescription = newDescription;
      }
    }

    document.getElementById('switchBtn').addEventListener('click', () => {
      isSwitched = !isSwitched;

      document.getElementById('playerRed').classList.toggle('red');
      document.getElementById('playerRed').classList.toggle('blue');
      document.getElementById('playerBlue').classList.toggle('red');
      document.getElementById('playerBlue').classList.toggle('blue');

      fetchLiveData();       // Perbarui tampilan data setelah switch
      determineWinner();     // Hitung ulang pemenang setelah switch
    });


    setInterval(() => {
      fetchLiveData();
      checkDescriptionUpdate();
      determineWinner();
    }, 100);
    function getClubLogoFilename(clubName) {
      if (!clubName) return 'default.png';

      const logoMap = {
        'ASKI': 'aski.png',
        'BKC': 'bkc.png',
        'BLACK PANTHER': 'blackpanther.png',
        'PORDIBYA': 'pordibya.png',
        'FUNAKOSHI': 'funakoshi.png',
        'GABDIKA': 'gabdika.png',
        'GOJUKAI': 'gojukai.png',
        'GOJU ASS': 'gojuass.png',
        'GOKASI': 'gokasi.png',
        'INKADO': 'inkado.png',
        'INKAI': 'inkai.png',
        'INKANAS': 'inkanas.png',
        'KALA HITAM': 'kalahitam.png',
        'KEI SHIN KAN': 'keishinkan.png',
        'KKNSI': 'kknsi.png',
        'KKI': 'kki.png',
        'KYOKUSHINKAI': 'kyokushinkai.png',
        'LEMKARI': 'lemkari.png',
        'SHOKAIDO': 'shokaido.png',
        'SHOTOKAI': 'shotokai.png',
        'PORBIKAWA': 'porbikawa.png',
        'SHINDOKA': 'shindoka.png',
        'SHI ROI TE': 'shiroite.png',
        'TAKO': 'tako.png',
        'WADOKAI': 'wadokai.png'
      };

      const normalized = clubName.trim().toUpperCase();
      return logoMap[normalized] || 'default.png';
    }

  </script>
</body>

</html>