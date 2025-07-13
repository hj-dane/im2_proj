document.addEventListener('DOMContentLoaded', () => {
    let index = 0;
    const images = document.getElementById('carouselImages');
    const totalSlides = images.children.length;
  
    function showSlide(i) {
      index = (i + totalSlides) % totalSlides;
      images.style.transform = `translateX(${-index * 100}%)`;
    }
  
    function nextSlide() {
      showSlide(index + 1);
    }
  
    function prevSlide() {
      showSlide(index - 1);
    }
  
    document.getElementById('nextBtn').addEventListener('click', nextSlide);
    document.getElementById('prevBtn').addEventListener('click', prevSlide);
  
    // Auto-slide every 5 seconds
    setInterval(nextSlide, 5000);
  });
  