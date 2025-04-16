<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8" />
  <title>📸 Galeria</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body {
	font-family: 'Poppins', sans-serif;
	background: #f0f0f0;
	text-align: center;
	}

    h1 {
      font-size: 35px;
      margin-top: 20px;
	  color: #252525;
    }

    .counter {
      font-size: 50px;
      color: #252525;
      font-weight: bold;
      margin: 25px 0;
      transition: transform 0.3s ease;
    }

    .counter span {
      display: inline-block;
      min-width: 50px;
    }

    .count-pulse {
      animation: pulse 0.5s ease;
    }

    @keyframes pulse {
      0% {
        transform: scale(1);
      }
      50% {
        transform: scale(1.2);
        color: #4e2fab;
        text-shadow: 0 0 10px #4e2fab;
      }
      100% {
        transform: scale(1);
      }
    }

    .gallery {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 30px;
      padding: 20px;
    }

    .image-box {
      display: flex;
      flex-direction: column;
      align-items: center;
      max-width: 800px;
      position: relative;
    }

    .gallery img {
      max-width: 100%;
      height: auto;
      border-radius: 22px;
      transition: transform 0.2s;
    }

    .gallery img:hover {
      transform: scale(1.03);
    }

    .date {
      font-size: 18px;
      color: #252525;
      font-weight: bold;
	  margin: 5px 0;
    }
	  .size {
	  color: #4e2fab;
	  font-weight: bold;
	  margin: 5px 0;
	}

    .toggle-btn {
      margin: 10px;
      padding: 10px 20px;
      background-color: #27ae60;
      color: white;
      border: none;
      border-radius: 5px;
	  font-family: 'Poppins', sans-serif;
      font-size: 16px;
      cursor: pointer;
    }

    .toggle-btn:hover {
      background-color: #1e8449;
    }

    #toast {
      visibility: hidden;
      min-width: 250px;
      background-color: #2ecc71;
      color: white;
      text-align: center;
      border-radius: 5px;
      padding: 12px 20px;
      position: fixed;
      top: 20px;
      right: 20px;
      z-index: 999;
      font-size: 16px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
      opacity: 0;
      transition: visibility 0s, opacity 0.5s ease-in-out;
    }

    #toast.show {
      visibility: visible;
      opacity: 1;
    }
	.bg-blur {
	position: fixed;
	top: 0;
	left: 0;
	width: 100vw;
	height: 100vh;
	background-position: center;
	background-size: cover;
	filter: blur(50px);
	z-index: -1;
	opacity: 0.4;
	}
  </style>
</head>
<body>
  <h1>📸 Galeria</h1>

	<div id="toast">📸 Dodano nowe zdjęcia!</div>

	<div id="galeria-zdjec">Wczytywanie zdjęć...</div>

	<div id="blur-bg" class="bg-blur"></div>
	
<form action="download.php" method="post" style="margin-bottom: 30px;">
  <button type="submit" class="toggle-btn">📦 Pobierz wszystkie zdjęcia (ZIP)</button>
</form>

<script>
function setBackgroundFromNewestImage() {
  const bgInfo = document.getElementById("background-image");
  const blur = document.getElementById("blur-bg");
  if (bgInfo && blur) {
    const url = bgInfo.dataset.src;
    blur.style.backgroundImage = `url('${url}')`;
  }
}

function loadGallery() {
  fetch("gallery.php")
    .then(response => response.text())
    .then(html => {
      const container = document.getElementById("galeria-zdjec");
      const oldCount = parseInt(document.getElementById("count")?.textContent || "0");

      const temp = document.createElement("div");
      temp.innerHTML = html;

      const newCount = parseInt(temp.querySelector("#count")?.textContent || "0");

      container.innerHTML = html;

      if (oldCount !== newCount) {
        animateCounter(oldCount, newCount);
        if (newCount > oldCount) {
          showToast("📸 Dodano " + (newCount - oldCount) + " nowych zdjęć!");
        }
      }

      setBackgroundFromNewestImage();
    });
}

loadGallery();
setInterval(loadGallery, 5000);

</script>
<script>
    function animateCounter(from, to, duration = 1000) {
      const element = document.getElementById("count");
      if (!element) return;

      const startTime = performance.now();

      function update(currentTime) {
        const progress = Math.min((currentTime - startTime) / duration, 1);
        const currentValue = Math.floor(from + (to - from) * progress);
        element.textContent = currentValue;

        if (progress < 1) {
          requestAnimationFrame(update);
        } else {
          element.textContent = to;
        }
      }

      element.classList.remove("count-pulse");
      void element.offsetWidth;
      element.classList.add("count-pulse");

      requestAnimationFrame(update);
    }

    function showToast(message = "📸 Dodano nowe zdjęcia!") {
      const toast = document.getElementById("toast");
      toast.textContent = message;
      toast.classList.add("show");

      setTimeout(() => {
        toast.classList.remove("show");
      }, 4000);
    }
</script>

</body>
</html>
