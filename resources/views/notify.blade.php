<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <style>
     
.toast {
  position: absolute;
  top: 25px;
  right: 30px;
  border-radius: 5px;
  background: #fff;
  padding: 7px 35px 7px 25px;
  box-shadow: 0 6px 20px -5px rgba(0, 0, 0, 0.413);
  overflow: hidden;
  transform: translateX(calc(100% + 30px));
  transition: all 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.35);
  z-index: 7;
}

.toast.active {
  transform: translateX(0%);
}

.toast .toast-content {
  display: flex;
  align-items: center;
}

.toast-content .check {
  display: flex;
  align-items: center;
  justify-content: center;
  height: 35px;
  min-width: 35px;
  background-color: #4070f4;
  color: #fff;
  font-size: 20px;
  border-radius: 50%;
}

.toast-content .message {
  display: flex;
  flex-direction: column;
  margin: 0 20px;
}

.message .text {
  font-size: 16px;
  font-weight: 400;
  color: #666666;
}

.message .text.text-1 {
  font-weight: 600;
  color: #333;
}

.toast .close {
  position: absolute;
  top: 10px;
  right: 15px;
  padding: 5px;
  cursor: pointer;
  opacity: 0.7;
}

.toast .close:hover {
  opacity: 1;
}

.toast .progress {
  position: absolute;
  bottom: 0;
  left: 0;
  height: 3px;
  width: 100%;

}

.toast .progress:before {
  content: "";
  position: absolute;
  bottom: 0;
  right: 0;
  height: 100%;
  width: 100%;
  background-color: #4070f4;
}

.progress.active:before {
  animation: progress 5s linear forwards;
}

@keyframes progress {
  100% {
    right: 100%;
  }
}


    </style>
</head>
<body>
    <div class="toast active">
        <div class="toast-content">
          <i class="fas fa-solid fa-check check"></i>
          <div class="message">
            <span class="text text-1">Success</span>
            <span class="text text-2">{{ session('success') }}</span>
          </div>
        </div>
        <i class="fa-solid fa-xmark close"></i>
        <!-- Remove 'active' class, this is just to show in Codepen thumbnail -->
        <div class="progress active"></div>
      </div>
      {{-- <button>Show Toast</button>

      <small style="position: absolute; bottom: 40px">Remove 'active' classes, this is just to show in Codepen thumbnail</small> --}}


<script>
    const button = document.querySelector("button"),
  toast = document.querySelector(".toast");
(closeIcon = document.querySelector(".close")),
  (progress = document.querySelector(".progress"));
  nav = document.querySelector("nav");
let timer1, timer2;


  toast.classList.add("active");
  progress.classList.add("active");

  timer1 = setTimeout(() => {
    toast.style.display = "none";
    // nav.style.marginTop = "0";

  }, 5000); //1s = 1000 milliseconds

  timer2 = setTimeout(() => {
    progress.classList.remove("active");
  }, 5300);


closeIcon.addEventListener("click", () => {
  toast.style.display = "none";

  setTimeout(() => {
    progress.classList.remove("active");
  }, 300);

  clearTimeout(timer1);
  clearTimeout(timer2);
});

</script>


    </body>
</html>