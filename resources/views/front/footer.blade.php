<!-- Footer Start -->
<div class="container-fluid bg-dark text-light footer mt-5 pt-5">
  <div class="container py-5">
    <div class="row g-5">
      <div class="col-lg-3 col-md-6">
        <h5 class="text-light mb-4">{{ __('dictt.address') }} - 1</h5>
        <p class="mb-2"><i class="fa fa-map-marker-alt me-3"></i>Karaçalı Mahallesi Şehit Hamza Atakul Caddesi No:39-A
          Dalaman/Muğla</p>
        <p class="mb-2"><i class="fa fa-phone-alt me-3"></i>Dalaman: (530) 828 4884</p>
        <p class="mb-2"><i class="fa fa-envelope me-3"></i>learnenglishwithala@gmail.com</p>
        <div class="d-flex pt-2">
          <a class="btn btn-outline-light btn-social rounded-circle" href="https://www.instagram.com/aydindilakademisidalaman?igsh=MTVjaXl2eDJ2MjJwYg==" target="_blank"><i class="fab fa-instagram"></i></a>
          <a class="btn btn-outline-light btn-social rounded-circle" href="https://www.youtube.com/@AydınLanguageAcademy" target="_blank"><i class="fab fa-youtube"></i></a>
          <a class="btn btn-outline-light btn-social rounded-circle" href="https://wa.me/905326666549?text=Selamlar" target="_blank"><i
              class="bi bi-whatsapp"></i></a>
        </div>
      </div>
            <div class="col-lg-3 col-md-6">
        <h5 class="text-light mb-4">{{ __('dictt.address') }} - 2</h5>
        <p class="mb-2"><i class="fa fa-map-marker-alt me-3"></i>Atatürk Mahallesi Eski Postane Karşısı No:10
          Ortaca/Muğla</p>
        <p class="mb-2"><i class="fa fa-phone-alt me-3"></i>Ortaca: (546) 828 4884</p>
        <p class="mb-2"><i class="fa fa-envelope me-3"></i>learnenglishwithala@gmail.com</p>

        <div class="d-flex pt-2">
          <a class="btn btn-outline-light btn-social rounded-circle" href="https://www.instagram.com/aydindilakademisidalaman?igsh=MTVjaXl2eDJ2MjJwYg==" target="_blank"><i class="fab fa-instagram"></i></a>
          <a class="btn btn-outline-light btn-social rounded-circle" href="https://www.youtube.com/@AydınLanguageAcademy" target="_blank"><i class="fab fa-youtube"></i></a>
          <a class="btn btn-outline-light btn-social rounded-circle" href="https://wa.me/905326666549?text=Selamlar" target="_blank"><i
              class="bi bi-whatsapp"></i></a>
        </div>
      </div>
      <div class="col-lg-3 col-md-6">
        <h5 class="text-light mb-4">{{ __('dictt.links') }}</h5>
        <a class="btn btn-link" href="{{ Route('about') }}">{{ __('dictt.about') }}</a>
        <a class="btn btn-link" href="{{ Route('contact') }}">{{ __('dictt.contact') }}</a>
      </div>
    </div>
  </div>
  <div class="container">
    <div class="copyright">
      <div class="row">
        <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
          <a class="border-bottom" href="{{ route('home') }}">{{ __('dictt.ala') }}</a>, {{ __('dictt.allrightreserved.') }}
        </div>
        <div class="col-md-6 text-center text-md-end">
          <a class="border-none" href="{{ route('levels_list') }}" target="_blank">&copy; </a>
          Designed By <a class="border-bottom" href="https://www.madematik.com" target="_blank">mADEMatik</a>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- Footer End -->


<!-- Back to Top -->
<a href="#" class="btn btn-lg btn-primary btn-lg-square rounded-circle back-to-top"><i class="bi bi-arrow-up"></i></a>

<!-- JavaScript Libraries -->
<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('front/') }}/lib/waypoints/waypoints.min.js"></script>
<script src="{{ asset('front/') }}/lib/easing/easing.min.js"></script>
<script src="{{ asset('front/') }}/lib/wow/wow.min.js"></script>
<script src="{{ asset('front/') }}/lib/counterup/counterup.min.js"></script>
<script src="{{ asset('front/') }}/lib/owlcarousel/owl.carousel.min.js"></script>
<script src="{{ asset('front/') }}/lib/tempusdominus/js/moment.min.js"></script>
<script src="{{ asset('front/') }}/lib/tempusdominus/js/moment-timezone.min.js"></script>
<script src="{{ asset('front/') }}/lib/tempusdominus/js/tempusdominus-bootstrap-4.min.js"></script>

<!-- Template Javascript -->
<script src="{{ asset('front/') }}/js/main.js"></script>
</body>

</html>