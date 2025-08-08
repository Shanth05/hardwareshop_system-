<section class="footer">
<span>Copyrights © 2025 K.N Raam | all right reserved    </span>
 



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    <!-- Initialize Swiper -->
  <script>
    var swiper = new Swiper(".swiper-container", {
      spaceBetween: 30,
      effect: "fade",
      loop:true,
      navigation: {
        nextEl: ".swiper-button-next",
        prevEl: ".swiper-button-prev",
      },
      pagination: {
        el: ".swiper-pagination",
        clickable: true,
      },
    });

    var swiper = new Swiper(".swiper-testimonials", {
      effect: "coverflow",
      grabCursor: true,
      centeredSlides: true,
      slidesPerView: "auto",
      slidesPerView: "3",
      loop:true,
      coverflowEffect: {
        rotate: 50,
        stretch: 0,
        depth: 100,
        modifier: 1,
        slideShadows: true,
      },
      pagination: {
        el: ".swiper-pagination",
      },
      breakpoints:{
        320:{
          slidesPerView: 1,
        },

        640:{
          slidesPerView: 1,
        },

        768:{
          slidesPerView: 2,
        },

        1024:{
          slidesPerView: 3,
        },
      }
    });

    var swiper = new Swiper(".mySwiper", {
    slidesPerView:4,
    loop:true,
    spaceBetween:40,
    pagination: {
      el: ".swiper-pagination",
      dynamicBullets: true,
      loop: true,
    },
    breakpoints:{
        320:{
          slidesPerView: 1,
        },

        640:{
          slidesPerView: 1,
        },

        768:{
          slidesPerView: 3,
        },

        1024:{
          slidesPerView: 3,
        },
      }
  });
  </script>



</section>
</body>
</html>