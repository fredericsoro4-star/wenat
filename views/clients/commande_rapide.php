<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Maintenance</title>

<link rel="stylesheet" href="../../assets/styles/commande-rapide.css">
</head>

<body>

<div class="box">
    <h1>Maintenance</h1>
    <p>Le service est temporairement indisponible.</p>
    <p>Retour dans :</p>
    <div class="timer" id="timer">01:00:00</div>
</div>

<script>
    let time = 3600; // 1 heure

    setInterval(() => {
        let h = Math.floor(time / 3600);
        let m = Math.floor((time % 3600) / 60);
        let s = time % 60;

        document.getElementById("timer").textContent =
            (h < 10 ? "0" : "") + h + ":" +
            (m < 10 ? "0" : "") + m + ":" +
            (s < 10 ? "0" : "") + s;

        if (time > 0) time--;
    }, 1000);
</script>

</body>
</html>