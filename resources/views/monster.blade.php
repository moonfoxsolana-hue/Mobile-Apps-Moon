<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mystic Nusa Landing</title>
  <style>
    body {
      margin: 0;
      background-color: #000;
    }

    .container {
      position: relative;
      width: 100vw;
      height: 100vh;
      background-color: #000;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .monster-wrapper {
      position: relative;
      width: 100vw;
      max-width: 1200px;
    }

    .monster-img {
      width: 100%;
      height: auto;
      display: block;
    }

    .eye {
      position: absolute;
      width: 5%;
      aspect-ratio: 1/1;
      background-image: "images/noneyeball.png";
      background-size: cover;
      background-position: center;
      border-radius: 50%;
      overflow: hidden;
    }

    .left-eye {
      top: 30.5%;
      left: 33.5%;
    }

    .right-eye {
      top: 31%;
      left: 40%;
    }

    .pupil {
      position: absolute;
      width: 70%;
      height: 70%;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      pointer-events: none;
      z-index: 1;
    }

    .eyelid {
      position: absolute;
      top: 0;
      left: 0;
      width: 80%;
      height: 80%;
      background-color: #000;
      border-radius: 50%;
      animation: blink s infinite;
      transform-origin: top center;
      z-index: 2;
      opacity: 0;
    }

    .left-eye .eyelid {
      animation-delay: 1s;
    }

    .right-eye .eyelid {
      animation-delay: 2s;
    }

    @keyframes blink {
      0%, 90%, 100% {
        opacity: 0;
        transform: scaleY(0);
      }
      95% {
        opacity: 1;
        transform: scaleY(1);
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="monster-wrapper">
      <img src="{{ asset('images/monster.jpg') }}" class="monster-img" alt="Monster">
      <div class="eye left-eye">
        <img src="{{ asset('images/pupil.png') }}" class="pupil">
        <div class="eyelid"></div>
      </div>
      <div class="eye right-eye">
        <img src="{{ asset('images/pupil.png') }}" class="pupil">
        <div class="eyelid"></div>
      </div>
    </div>
  </div>

  <script>
    document.addEventListener('mousemove', (e) => {
      const eyes = document.querySelectorAll('.eye');
      eyes.forEach(eye => {
        const pupil = eye.querySelector('.pupil');
        const rect = eye.getBoundingClientRect();
        const eyeX = rect.left + rect.width / 1;
        const eyeY = rect.top + rect.height / 1;

        const angle = Math.atan2(e.clientY - eyeY, e.clientX - eyeX);
        const offsetX = Math.cos(angle) * 3;
        const offsetY = Math.sin(angle) * 3;

        pupil.style.transform = `translate(calc(-50% + ${offsetX}px), calc(-50% + ${offsetY}px))`;
      });
    });
  </script>
</body>
</html>
