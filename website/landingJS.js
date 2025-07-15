document.addEventListener("DOMContentLoaded", () => {
  const carousel = document.getElementById("carouselImages");
  const images = carousel.querySelectorAll("img");
  const totalImages = images.length;
  let currentIndex = 0;
  let intervalId;
  let inactivityTimeout;
  const AUTO_INTERVAL = 5000; // 5 seconds between slides
  const INACTIVITY_RESET_TIME = 5000; // 5 seconds to resume autoplay

  function updateCarousel() {
    carousel.style.transform = `translateX(-${currentIndex * 100}%)`;
  }

  function showNextImage() {
    currentIndex = (currentIndex + 1) % totalImages;
    updateCarousel();
  }

  function startAutoSlide() {
    clearInterval(intervalId);
    intervalId = setInterval(showNextImage, AUTO_INTERVAL);
  }

  function stopAutoSlideTemporarily() {
    clearInterval(intervalId); // Stop current auto loop
    clearTimeout(inactivityTimeout); // Reset inactivity timeout
    inactivityTimeout = setTimeout(() => {
      startAutoSlide(); // Resume auto after inactivity period
    }, INACTIVITY_RESET_TIME);
  }

  document.getElementById("prevBtn").addEventListener("click", () => {
    currentIndex = (currentIndex - 1 + totalImages) % totalImages;
    updateCarousel();
    stopAutoSlideTemporarily();
  });

  document.getElementById("nextBtn").addEventListener("click", () => {
    currentIndex = (currentIndex + 1) % totalImages;
    updateCarousel();
    stopAutoSlideTemporarily();
  });

  // Start autoplay on load
  startAutoSlide();
});
