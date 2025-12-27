<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tracking | Tatami <?= $_GET['tatami'] ?></title>
    <link rel="stylesheet" href="./style/output.css">
    <script src="https://unpkg.com/mqtt/dist/mqtt.min.js"></script>

    <!-- ICONS -->
    <link href='https://cdn.boxicons.com/3.0.6/fonts/basic/boxicons.min.css' rel='stylesheet'>
</head>

<body class="overflow-hidden">
    <div id="app" class="flex flex-col gap-10 items-center h-screen py-6 relative">
        <div class="mt-15 flex flex-col items-center">
            <h1 class="text-3xl font-bold">HALAMAN TRACKING JURI</h1>
            <h2 id="tatamiNumber" class="text-2xl">"Tatami <?= $_GET['tatami'] ?>"</h2>
        </div>
        <section class="flex flex-col items-center gap-4">
            <div id="boxScore" class="flex items-center gap-4">
                <div class="flex flex-col items-center gap-1">
                    <div data-juri="juri-1" class="box w-16 h-16 rounded-lg border border-black"></div>
                    <p class="text-sm font-bold">Juri 1</p>
                </div>
                <div class="flex flex-col items-center gap-1">
                    <div data-juri="juri-2" class="box w-16 h-16 rounded-lg border border-black"></div>
                    <p class="text-sm font-bold">Juri 2</p>
                </div>
                <div class="flex flex-col items-center gap-1">
                    <div data-juri="juri-3" class="box w-16 h-16 rounded-lg border border-black"></div>
                    <p class="text-sm font-bold">Juri 3</p>
                </div>
                <div class="flex flex-col items-center gap-1">
                    <div data-juri="juri-4" class="box w-16 h-16 rounded-lg border border-black"></div>
                    <p class="text-sm font-bold">Juri 4</p>
                </div>
            </div>
            <div class="flex flex-col items-center gap-1">
                <div id="boxReset" class="w-16 h-16 rounded-lg border border-black"></div>
                <p class="text-sm font-bold">Reset</p>
            </div>
        </section>
        <section id="history"
            class="w-[95%] flex flex-col items-stretch max-w-125 w-125 p-4 rounded-lg gap-2 overflow-y-auto h-full shadow-[1px_2px_2px_rgba(0,0,0,0.3)]">
            <div class="w-full h-full flex items-center justify-center">
                <h1 class="text-2xl font-bold">Belum ada history</h1>
            </div>
        </section>
    </div>

    <script>
        const boxScore = document.getElementById("boxScore");
        const box = boxScore.querySelectorAll(".box");
        const boxReset = document.getElementById("boxReset");
        const history = document.getElementById("history");
        const tatami = localStorage.getItem("tatamiNumber") || '1';
        const tatamiNumber = document.getElementById("tatamiNumber");
        const root = document.getElementById("app");
        tatamiNumber.innerHTML = "Tatami " + tatami;
        localStorage.setItem("juri" + tatami, JSON.stringify([]));

        let isCalculated1 = false;
        let isCalculated2 = false;
        let timeout1 = null;
        let isCoolingDown = false;
        const durasiJuri = 1500;
        loadVoteJuri();
        loadHistoryJuri();
        history.scrollTop = history.scrollHeight;

        const delay = (ms) => new Promise(resolve => setTimeout(resolve, ms));

        async function handleRemote() {
            if (isCoolingDown) return;
            isCoolingDown = true;

            await delay(3000);

            isCoolingDown = false;
        }


        const interval = setInterval(() => {
            loadVoteJuri();
            loadHistoryJuri();
        }, 1000);

        const host =
            "wss://93eedc74ea8c4eb0acfa05709199be1f.s1.eu.hivemq.cloud:8884/mqtt";
        const options = {
            username: "Remote",
            password: "Raflysaputra23",
            connectTimeout: 5000,
        };
        const client = mqtt.connect(host, options);
        client.on("connect", () => {
            localStorage.setItem("remote", JSON.stringify([]));
            console.log("✅ Berhasil terhubung ke cloud");
            client.subscribe("scorekata/data");
            client.subscribe("scorekata/koneksi");
            client.subscribe("scorekata/koneksi2");
            client.publish("scorekata/kontrol", JSON.stringify({ tatami }));
        });
        client.on("message", async (topic, message) => {
            try {
                if (topic == "scorekata/koneksi2") {
                    const item = JSON.parse(message.toString());
                    const remote = JSON.parse(localStorage.getItem("remote")) || [];
                    if (!remote.includes(item.deviceId)) {
                        remote.push(item.deviceId);
                        localStorage.setItem("remote", JSON.stringify(remote));
                        // showPopup(`Remote ${item.deviceId} terhubung ke tatami ${item.tatami}!!`, 'bg-green-500', 3000);
                    }
                } else if (topic == "scorekata/koneksi") {
                    const item = JSON.parse(message.toString());
                    if (item.status == "online") {
                        const remote = JSON.parse(localStorage.getItem("remote")) || [];
                        if (!remote.includes(item.deviceId)) {
                            remote.push(item.deviceId);
                            localStorage.setItem("remote", JSON.stringify(remote));
                        }
                        client.publish("scorekata/kontrol", JSON.stringify({ tatami }));
                        // showPopup(`Remote ${item.deviceId} terhubung ke tatami ${item.tatami}!!`, 'bg-green-500', 3000);
                    } else {
                        const remote = JSON.parse(localStorage.getItem("remote")) || [];
                        if (remote.includes(item.deviceId)) {
                            const newRemote = remote.filter((r) => r != item.deviceId);
                            localStorage.setItem("remote", JSON.stringify(newRemote));
                            // showPopup(`Remote ${item.deviceId} terputus!!`, 'bg-red-500', 3000);
                        }
                    }
                } else if (topic == "scorekata/data") {
                    const item = JSON.parse(message.toString());
                    const tatami = localStorage.getItem("tatamiNumber");
                    const timerPlay = localStorage.getItem("timerPlay");
                    const juri = JSON.parse(localStorage.getItem("juri" + tatami)) || [];
                    const historyJuri = JSON.parse(localStorage.getItem("historyJuri" + tatami)) || [];
                    // if (item.tatami != tatami) return true;

                    // if(isCoolingDown) return false;
                    // handleRemote();

                    if (item.team == "reset") {
                        if (item.deviceId == "juri-1" && timerPlay == "false") {
                            localStorage.setItem("blueScore" + tatami, "0");
                            localStorage.setItem("redScore" + tatami, "0");
                            localStorage.setItem("timer" + tatami, "180000");
                            localStorage.setItem("juri" + tatami, JSON.stringify([]));
                            // localStorage.setItem("historyJuri" + tatami, JSON.stringify([]));
                            box.forEach((box) => {
                                box.classList.remove("bg-red-500");
                                box.classList.remove("bg-blue-500");
                            });
                            document.getElementById('boxReset').classList.add("bg-yellow-500");
                            const timeout = setTimeout(() => {
                                document.getElementById('boxReset').classList.remove("bg-yellow-500");
                            }, 1000);
                        }
                        return true;
                    }

                    //   NGECEK TIMER KLO UDAH BERHENTI JANGAN VOTE
                    if (timerPlay == "false") return true;

                    //   NGECEK JURI UDAH VOTE ATAU BELUM
                    if (juri.some(j => j.deviceId == item.deviceId)) return true

                    // CEK JIKA JURI UDAH VOTE KEEMPATNYA MAKA JANGAN DITAMBAH LAGI
                    if (juri.length > 4) return false;

                    const dataJuri = {
                        deviceId: item.deviceId,
                        votes: item.team,
                        score: item.score,
                        time: localStorage.getItem("timer" + tatami),
                    };

                    historyJuri.push(dataJuri);
                    juri.push(dataJuri);
                    localStorage.setItem("juri" + tatami, JSON.stringify(juri));
                    localStorage.setItem("historyJuri" + tatami, JSON.stringify(historyJuri));

                    // LOAD DATA
                    loadHistoryJuri();
                    loadVoteJuri();
                    history.scrollTop = history.scrollHeight;

                    if (juri.length >= 1) {
                        const scoreBlue = [];
                        const scoreRed = [];

                        juri.forEach((j) => {
                            if (j.votes == "blue") {
                                scoreBlue.push(j.score);
                            } else {
                                scoreRed.push(j.score);
                            }
                        });

                        if ((scoreBlue.length > 0 || scoreRed.length > 0) && !isCalculated1) {
                            isCalculated1 = true;
                            timeout1 = setTimeout(() => {
                                isCalculated1 = false;
                                localStorage.setItem("juri" + tatami, JSON.stringify([]));
                                loadVoteJuri();
                                clearTimeout(timeout1);
                            }, durasiJuri);
                        }

                        // CEK JIKA TIM BLUE ATAU TIM RED LEBIH DARI SATU VOTING MASUK KE LOGIKA
                        if ((scoreBlue.length > 1 || scoreRed.length > 1) && !isCalculated2) {
                            isCalculated2 = true;
                            isCalculated1 = false;
                            clearTimeout(timeout1);
                            const timeout2 = setTimeout(() => {
                                isCalculated2 = false;
                                const juri = JSON.parse(localStorage.getItem("juri" + tatami)) || [];
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
                                    const blueScore = parseInt(localStorage.getItem("blueScore" + tatami)) || 0;
                                    const total = blueScore + maxBlue;
                                    localStorage.setItem("blueScore" + tatami, total);
                                    showPopup(`Biru plus ${maxBlue}!`, 'bg-blue-500');
                                }

                                if (latestRed.length > 1) {
                                    const maxRed = Math.max(...latestRed);
                                    const redScore = parseInt(localStorage.getItem("redScore" + tatami)) || 0;
                                    const total = redScore + maxRed;
                                    localStorage.setItem("redScore" + tatami, total);
                                    showPopup(`Merah plus ${maxRed}!`, 'bg-red-500');
                                }

                                // RESET
                                localStorage.setItem("juri" + tatami, JSON.stringify([]));
                                localStorage.setItem("timerPlay", false);
                                clearTimeout(timeout2);
                                loadVoteJuri();
                            }, durasiJuri);
                        }
                    }
                }
            } catch (e) {
                console.log("Error: ", e);
            }
        });
        client.on("error", (err) => {
            console.log("❌ Gagal terhubung!");
            console.error(err);
        });

        function getPopupContainer() {
            let container = document.getElementById("popup-container");

            if (!container) {
                container = document.createElement("div");
                container.id = "popup-container";
                container.className =
                    "fixed top-0 left-0 right-0 bottom-0 pointer-events-none flex flex-col items-center justify-center gap-3 bg-black/50";
                document.body.appendChild(container);
            }

            return container;
        }


        function showPopup(text, bg, duration = 1500) {
            const container = getPopupContainer();

            const popup = document.createElement("div");
            popup.className =
                "w-96 bg-white p-2 rounded-lg shadow animate-fade transition-opacity";

            popup.innerHTML = `
    <h1 class="text-3xl text-white text-center font-bold ${bg} p-2 px-3 rounded-md">
      ${text}
    </h1>
  `;

            container.appendChild(popup);

            setTimeout(() => {
                popup.classList.add("opacity-0");

                setTimeout(() => {
                    popup.remove();

                    if (container.children.length === 0) {
                        container.classList.add("opacity-0");
                        setTimeout(() => container.remove(), 300);
                    }
                }, 300);
            }, duration);
        }





        function formatTime(ms) {
            const totalSeconds = Math.floor(ms / 1000);
            const minutes = Math.floor(totalSeconds / 60);
            const seconds = totalSeconds % 60;

            return (
                String(minutes).padStart(2, "0") +
                ":" +
                String(seconds).padStart(2, "0")
            );
        }

        function loadVoteJuri() {
            const juri = JSON.parse(localStorage.getItem("juri" + tatami)) || [];
            if (juri.length > 0) {
                juri.forEach((j, index) => {
                    box.forEach((box) => {
                        const dataJuri = box.dataset.juri;
                        if (dataJuri == j.deviceId) {
                            box.classList.add(j.votes == "blue" ? "bg-blue-500" : "bg-red-500");
                        }
                    });
                });
            } else {
                box.forEach((box) => {
                    box.classList.remove("bg-blue-500");
                    box.classList.remove("bg-red-500");
                });
            }
        }

        function loadHistoryJuri() {
            const historyJuri = JSON.parse(localStorage.getItem("historyJuri" + tatami)) || [];
            if (historyJuri.length > 0) {
                history.innerHTML = "";
                historyJuri.forEach((h, index) => {
                    history.innerHTML += `
                <div class="bg-slate-300 flex items-center justify-between rounded-md p-2 px-3">
                    <div class="flex flex-col items-start">
                        <h1 class="font-bold text-2xl">Juri ${h.deviceId.split("-")[1]}</h1>
                        <p class="text-sm text-slate-800">Memberikan voting</p>
                    </div>
                    <div class="flex flex-col items-end">
                        <div class="w-8 h-8 rounded-md ${h.votes == "blue" ? "bg-blue-500" : "bg-red-500"} flex items-center justify-center text-white">${h.score}</div>
                        <p class="text-sm text-slate-800 font-bold">${formatTime(Number(h.time))}</p>
                    </div>
                </div>     
                    `;
                });
            } else {
                history.innerHTML = `
                <div class="w-full h-full flex items-center justify-center">
                    <h1 class="text-2xl font-bold">Belum ada history</h1>
                </div>
                `;
            }
        }
    </script>
</body>

</html>