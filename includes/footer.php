<!-- Footer -->
<section class="footer bg-primary text-light py-3 d-flex justify-content-center align-items-center">
  <span class="text-center">Copyrights © 2025 K.N Raam | All rights reserved</span>
</section>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" 
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" 
        crossorigin="anonymous"></script>

<!-- Swiper JS -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<!-- Swiper Initialization -->
<script>
  var swiper = new Swiper(".swiper-container", {
    spaceBetween: 30,
    effect: "fade",
    loop: true,
    navigation: { nextEl: ".swiper-button-next", prevEl: ".swiper-button-prev" },
    pagination: { el: ".swiper-pagination", clickable: true },
  });

  var swiperTestimonials = new Swiper(".swiper-testimonials", {
    effect: "coverflow",
    grabCursor: true,
    centeredSlides: true,
    slidesPerView: "auto",
    loop: true,
    coverflowEffect: {
      rotate: 50, stretch: 0, depth: 100, modifier: 1, slideShadows: true,
    },
    pagination: { el: ".swiper-pagination" },
    breakpoints: {
      320: { slidesPerView: 1 },
      640: { slidesPerView: 1 },
      768: { slidesPerView: 2 },
      1024: { slidesPerView: 3 },
    }
  });

  var swiperProducts = new Swiper(".mySwiper", {
    slidesPerView: 4,
    loop: true,
    spaceBetween: 40,
    pagination: { el: ".swiper-pagination", dynamicBullets: true },
    breakpoints: {
      320: { slidesPerView: 1 },
      640: { slidesPerView: 1 },
      768: { slidesPerView: 3 },
      1024: { slidesPerView: 3 },
    }
  });
</script>
</body>
</html>
