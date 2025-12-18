<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tracking | Tatami <?= $_GET['tatami'] ?></title>
    <link rel="stylesheet" href="./style/output.css">
    <script src="https://unpkg.com/mqtt/dist/mqtt.min.js"></script>
</head>

<body class="overflow-hidden">
    <div class="flex flex-col gap-10 items-center h-screen py-6">
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
        tatamiNumber.innerHTML = "Tatami " + tatami;
        let isCalculated = false;
        let isCalculated2 = false;
        const durasiJuri = 1000;

        const juri = JSON.parse(localStorage.getItem("juri" + tatami)) || [];
        const historyJuri = JSON.parse(localStorage.getItem("historyJuri" + tatami)) || [];
        let vote = juri.length;

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

        const interval = setInterval(() => {
            fetchDataJuri();
        }, 1000);

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
        }

        const fetchDataJuri = () => {
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
            client.publish("scorekata/kontrol", JSON.stringify({ tatami: localStorage.getItem("tatamiNumber") }));
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

                if (timerPlay == "false") return true;


                //   NGECEK JURI UDAH VOTE ATAU BELUM
                if (juri.some(j => j.deviceId == item.deviceId)) return true

                box.forEach((box) => {
                    const dataJuri = box.dataset.juri;
                    if (dataJuri == item.deviceId) {
                        box.classList.add(
                            item.team == "blue" ? "bg-blue-500" : "bg-red-500"
                        );
                    }
                });

                if (historyJuri.length == 0) history.innerHTML = ``;

                history.innerHTML += `
                <div class="bg-slate-300 flex items-center justify-between rounded-md p-2 px-3">
                    <div class="flex flex-col items-start">
                        <h1 class="font-bold text-2xl">Juri ${item.deviceId.split("-")[1]}</h1>
                        <p class="text-sm text-slate-800">Memberikan voting</p>
                    </div>
                    <div class="flex flex-col items-end">
                        <div class="w-8 h-8 rounded-md ${item.team == "blue" ? "bg-blue-500" : "bg-red-500"} flex items-center justify-center text-white">${item.score}</div>
                        <p class="text-sm text-slate-800 font-bold">${formatTime(Number(localStorage.getItem("timer" + tatami)))}</p>
                    </div>
                </div>               
                    `;

                history.scrollTop = history.scrollHeight;



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
                localStorage.setItem("historyJuri" + tatami, JSON.stringify(historyJuri));
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
                    if (scoreBlue.length >= 1 && scoreRed.length >= 1 && !isCalculated2) {
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
                                alert(`Biru plus ${maxBlue}!`);
                            }

                            if (latestRed.length > 1) {
                                const maxRed = Math.max(...latestRed);
                                alert(`Merah plus ${maxRed}!`);
                            }

                            // RESET
                            juri.length = 0;
                            vote = 0;
                            clearTimeout(timeout);
                            box.forEach((box) => {
                                box.classList.remove("bg-red-500");
                                box.classList.remove("bg-blue-500");
                            });
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
    </script>
</body>

</html>